<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Api\BaseController;
use App\Models\NewDriverCar;
use App\Models\NewDriverInfo;
use App\Models\NewUserInfo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProfileController extends BaseController
{
    public function updateGeneral(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user-first-name'   => 'nullable|string|max:20',
            'user-last-name'    => 'nullable|string|max:20',
            'phone-no'          => ['nullable', 'max:20', Rule::unique('users')->ignore(auth()->user()->id)],
            'gender'            => 'nullable|string|max:20',
            'university-id'     => 'nullable|numeric',
            'call-key-id'       => 'nullable|numeric',
            'user-stage-id'     => 'nullable|numeric',
            'user-type'         => 'nullable|string|in:driver',
            'email'             => ["nullable", "email", "max:50", Rule::unique('users')->ignore(auth()->user()->id)],
            'image'             => 'nullable|mimes:jpeg,jpg,png',
        ]);

        if($validator->fails()) {
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);
        }

        $data = $validator->validated();
        $data['user-id'] = auth()->user()->id;

        NewUserInfo::create($data);
        auth()->user()->update(['approval' => 2]);

        return $this->sendResponse([],
            __('Your request for edit will be reviewed, and we will respond to you as soon as possible'));
    }

    public function updateCar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'car_front_img'         => 'nullable|image|mimes:jpeg,jpg,png',
            'car_back_img'          => 'nullable|image|mimes:jpeg,jpg,png',
            'car_rside_img'         => 'nullable|image|mimes:jpeg,jpg,png',
            'car_lside_img'         => 'nullable|image|mimes:jpeg,jpg,png',
            'car_insideFront_img'   => 'nullable|image|mimes:jpeg,jpg,png',
            'car_insideBack_img'    => 'nullable|image|mimes:jpeg,jpg,png',
        ]);

        $data = $validator->validated();
        $data['driver-id'] = auth()->user()->id;

        $images = $this->uploadImages($request, $data);

        NewDriverCar::create(array_merge(
            $data, $images
        ));

        auth()->user()->driverCar->update([
            'approval'  => 2
        ]);

        return $this->sendResponse([],
            __('Your request for edit will be reviewed, and we will respond to you as soon as possible'));
    }

    public function updateInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'car-brand'         => 'required|string',
            'car-model'         => 'required|numeric',
            'car-letters'       => 'required|string',
            'car-color'         => 'required|string',
            'car-number'        => 'required|numeric',
            'driver-type-id'    => 'required|numeric',
            'license_img'       => 'nullable|mimes:jpeg,jpg,png',
            'car_form_img'      => 'nullable|mimes:jpeg,jpg,png',
        ]);

        $data = $validator->validated();
        $data['driver-id'] = auth()->user()->id;

        $images = $this->uploadImages($request, $data);

        if(isset($images['license_img'])) {
            $data['driver-license-link'] = $images['license_img'];
        }

        NewDriverInfo::create(array_merge(
            $data, $images
        ));
        NewDriverCar::create(array_merge(
            $data, $images
        ));

        auth()->user()->driverInfo->update([
            'approval'  => 2
        ]);

        auth()->user()->driverCar->update([
            'approval'  => 2
        ]);

        return $this->sendResponse([],
            __('Your request for edit will be reviewed, and we will respond to you as soon as possible'));
    }

    private function uploadImages(Request $request, $data): array
    {
        $returnData = [];

        $path = public_path("uploads/" . auth()->user()->id);
        if(!File::exists($path)) {
            File::makeDirectory($path, 0777, true);
        }

        foreach ($data as $key => $image) {
            if ($request->hasFile($key)) {
                $extension = $request->{$key}->extension();
                $imageName = $key . '_' . strtotime(now()) . '.' . $extension;
                $request->{$key}->move($path, $imageName);
                $returnData[$key] = $imageName;

//                $this->removeOldImage($key);
            }
        }

        return $returnData;
    }

    private function removeOldImage($key)
    {
        $path = public_path("uploads/" . auth()->user()->id);
        $imagePath = auth()->user()->driverCar?->{"$key"};

        if($key == 'license_img') {
            $imagePath =  $path . "/" . auth()->user()->driverInfo->{"driver-license-link"};
        }

        File::delete($imagePath);
    }
}
