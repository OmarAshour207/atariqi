<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\DriverCarResource;
use App\Http\Resources\DriverInfoResource;
use App\Models\DriverInfo;
use App\Models\DriversCar;
use App\Models\Package;
use App\Models\User;
use App\Models\UserPackage;
use App\Rules\UniquePhoneNumberForUserType;
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
            'phone-no'          => ['required', 'max:20', new UniquePhoneNumberForUserType($request->input("user-type"))],
            'gender'            => 'required|string|max:20',
            'university-id'     => 'required|numeric',
            'email'             => 'required|email|unique:users|max:50',
            'user-type'         => 'required|string|in:driver',
            'driver-type-id'    => 'required|numeric',
            'call-key-id'       => 'required|numeric',
            'user-stage-id'     => 'required|numeric',
            'image'             => 'nullable|mimes:jpeg,jpg,png',
            'car-brand'         => 'required|string',
            'car-model'         => 'required|numeric',
            'car-letters'       => 'required|string',
            'car-color'         => 'required|string',
            'car-number'        => 'required|numeric',
            'license_img'       => 'required|mimes:jpeg,jpg,png',

            'identity_number'   => 'required|string|unique:driver-info,identity_number',
            'date_of_birth'       => 'nullable|string',
            'date_of_birth_hijri' => 'nullable|string',

            'car_form_img'      => 'required|mimes:jpeg,jpg,png',
            'car_front_img'     => 'required|mimes:jpeg,jpg,png',
            'car_back_img'      => 'required|mimes:jpeg,jpg,png',
            'car_rside_img'     => 'required|mimes:jpeg,jpg,png',
            'car_lside_img'     => 'required|mimes:jpeg,jpg,png',
            'car_insideFront_img'       => 'required|image|mimes:jpeg,jpg,png',
            'car_insideBack_img'        => 'required|image|mimes:jpeg,jpg,png',
        ]);

        if($validator->fails()) {
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);
        }
        $data = $validator->validated();
        $data['date_of_birth'] = isset($data['date_of_birth']) ? convertArabicDateToEnglish($data['date_of_birth']) : null;
        $data['date_of_birth_hijri'] = isset($data['date_of_birth_hijri']) ? convertArabicDateToEnglish($data['date_of_birth_hijri']) : null;

        $data['approval'] = 0;

        try {
            DB::beginTransaction();
            $user = User::create($data);
            $images = $this->uploadImages($request, $data, $user->id);
            if(isset($images['image'])) {
                $user->update([
                    'image'     => $images['image']
                ]);
            }

            $driverInfo = DriverInfo::create([
                'driver-id'     => $user->id,
                'car-brand'     => $data['car-brand'],
                'car-model'     => $data['car-model'],
                'car-number'    => $data['car-number'],
                'car-letters'   => $data['car-letters'],
                'car-color'     => $data['car-color'],
                'driver-license-link' => $images['license_img'],
                'identity_number'   => $data['identity_number'],
                'date_of_birth'       => $data['date_of_birth'] ?? null,
                'date_of_birth_hijri' => $data['date_of_birth_hijri'] ?? null,
            ]);

            $driverCar = DriversCar::create(array_merge(
                $images, [
                    'driver-id'     => $user->id,
                    'driver-type-id' => $data['driver-type-id'],
                ]
            ));

            $freePackage = Package::where('price_monthly', 0)
                ->where('status', Package::FREE)
                ->first();

            UserPackage::create([
                'user_id'       => $user->id,
                'package_id'    => $freePackage->id,
                'start_date'    => now(),
                'end_date'      => now()->addYear(),
                'status'        => UserPackage::STATUS_ACTIVE,
                'interval'     => 'yearly'
            ]);

            DB::commit();
        } catch (\Exception $e) {
            Log::error("there is an error while storing user {$e->getMessage()}");
            DB::rollBack();
            return $this->sendError('s_unexpected_error', [__('Unexpected Error!')], 422);
        }

        $success['driver'] = $user;
        $success['driver_info'] = new DriverInfoResource($driverInfo);
        $success['driver_car'] = new DriverCarResource($driverCar);

        return $this->sendResponse($success,
            __('We are checking your registration order, please bear with us and will send on academic email or phone'));
    }

    private function uploadImages(Request $request, $data, $userId): array
    {
        $returnData = [];
        $path = public_path("uploads/$userId");
        if(!File::exists($path)) {
            File::makeDirectory($path, 0777, true);
        }
        foreach ($data as $key => $image) {
            if ($request->hasFile($key)) {
                $extension = $request->{$key}->extension();
                $imageName = $key . '_' . strtotime(now()) . '.' . $extension;
                $request->{$key}->move($path, $imageName);
                $returnData[$key] = $imageName;
            }
        }

        return $returnData;
    }

}
