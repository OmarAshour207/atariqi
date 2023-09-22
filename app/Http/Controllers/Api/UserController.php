<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\UserLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;
use Vonage\Client\Exception\Exception;

class UserController extends BaseController
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user-first-name'   => 'required|string|max:20',
            'user-last-name'    => 'required|string|max:20',
            'phone-no'          => 'required|unique:users|max:20',
            'gender'            => 'required|string|max:20',
            'university-id'     => 'required|numeric',
            'user-stage-id'     => 'required|numeric',
            'email'             => 'required|email|max:50',
            'approval'          => 'required|numeric',
            'user-type'         => 'required|string|in:passenger,driver',
            'call-key-id'       => 'required|numeric'
        ]);

        if($validator->fails())
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);

        $data = $validator->validated();
        $data['date-of-add'] = now();

        $code = $this->generateCode();
        $data['code'] = $code;

        $user = User::create($data);
        $success['user'] = $user;

        $phoneNumber = '+' . $user->callingKey->{"call-key"} . $user->{"phone-no"};

        $this->sendSMS($phoneNumber, $code);

        return $this->sendResponse($success, __('User Registered Successfully.'));
    }

    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone-no'      => 'required|string',
        ]);

        if($validator->fails())
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);

        $phoneNumber = $validator->validated()['phone-no'];
        $user = User::with('callingKey')->where('phone-no', $phoneNumber)->first();

        if(!$user)
            return $this->sendError("s_userNotExist", [__("User doesn't exist")], 401);

        $code = $this->generateCode();

        $phoneNumber = '+' . $user->callingKey->{"call-key"} . $phoneNumber;

        $response = $this->sendSMS($phoneNumber, $code);
        if(!$response)
            return $this->sendError('s_unexpected_error', [__('Unexpected Error!')], 422);

        $user->update(['code' => $code]);

        return $this->sendResponse('s_codeSent', __('Verification code sent'));
    }

    public function verifyCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone-no'      => 'required|string',
            'code'          => 'required|string',
            'fcm_token'     => 'required|nullable|string'
        ]);

        $data = $validator->validated();

        $code = $data['code'];
        $phoneNumber = $data['phone-no'];

        $user = User::where('phone-no', $phoneNumber)->first();

        if(!$user)
            return $this->sendError(__("s_userNotExist"), [__("User doesn't exist")], 401);

        if($user->code != $code)
            return $this->sendError(__('s_invalidCode'), [__('Invalid Code')], 401);

        $user->update([
            'code'      => null,
            'fcm_token' => $data['fcm_token']
        ]);

        UserLogin::create([
            'user-id'       => $user->id,
            'date-time'     => now(),
            'login-logout'  => 1
        ]);

        $success['token'] = $user->createToken('atariqi')->plainTextToken;
        $success['user'] = new UserResource($user);

        return $this->sendResponse($success, __('User Logged Successfully.'));
    }

    public function sendSMS($userNumber, $code)
    {
        $provider = 'msegat';
        if ($provider == 'twilio') {
            $accountSID = config('services.twilio.account_sid');
            $token = config('services.twilio.auth_token');
            $twilioPhoneNumber = config('services.twilio.phone_number');

            $client = new Client($accountSID, $token);

            try {
                $client->messages->create(
                    $userNumber, [
                        'from'      => $twilioPhoneNumber,
                        'body'      => __('Your Atariqi verification code is: ') . $code
                    ]
                );
            } catch (TwilioException $e) {
                Log::error($e->getMessage());
            }
        }
        if ($provider == 'nexmo') {

            $key = config('services.nexmo.key');
            $secret = config('services.nexmo.secret');

            $basic  = new \Vonage\Client\Credentials\Basic($key, $secret);
            $client = new \Vonage\Client($basic);

            try {
                $response = $client->sms()->send(
                    new \Vonage\SMS\Message\SMS($userNumber, __('Atariqi'), (__('Your Atariqi verification code is: ') . $code))
                );

                $message = $response->current();
                if ($message->getStatus() == 0) {
                    Log::info("The message was sent successfully");
                } else {
                    Log::error("The message failed with status: " . $message->getStatus());
                }

            } catch (Exception $e) {
                Log::error($e->getMessage());
            }
        }
        if ($provider == 'msegat') {
            $config = array();
            $config['userName'] = config('services.msegat.user_name');
            $config['numbers'] = $userNumber;
            $config['userSender'] = config('services.msegat.user_sender');
            $config['apiKey'] = config('services.msegat.api_key');
            $config['msg'] = __('Verification Code: ') . $code;
            
            try {
                $codes = [1, 'M0000'];
                $response = Http::post('https://www.msegat.com/gw/sendsms.php', $config);
                $response = $response->body();
                $response = json_decode($response, true);
                $messageCode = $response['code'];
                if (!in_array($messageCode, $codes)) {
                    Log::error("Msegat error API: " . $response['message']);
                    return false;
                }
            } catch (Exception $e) {
                Log::error($e->getMessage());
                return false;
            }

            return true;
        }
    }

    public function generateCode()
    {
        $code = mt_rand(1000, 9999);
        while (true) {
            $user = User::where('code', $code)->first();
            if(!$user)
                break;
            $code = mt_rand(1000, 9999);
        }

        return $code;
    }
}
