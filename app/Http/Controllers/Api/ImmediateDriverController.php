<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\DriverInfoResource;
use App\Http\Resources\RideBookingResource;
use App\Models\DriverInfo;
use App\Models\DriverSchedule;
use App\Models\DriversServices;
use App\Models\Neighbour;
use App\Models\RideBooking;
use App\Models\Service;
use App\Models\SuggestionDriver;
use App\Models\University;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ImmediateDriverController extends BaseController
{
    public function getService($id)
    {
        return Service::whereId($id)->first();
    }

    public function getDrivers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'neighborhood_id'   => 'required|numeric',
            'university_id'     => 'required|numeric',
            'ride_type_id'      => 'required|numeric',
            'lat'               => 'required|string',
            'lng'               => 'required|string',
            'locale'            => 'sometimes|nullable|string'
        ]);

        if($validator->fails()) {
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);
        }

        $data = $validator->validated();

        $data['now_day'] = Carbon::now()->format('l');

        $passengerId = auth()->user()->id;
        $rideTypeId = $data['ride_type_id'];
        $universityId = $data['university_id'];
        $neighborhoodId = $data['neighborhood_id'];
        $lat = $data['lat'];
        $lng = $data['lng'];
        $service = $this->getService($rideTypeId);
        $roadWay = $service->{"road-way"};

        $success = array();
        $success['drivers'] = [];
        $success['to'] = null;
        $success['from'] = null;
        $success['estimated_time'] = null;
        $success['action'] = 'immediate/transport/trips';

        $drivers = User::select('users.id as driver_id')
            ->join('university', 'users.university-id', '=', 'university.id')
            ->join('uni-driving-services', function ($join) use ($rideTypeId, $universityId) {
                $join->on('uni-driving-services.university-id', '=', 'users.university-id')
                    ->where('uni-driving-services.service-id', '=', $rideTypeId);
            })
            ->where('users.user-type', '=', 'driver')
            ->where('users.university-id', '=', $universityId)
            ->get()
            ->toArray();

        $savingData = [
            'passenger-id'      => $passengerId,
            'university-id'     => $universityId,
            'neighborhood-id'   => $neighborhoodId,
            'lat'               => $lat,
            'lng'               => $lng,
            'service-id'        => $rideTypeId,
            'road-way'          => $roadWay,
            'action'            => 1,
            'date-of-add'       => Carbon::now()
        ];

        if(!count($drivers)) {
            $success['trip'] = RideBooking::create($savingData);
            return $this->sendResponse($success, __('Drivers'));
        }

        $driverIds = array();
        foreach ($drivers as $driver) {
            $driverIds[] = $driver['driver_id'];
        }

        $foundDrivers = DriversServices::select('drivers-services.driver-id AS Found-driver-id')
            ->join('drivers-neighborhoods', function ($join) use ($neighborhoodId, $roadWay) {
                $join->on('drivers-neighborhoods.driver-id', '=', 'drivers-services.driver-id')
                    ->where("drivers-neighborhoods.neighborhoods-$roadWay", 'LIKE', "%$neighborhoodId%");
            })
            ->whereIn('drivers-services.driver-id', $driverIds)
            ->where('drivers-services.service-id', '=', $rideTypeId)
            ->get()
            ->toArray();

        if(!count($foundDrivers)) {
            $success['trip'] = RideBooking::create(array_merge($savingData, ['action' => 2]));
            return $this->sendResponse($success, __('Found Drivers'));
        }

        $foundDriverId = array();
        foreach ($foundDrivers as $foundDriver) {
            $foundDriverId[] = $foundDriver['Found-driver-id'];
        }

        $timeAfterHour = Carbon::now()->addHour()->format('H') == 00 ? '24:00:00' : Carbon::now()->addHour()->format('H:i:s');

        $suggestDriverId = DB::table('drivers-schedule')
            ->select('driver-id AS suggest-driver-id')
//            ->when($roadWay == 'to', function ($query) use ($nowDay, $roadWay) {
//                $query->where("$nowDay-$roadWay", '>=', Carbon::now()->subMinutes(15)->format('H:i:s'));
//                $query->where("$nowDay-$roadWay", '<=', Carbon::now()->addHour()->format("H:i:s"));
//            })
//            ->when($roadWay == 'from', function ($query) use ($nowDay, $roadWay, $timeAfterHour) {
//                $query->where("$nowDay-$roadWay", '<=', $timeAfterHour);
//                $query->where("$nowDay-$roadWay", '>=', Carbon::now()->subMinutes(15)->format("H:i:s"));
//            })
            ->whereIn('driver-id', $foundDriverId)
            ->get();

        if(count($suggestDriverId) < 1) {
            $success['trip'] = RideBooking::create(array_merge($savingData, ['action' => 3]));
            return $this->sendResponse($success, __('No Drivers!'));
        }

        $rideBooking = RideBooking::create(array_merge($savingData, ['action' => 0]));

        // TODO:: Check in suggestions-drivers table the drivers with action = 2 and exclude them
        // TODO:: filter using gender also

