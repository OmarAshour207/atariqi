<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\GuardsOtpSending;
use App\Http\Resources\UserResource;
use App\Models\Neighbour;
use App\Models\NewUserInfo;
use App\Models\PassengerBanned;
use App\Models\University;
use App\Models\User;
use App\Models\UserLogin;
use App\Rules\SaudiCallingKey;
use App\Rules\SaudiMobileNumber;
use App\Rules\UniquePhoneNumberForUserType;
use App\Services\OtpRateLimiter;
use App\Support\OtpBypass;
use App\Support\SaudiPhone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends BaseController
{
    use GuardsOtpSending;

    public function __construct(private OtpRateLimiter $otpRateLimiter)
    {
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user-first-name'   => 'required|string|max:20',
            'user-last-name'    => 'required|string|max:20',
            'phone-no'          => ['required', 'max:20', new SaudiMobileNumber(), new UniquePhoneNumberForUserType($request->input("user-type"))],
            'gender'            => 'required|string|max:20',
            'university-id'     => 'required|numeric',
            'user-stage-id'     => 'required|numeric',
            'email'             => 'required|email|max:50',
            'approval'          => 'required|numeric',
            'user-type'         => 'required|string|in:passenger',
            'call-key-id'       => ['required', 'numeric', new SaudiCallingKey()],
        ]);

        if($validator->fails()) {
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);
        }

        $passengerBanned = PassengerBanned::where('passenger_no', $request->input('phone-no'))->first();

        if($passengerBanned) {
            return $this->sendError(__('s_userBanned'), [__('This user is banned')], 403);
        }

        $data = $validator->validated();
        $data['date-of-add'] = now();

        if ($response = $this->guardOtpForRegistration((int) $data['call-key-id'], $data['phone-no'], $this->otpRateLimiter)) {
            return $response;
        }

        $code = resolve_otp_code($data['phone-no']);
        $data['code'] = $code;

        $user = User::create($data);
        $success['user'] = $user;

        if (! deliver_otp_code($user, $code, $data['phone-no'])) {
            return $this->rejectNonSaudiPhone();
        }

        return $this->sendResponse($success, __('User Registered Successfully.'));
    }

    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone-no'      => ['required', 'string', new SaudiMobileNumber()],
        ]);

        if($validator->fails()) {
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);
        }

        $passengerBanned = PassengerBanned::where('passenger_no', $request->input('phone-no'))->first();

        if($passengerBanned) {
            return $this->sendError(__('s_userBanned'), [__('This user is banned')], 403);
        }

        $phoneNumber = $validator->validated()['phone-no'];
        $user = User::with('callingKey')
            ->where('phone-no', $phoneNumber)
            ->where('user-type', 'passenger')
            ->first();

        if(!$user) {
            return $this->sendError("s_userNotExist", [__("User doesn't exist")], 401);
        }

        if ($response = $this->guardOtpForUser($user, $this->otpRateLimiter)) {
            return $response;
        }

        $code = resolve_otp_code($phoneNumber);

        if (! deliver_otp_code($user, $code, $phoneNumber)) {
            return $this->sendError('s_unexpected_error', [__('Unexpected Error!')], 422);
        }

        $user->update(['code' => $code]);

        return $this->sendResponse('s_codeSent', __('Verification code sent'));
    }

    public function verifyCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone-no'      => ['required', 'string', new SaudiMobileNumber()],
            'code'          => 'required|string',
            'fcm_token'     => 'required|nullable|string'
        ]);

        $data = $validator->validated();

        $code = $data['code'];
        $phoneNumber = $data['phone-no'];

        $user = User::where('phone-no', $phoneNumber)
            ->where('user-type', 'passenger')
            ->first();

        if(!$user) {
            return $this->sendError(__("s_userNotExist"), [__("User doesn't exist")], 401);
        }

        if (! OtpBypass::isBypassPhone($phoneNumber) && ! SaudiPhone::resolveForUser($user->loadMissing('callingKey'))) {
            return $this->rejectNonSaudiPhone();
        }

        if($user->code != $code) {
            return $this->sendError(__('s_invalidCode'), [__('Invalid Code')], 401);
        }

        $user->update([
            'code'      => null,
            'fcm_token' => $data['fcm_token']
        ]);

        $success['user'] = new UserResource($user);
        $success['token'] = $user->createToken('atariqi')->plainTextToken;

        UserLogin::create([
            'user-id'       => $user->id,
            'date-time'     => now(),
            'login-logout'  => 1
        ]);

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
            'call-key-id'       => 'required|numeric',
            'image'             => 'nullable|image|mimes:jpeg,jpg,png',
        ]);

        if($validator->fails())
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);

        $user = auth()->user();
        $data = merge_pending_user_profile_data($validator->validated(), $user);

        if ($request->hasFile('image')) {
            $data['image'] = store_user_upload($request->file('image'), $user->id, 'image');
        }

        NewUserInfo::updateOrCreate(
            ['user-id' => $user->id],
            $data
        );

        $user->update([
            'approval'  => 2
        ]);

        return $this->sendResponse([], __('The order under processing and will touch with you soon.'));
    }
}
