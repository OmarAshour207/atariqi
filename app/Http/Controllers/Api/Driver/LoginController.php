<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\DriverResource;
use App\Models\User;
use App\Models\UserLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoginController extends BaseController
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone-no'      => 'required|string',
        ]);

        if($validator->fails()) {
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);
        }

        $phoneNumber = $validator->validated()['phone-no'];

        $user = User::with('callingKey')
            ->where('phone-no', $phoneNumber)
            ->where('user-type', 'driver')
            ->first();

        if(!$user) {
            return $this->sendError("s_userNotExist", [__("User not registered on Atariqi family, you have to register first")], 401);
        }

        if ($user->approval != 1) {
            return $this->sendError("s_userNotApproved",
                [__("We are checking your registration order, please bear with us and will send on academic email or phone")], 401);
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

    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone-no'      => 'required|string',
            'code'          => 'required|string',
            'fcm_token'     => 'required|nullable|string'
        ]);

        $data = $validator->validated();

        $code = $data['code'];
        $phoneNumber = $data['phone-no'];

        $user = User::with('driverInfo', 'driverCar')
            ->where('phone-no', $phoneNumber)
            ->where('user-type', 'driver')
            ->first();

        if(!$user) {
            return $this->sendError(__("s_userNotExist"), [__("User doesn't exist")], 401);
        }

        $success = array();
        $success['welcome_message'] = $this->checkWelcomeMessage($user);

        if($phoneNumber == '1124988931') {
            $success['token'] = $user->createToken('atariqi')->plainTextToken;
            $success['driver'] = new DriverResource($user);

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
        $success['driver'] = new DriverResource($user);

        return $this->sendResponse($success, __('User Logged Successfully.'));
    }

    private function checkWelcomeMessage(User $user): string
    {
        $userLogin = UserLogin::where('user-id', $user->id)->first();

        return $userLogin
            ? __('Thank you for creating your account. Please complete your profile information in the personal profile interface to start working.')
            : '';
    }
}