//        $finalDriversId = array();

        foreach ($suggestDriverId as $driver) {
//            $finalDriversId[] = $driver->{"suggest-driver-id"};
            SuggestionDriver::create([
                'booking-id'    => $rideBooking->id,
                'passenger-id'  => $passengerId,
                'driver-id'     => $driver->{"suggest-driver-id"},
                'action'        => 0,
                'date-of-add'   => Carbon::now()
            ]);
        }

//        $drivers = DriverInfo::with('driver')
//            ->whereIn('driver-id', $finalDriversId)
//            ->get();

        $success['drivers'] = [];//DriverInfoResource::collection($drivers);

        $neighborhood = Neighbour::findOrFail($neighborhoodId);
        $university = University::whereId($universityId)->first();
        $from = array();
        $to = array();


        $to['ar'] = $roadWay == 'from' ? $neighborhood->{"neighborhood-ar"} : $university->{"name-ar"};
        $to['en'] = $roadWay == 'from' ? $neighborhood->{"neighborhood-eng"} : $university->{"name-eng"};
        $from['ar'] = $roadWay == 'from' ? $university->{"name-ar"} : $neighborhood->{"neighborhood-ar"};
        $from['en'] = $roadWay == 'from' ? $university->{"name-eng"} : $neighborhood->{"neighborhood-eng"};
        $success['destination_lat'] = $roadWay == 'from' ? $lat : $university->lat;
        $success['destination_lng'] = $roadWay == 'from' ? $lng : $university->lng;

        $success['to'] = $to;
        $success['from'] = $from;
        $success['estimated_time'] = 15;
        $success['trip'] = $rideBooking;

        return $this->sendResponse($success, __('Drivers'));

    }

    public function execute(Request $request)
    {
        Log::info("Execute ride with ID: " . $request->input('booking_id'));
        $validator = Validator::make($request->all(), [
            'booking_id'   => 'required|numeric',
        ]);

        if($validator->fails()) {
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);
        }

        $success = array();
        $success['drivers'] = [];
        $success['trip'] = [];
        $success['to'] = null;
        $success['from'] = null;
        $success['estimated_time'] = null;
        $success['action'] = 'immediate/transport/trips';

        $trip = RideBooking::with('university', 'neighborhood')
            ->where('id', $request->input('booking_id'))
            ->first();

        if (!$trip) {
            return $this->sendError(__('Trip not found!'), [__('Trip not found!')]);
        }

        $success['trip'] = new RideBookingResource($trip);

        $suggestedDriver = SuggestionDriver::with('booking', 'driver', 'driverinfo', 'deliveryInfo')
            ->where('passenger-id', auth()->user()->id)
            ->whereIn('action', [1, 2])
            ->where('booking-id', $request->input('booking_id'))
            ->first();

        if (!$suggestedDriver) {
            Log::info("No suggested drivers in immediate");
            return $this->sendResponse($success, __('Driver not found!'));
        }

        $neighborhood = $trip->neighborhood;
        $university = $trip->university;
        $roadWay = $trip->{"road-way"};

        $from = array();
        $to = array();

        $to['ar'] = $roadWay == 'from' ? $neighborhood->{"neighborhood-ar"} : $university->{"name-ar"};
        $to['en'] = $roadWay == 'from' ? $neighborhood->{"neighborhood-eng"} : $university->{"name-eng"};
        $from['ar'] = $roadWay == 'from' ? $university->{"name-ar"} : $neighborhood->{"neighborhood-ar"};
        $from['en'] = $roadWay == 'from' ? $university->{"name-eng"} : $neighborhood->{"neighborhood-eng"};

        $success['source_lat'] = $roadWay == 'from' ? $university->lat : $trip->lat;
        $success['source_lng'] = $roadWay == 'from' ? $university->lng : $trip->lng;
        $success['destination_lat'] = $roadWay == 'from' ? $trip->lat : $university->lat;
        $success['destination_lng'] = $roadWay == 'from' ? $trip->lng : $university->lng;

        $success['to'] = $to;
        $success['from'] = $from;
        $success['estimated_time'] = $suggestedDriver->deliveryInfo?->{"expect-arrived"};
        $success['drivers'][] = new DriverInfoResource($suggestedDriver->driverinfo);

        Log::info("Execute Response: ", $success);

        return $this->sendResponse($success, __('Drivers'));
    }

    public function get(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'        => 'required|numeric',
            'locale'    => 'sometimes|nullable|string'
        ]);

        if($validator->fails()) {
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);
        }

        $success = [];

        $trip = RideBooking::with('passenger', 'neighborhood', 'service')
            ->where('id', $request->input('id'))
            ->first();

        if (!$trip) {
            Log::info("There is no Trip with ID: " . $request->input('id'));
            return $this->sendResponse($success, __('Trip not found!'));
        }
        Log::info("There is Trip with ID: " . $request->input('id'));

        $success['trip'] = new RideBookingResource($trip);

        $suggestDriver = SuggestionDriver::with('driverinfo')
            ->where('booking-id', $trip->id)
            ->first();

        $success['action'] = $suggestDriver?->action;
        $success['driverinfo'] = $suggestDriver ? new DriverInfoResource($suggestDriver->driverinfo) : new \stdClass();

        return $this->sendResponse($success, __('Trip Details'));
    }

    public function changeAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'        => 'required|numeric',
            'action'    => 'required|numeric',
        ]);

        if($validator->fails()) {
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);
        }

        $data = $validator->validated();
        $id = $data['id'];
        $action = $data['action'];

        $trip = RideBooking::where('id', $id)
            ->where('passenger-id', auth()->user()->id)
            ->first();

        if(!$trip) {
            return $this->sendError(__('Trip not found'), [__('Trip not found')]);
        }

