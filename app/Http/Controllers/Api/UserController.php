<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

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
            'user-type'         => 'required|string|max:50',
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

        $phoneNumber = $user->{"phone-no"};
        // check if phone number already starts with calling key
        if(!str_starts_with($user->{"phone-no"}, $user->callingKey->{"call-key"}))
            $phoneNumber = $user->callingKey->{"call-key"} . $phoneNumber;

        $this->sendSMS($phoneNumber, $code);

        return $this->sendResponse($success, __('User Registered Successfully.'));
    }

    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone-no'      => 'required|string',
        ]);

        $phoneNumber = $validator->validated()['phone-no'];
        if($validator->fails())
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);

        $user = User::with('callingKey')->where('phone-no', $phoneNumber)->first();
        if(!$user)
            return $this->sendError(__("User doesn't exist"), [__("User doesn't exist")], 401);

        $code = $this->generateCode();

        // check if phone number already starts with calling key
        if(!str_starts_with($user->{"phone-no"}, $user->callingKey->{"call-key"}))
            $phoneNumber = $user->callingKey->{"call-key"} . $phoneNumber;

        $this->sendSMS($phoneNumber, $code);

        $user->update(['code' => $code]);

        return $this->sendResponse(__('Verification code sent'), __('Verification code sent'));
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
            return $this->sendError(__("User doesn't exist"), [__("User doesn't exist")], 401);

        if($user->code != $code)
            return $this->sendError(__('Invalid Code'), [__('Invalid Code')], 401);

        $user->update([
            'code'      => null,
            'fcm_token' => $data['fcm_token']
        ]);

        $success['token'] = $user->createToken('atariqi')->plainTextToken;
        $success['user'] = $user;

        return $this->sendResponse($success, __('User Logged Successfully.'));
    }

    public function sendSMS($userNumber, $code)
    {
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
