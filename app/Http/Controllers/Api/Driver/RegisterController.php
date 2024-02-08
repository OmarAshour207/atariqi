<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\DriverCarResource;
use App\Http\Resources\DriverInfoResource;
use App\Models\DriverInfo;
use App\Models\DriversCar;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RegisterController extends BaseController
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user-first-name'   => 'required|string|max:20',
            'user-last-name'    => 'required|string|max:20',
            'phone-no'          => 'required|unique:users|max:20',
            'gender'            => 'required|string|max:20',
            'university-id'     => 'required|numeric',
            'email'             => 'required|email|unique:users|max:50',
            'approval'          => 'required|numeric',
            'user-type'         => 'required|string|in:driver',
            'driver-type-id'    => 'required|numeric',
            'call-key-id'       => 'required|numeric',
            'image'             => 'nullable|image|mimes:jpeg,jpg,png',
            'car-brand'         => 'required|string',
            'car-model'         => 'required|string',
            'car-letters'       => 'required|string',
            'car-color'         => 'required|string',
            'car-number'        => 'required|string',
            'license_img'       => 'required|image|mimes:jpeg,jpg,png',
            'car_form_img'      => 'required|image|mimes:jpeg,jpg,png',
            'car_front_img'     => 'required|image|mimes:jpeg,jpg,png',
            'car_back_img'      => 'required|image|mimes:jpeg,jpg,png',
            'car_rside_img'     => 'required|image|mimes:jpeg,jpg,png',
            'car_lside_img'     => 'required|image|mimes:jpeg,jpg,png',
            'car_insideFront_img'       => 'required|image|mimes:jpeg,jpg,png',
            'car_insideBack_img'        => 'required|image|mimes:jpeg,jpg,png',
        ]);

        if($validator->fails()) {
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);
        }
        $data = $validator->validated();

        $code = generateCode();
        $data['code'] = $code;

        try {
            DB::beginTransaction();
            $user = User::create($data);
            $images = $this->uploadImages($request, $data, $user->id);

            $driverInfo = DriverInfo::create([
                'driver-id'     => $user->id,
                'car-brand'     => $data['car-brand'],
                'car-model'     => $data['car-model'],
                'car-number'    => $data['car-number'],
                'car-letters'   => $data['car-letters'],
                'car-color'     => $data['car-color'],
                'driver-license-link' => $images['license_img']
            ]);

            $driverCar = DriversCar::create(array_merge(
                $images, [
                    'driver-id'     => $user->id,
                    'driver-type-id' => $data['driver-type-id'],
                ]
            ));

            DB::commit();
        } catch (\Exception $e) {
            Log::error("there is an error while storing user {$e->getMessage()}");
            DB::rollBack();
            return $this->sendError('s_unexpected_error', [__('Unexpected Error!')], 422);
        }

        $success['driver'] = $user;
        $success['driver_info'] = new DriverInfoResource($driverInfo);
        $success['driver_car'] = new DriverCarResource($driverCar);

        $phoneNumber = '+' . $user->callingKey->{"call-key"} . $user->{"phone-no"};
        sendSMS($phoneNumber, $code);

        return $this->sendResponse($success, __('Driver Registered Successfully.'));
    }

    private function uploadImages(Request $request, $data, $userId): array
    {
        $returnData = [];
        $path = public_path("uploads/$userId");
        if(!File::exists($path)) {
            File::makeDirectory($path, 777, true);
        }
        foreach ($data as $key => $image) {
            if ($request->hasFile($key)) {
                $extension = $request->{$key}->extension();
                $imageName = $key . '.' . $extension;
                $request->{$key}->move($path, $imageName);
//                $request->{$key}->storeAs("public/uploads/$userId", $imageName);
                $returnData[$key] = $imageName;
            }
        }

        return $returnData;
    }

}