//        $trip->update([
//            'action' => $action
//        ]);
//
//        return $this->sendResponse(new RideBookingResource($trip), __('Updated successfully'));

        $suggestedDrivers = SuggestionDriver::with('booking', 'driver')
            ->where('passenger-id', auth()->user()->id)
            ->where('booking-id', $id)
            ->get();

        if (!count($suggestedDrivers)) {
            return $this->sendResponse(__('Trip not found'), __('Trip not found'));
        }

        foreach ($suggestedDrivers as $suggestedDriver) {
            $suggestedDriver->update([
                'action' => $action
            ]);
        }

        return $this->sendResponse(new RideBookingResource($trip), __('Updated successfully'));
    }

    public function rate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rate'      => 'required|numeric|max:5',
            'driver-id' => 'required|numeric',
            'locale'    => 'sometimes|nullable|string'
        ]);

        if($validator->fails()) {
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);
        }

        $data = $validator->validated();
        $driverId = $data['driver-id'];
        $rate = $data['rate'];

        $driverInfo = DriverInfo::where('driver-id', $driverId)->first();

        if (!$driverInfo) {
            return $this->sendError(__('Driver not found'), [__('Driver not found')]);
        }

        $newRate = number_format((($driverInfo->{"driver-rate"} + $rate ) / 2),1);

        $driverInfo->update([
            'driver-rate' => $newRate
        ]);

        return $this->sendResponse(new DriverInfoResource($driverInfo), __('Updated successfully'));
    }

    public function getTimes($id)
    {
        $schedule = DriverSchedule::where('driver-id', $id)->first();

        $to = array();
        $from = array();

        $days = [ 'Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

        for ($i = 0; $i < count($days); $i++) {
            $to[$days[$i]] = [
                'from' => Carbon::parse($schedule->{$days[$i] . "-to"})->format('H:i'),
                'to' => Carbon::parse($schedule->{$days[$i] . "-to"})->addHours(2)->format('H:i') > $schedule->{$days[$i] . "-to"}
                    ? Carbon::parse($schedule->{$days[$i] . "-to"})->addHours(2)->format('H:i')
                    : "24:00"
            ];

            $from[$days[$i]] = [
                'from' => Carbon::parse($schedule->{$days[$i] . "-from"})->subHours(2)->format('H:i') > $schedule->{$days[$i] . "-from"}
                    ? "00:00"
                    : Carbon::parse($schedule->{$days[$i] . "-from"})->subHours(2)->format('H:i'),
                'to' => Carbon::parse($schedule->{$days[$i] . "-from"})->format('H:i')
            ];
        }

        return response()->json([
            'to'  => $to,
            'from'=> $from
        ]);
    }
}
