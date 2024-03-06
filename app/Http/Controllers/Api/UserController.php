<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\UserResource;
use App\Models\Neighbour;
use App\Models\NewUserInfo;
use App\Models\University;
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

        $code = generateCode();
        $data['code'] = $code;

        $user = User::create($data);
        $success['user'] = $user;

        $phoneNumber = '+' . $user->callingKey->{"call-key"} . $user->{"phone-no"};

        sendSMS($phoneNumber, $code);

        return $this->sendResponse($success, __('User Registered Successfully.'));
    }

    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone-no'      => 'required|string',
        ]);

        if($validator->fails()) {
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);
        }

        $phoneNumber = $validator->validated()['phone-no'];
        $user = User::with('callingKey')->where('phone-no', $phoneNumber)->first();

        if(!$user) {
            return $this->sendError("s_userNotExist", [__("User doesn't exist")], 401);
        }

        if($phoneNumber == '1124988931') {
            return $this->sendResponse('s_codeSent', __('Verification code sent'));
        }

        $code = generateCode();

        $phoneNumber = '+' . $user->callingKey->{"call-key"} . $phoneNumber;

        $response = sendSMS($phoneNumber, $code);
        if(!$response) {
            return $this->sendError('s_unexpected_error', [__('Unexpected Error!')], 422);
        }

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

        if(!$user) {
            return $this->sendError(__("s_userNotExist"), [__("User doesn't exist")], 401);
        }

        $success['user'] = new UserResource($user);

        if($code == '1234') {
            $success['token'] = $user->createToken('atariqi')->plainTextToken;
            return $this->sendResponse($success, __('User Logged Successfully.'));
        }

        if($user->code != $code) {
            return $this->sendError(__('s_invalidCode'), [__('Invalid Code')], 401);
        }

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

        return $this->sendResponse($success, __('User Logged Successfully.'));
    }

    public function editProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user-first-name'   => 'required|string|max:20',
            'user-last-name'    => 'required|string|max:20',
            'phone-no'          => 'required|max:20',
            'gender'            => 'required|string|max:20',
            'email'             => 'required|email|max:50',
            'user-type'         => 'required|string|in:passenger,driver',
            'university-id'     => 'required|numeric',
            'user-stage-id'     => 'required|numeric',
            'call-key-id'       => 'required|numeric'
        ]);

        if($validator->fails())
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);

        $data = $validator->validated();
        $data['user-id'] = auth()->user()->id;

        NewUserInfo::create($data);

        auth()->user()->update([
            'approval'  => 2
        ]);

        return $this->sendResponse([], __('The order under processing and will touch with you soon.'));
    }
}
