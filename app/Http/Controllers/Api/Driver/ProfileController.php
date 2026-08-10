<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\NeighbourResource;
use App\Http\Resources\ServiceResource;
use App\Models\DriverNeighborhood;
use App\Models\DriverSchedule;
use App\Models\DriversServices;
use App\Models\NewDriverCar;
use App\Models\NewDriverInfo;
use App\Models\NewUserInfo;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'image'             => 'nullable|image|mimes:jpeg,jpg,png',
        ]);

        if($validator->fails()) {
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);
        }

        $driver = auth()->user();
        $data = merge_pending_user_profile_data($validator->validated(), $driver);

        if ($request->hasFile('image')) {
            $data['image'] = store_user_upload($request->file('image'), $driver->id, 'image');
        }

        NewUserInfo::updateOrCreate(
            ['user-id' => $driver->id],
            $data
        );
        auth()->user()->update(['approval' => 2]);

        return $this->sendResponse([],
            __('Your request for edit will be reviewed, and we will respond to you as soon as possible'));
    }

    public function updateCar(Request $request)
    {
        $fileFields = [
            'car_front_img',
            'car_back_img',
            'car_rside_img',
            'car_lside_img',
            'car_insideFront_img',
            'car_insideBack_img',
            'car_form_img',
        ];

        $rules = [];
        foreach ($fileFields as $field) {
            $rules[$field] = 'nullable|image|mimes:jpeg,jpg,png';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);
        }

        $driver = auth()->user();
        $images = $this->uploadImages($request, $fileFields, $driver->id);

        if (empty($images)) {
            return $this->sendError(__('Validation Error.'), [
                'images' => [__('Please upload at least one car image.')],
            ], 422);
        }

        $existingRequest = NewDriverCar::where('driver-id', $driver->id)->latest('id')->first();
        $currentCar = $driver->driverCar;

        $payload = [
            'driver-id' => $driver->id,
            'driver-type-id' => $existingRequest?->{'driver-type-id'}
                ?? $currentCar?->{'driver-type-id'},
            'date_of_add' => now()->toDateTimeString(),
        ];

        foreach ($fileFields as $field) {
            $payload[$field] = $images[$field]
                ?? $existingRequest?->{$field}
                ?? $currentCar?->{$field};
        }

        if ($request->hasFile('license_img')) {
            $licenseImages = $this->uploadImages($request, ['license_img'], $driver->id);
            if (!empty($licenseImages['license_img'])) {
                $payload['license_img'] = $licenseImages['license_img'];
            }
        } else {
            $payload['license_img'] = $existingRequest?->license_img
                ?? $currentCar?->license_img;
        }

        if ($existingRequest) {
            $existingRequest->update($payload);
            NewDriverCar::where('driver-id', $driver->id)
                ->where('id', '!=', $existingRequest->id)
                ->delete();
        } else {
            NewDriverCar::create($payload);
        }

        $this->ensureNewUserInfoExists();

        $driver->driverCar?->update([
            'approval' => 2,
        ]);

        $driver->update(['approval' => 2]);

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
            'sequence-number'    => 'required|numeric',
            'driver-type-id'    => 'required|numeric',
            'license_img'       => 'nullable|mimes:jpeg,jpg,png',
        ]);

        $data = $validator->validated();
        $data['driver-id'] = auth()->user()->id;

        $images = $this->uploadImages($request, ['license_img'], auth()->user()->id);

        if (isset($images['license_img'])) {
            $data['driver-license-link'] = $images['license_img'];
        } elseif (auth()->user()->driverInfo?->{'driver-license-link'}) {
            $data['driver-license-link'] = auth()->user()->driverInfo->{'driver-license-link'};
        }

        unset($data['license_img']);

        NewDriverInfo::updateOrCreate(
            ['driver-id' => auth()->user()->id],
            $data
        );

        $this->ensureNewUserInfoExists();

        auth()->user()->driverInfo->update([
            'approval'  => 2
        ]);

        auth()->user()->update(['approval' => 2]);

        return $this->sendResponse([],
            __('Your request for edit will be reviewed, and we will respond to you as soon as possible'));
    }

    private function ensureNewUserInfoExists(): void
    {
        $driver = auth()->user();

        if (NewUserInfo::where('user-id', $driver->id)->exists()) {
            return;
        }

        NewUserInfo::create([
            'user-id' => $driver->id,
            'user-first-name' => $driver->{'user-first-name'},
            'user-last-name' => $driver->{'user-last-name'},
            'phone-no' => $driver->{'phone-no'},
            'gender' => $driver->gender,
            'email' => $driver->email,
            'user-type' => $driver->{'user-type'},
            'image' => $driver->image,
            'call-key-id' => $driver->{'call-key-id'},
            'user-stage-id' => $driver->{'user-stage-id'},
            'university-id' => $driver->{'university-id'},
        ]);
    }

    private function uploadImages(Request $request, array $fields, ?int $userId = null): array
    {
        $returnData = [];
        $userId = $userId ?? auth()->user()->id;

        $path = public_path("uploads/{$userId}");
        if (!File::exists($path)) {
            File::makeDirectory($path, 0777, true);
        }

        foreach ($fields as $key) {
            if (!$request->hasFile($key)) {
                continue;
            }

            $file = $request->file($key);
            if (!$file->isValid()) {
                continue;
            }

            $extension = $file->extension();
            $imageName = $key . '_' . time() . '_' . uniqid() . '.' . $extension;
            $file->move($path, $imageName);
            $returnData[$key] = $imageName;
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

    public function updateTransport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'neighborhood_to'       => [Rule::requiredIf(empty($request->neighborhood_from)), 'string'],
            'neighborhood_from'     => [Rule::requiredIf(empty($request->neighborhood_to)), 'string'],
            'times.*'               => 'required',
            'allow-disabilities'    => 'required|string|in:yes,no',
            'services.*'            => 'required|numeric'
        ]);

        if($validator->fails()) {
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);
        }

        $data = $validator->validated();
        $data['neighborhood_to'] = json_decode($request->neighborhood_to, true);
        $data['neighborhood_from'] = json_decode($request->neighborhood_from, true);
        $data['services'] = json_decode($request->services, true);

        DriverNeighborhood::updateOrCreate([
            'driver-id' => auth()->user()->id
        ], [
            'neighborhoods-to'   => implode('|', $data['neighborhood_to']),
            'neighborhoods-from' => implode('|', $data['neighborhood_from']),
        ]);

        auth()->user()->driverInfo->update([
            'allow-disabilities' => $request->{"allow-disabilities"}
        ]);

        $this->saveServices($data['services']);

        $this->saveSchedule($data['times']);

        return $this->sendResponse([], __('Success'));
    }

    private function mapDays($dayName)
    {
        $daysOfWeek = [
            'الأحد'      => 'Sunday',
            'الاثنين'    => 'Monday',
            'الثلاثاء'   => 'Tuesday',
            'الأربعاء'   => 'Wednesday',
            'الخميس'    => 'Thursday',
            'الجمعة'    => 'Friday',
            'السبت'     => 'Saturday',
        ];

        if (key_exists($dayName, $daysOfWeek)) {
            return $daysOfWeek[$dayName];
        }
        return $dayName;
    }

    private function saveServices($services)
    {
        if(!count($services)) {
            return;
        }

        DriversServices::whereNotIn('service-id', $services)
            ->where('driver-id', auth()->user()->id)
            ->delete();

        for ($i = 0;$i < count($services); $i++) {
            DriversServices::updateOrCreate([
                'driver-id'     => auth()->user()->id,
                'service-id'    => $services[$i]
            ], [
                'date-of-add'   => Carbon::now()
            ]);
        }
    }

    private function saveSchedule($times)
    {
        $schedule = array();

        foreach ($times as $time) {
            $dayName = $this->mapDays($time['day']);
            $schedule["$dayName-to"] = $time['time_go'] ? convertArabicDateToEnglish($time['time_go']) : NULL;
            $schedule["$dayName-from"] = $time['time_back'] ? convertArabicDateToEnglish($time['time_back']) : NULL;
        }

        DriverSchedule::updateOrCreate([
            'driver-id' => auth()->user()->id
        ], $schedule);
    }

    public function getTransportData()
    {
        $user = auth()->user()->load(['driverService', 'driverSchedule', 'driverNeighborhood', 'driverInfo']);
        $services = Service::all();
        $neighborhoods = DB::table('neighborhoods')->where('city_id', function ($query) use ($user) {
            $query->select('city_id')
                ->from('university')
                ->where('id', $user->{"university-id"});
        })->get();

        $driverNeighborhoods = $user->driverNeighborhood;

        $success = array();
        $success['neighborhoods'] = NeighbourResource::collection($neighborhoods);

        $success['neighborhoods-to'] = $driverNeighborhoods && $driverNeighborhoods->{"neighborhoods-to"} ? explode('|', $driverNeighborhoods->{"neighborhoods-to"}) : [];
        $success['neighborhoods-from'] = $driverNeighborhoods && $driverNeighborhoods->{"neighborhoods-from"} ? explode('|', $driverNeighborhoods->{"neighborhoods-from"}) : [];

        $success['driver-schedule'] = $user->driverSchedule;
        $success['services'] = ServiceResource::collection($services);

        $success['driver-services'] = $user->driverService->pluck('service-id')->toArray();

        $success['allow-disabilities'] = $user->driverInfo->{"allow-disabilities"};

        return $this->sendResponse($success, __('Data'));
    }
}
