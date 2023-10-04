<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DriverInfoResource;
use App\Http\Resources\RideBookingResource;
use App\Http\Resources\UserResource;
use App\Models\DriverInfo;
use App\Models\DriversServices;
use App\Models\Neighbour;
use App\Models\RideBooking;
use App\Models\SuggestionDriver;
use App\Models\University;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ImmediateDriverController extends BaseController
{
    public function getDrivers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'neighborhood_id'   => 'required|numeric',
            'university_id'     => 'required|numeric',
            'road_way'          => 'required|string',
            'ride_type_id'      => 'required|numeric',
            'passenger_id'      => 'required|numeric',
            'lat'               => 'required|string',
            'lng'               => 'required|string',
            'locale'            => 'sometimes|nullable|string',
            'fake'              => 'sometimes|nullable|string'
        ]);

        if($validator->fails())
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);

        $data = $validator->validated();

        $data['now_day'] = Carbon::now()->format('l');

        $rideTypeId = $data['ride_type_id'];
        $universityId = $data['university_id'];
        $neighborhoodId = $data['neighborhood_id'];
        $passengerId = $data['passenger_id'];
        $lat = $data['lat'];
        $lng = $data['lng'];
        $roadWay = $data['road_way'];
        $nowDay = $data['now_day'];

        if($nowDay == 'Friday')
            return $this->sendError(__('Validation Error.'), [__('Not Available Immediate transport in Friday')], 422);

        $success = array();
        $success['drivers'] = [];
        $success['to'] = null;
        $success['from'] = null;
        $success['estimated_time'] = null;

        if (isset($data['fake'])) {
            $finalDriversId = User::select('users.id')
                ->where('user-type', 'driver')
                ->get()
                ->toArray();

            $ids = array();

            foreach ($finalDriversId as $driver) {
                $ids[] = $driver['id'];
            }

            $drivers = DriverInfo::with('driver')
                ->whereIn('driver-id', $ids)
                ->get();

            $neighborhood = Neighbour::findOrFail($neighborhoodId);
            $university = University::whereId($universityId)->first();

            $from = array();
            $to = array();
            if($roadWay == 'from') {
                $to['ar'] = $neighborhood->{"neighborhood-ar"};
                $to['en'] = $neighborhood->{"neighborhood-eng"};
                $from['ar'] = $university->{"name-ar"};
                $from['en'] = $university->{"name-eng"};
                $success['destination_lat'] = $lat;
                $success['destination_lng'] = $lng;
            } elseif ($roadWay == 'to') {
                $to['ar'] = $university->{"name-ar"};
                $to['en'] = $university->{"name-eng"};
                $from['ar'] = $neighborhood->{"neighborhood-ar"};
                $from['en'] = $neighborhood->{"neighborhood-eng"};
                $success['destination_lat'] = $university->lat;
                $success['destination_lng'] = $university->lng;
            }
            $success['to'] = $to;
            $success['from'] = $from;
            $success['estimated_time'] = 15;
            $success['trip'] = [];
            $success['drivers'] = DriverInfoResource::collection($drivers);;
            return $this->sendResponse($success, __('Drivers'));
        }

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

        if(!count($drivers)) {
            $rideBooking = RideBooking::create([
                'passenger-id'      => $passengerId,
                'neighborhood-id'   => $universityId,
                'lat'               => $lat,
                'lng'               => $lng,
                'service-id'        => $rideTypeId,
                'action'            => 1,
                'date-of-add'       => Carbon::now()
            ]);
            $success['trip'] = $rideBooking;
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
            $rideBooking = RideBooking::create([
                'passenger-id'      => $passengerId,
                'neighborhood-id'   => $universityId,
                'lat'               => $lat,
                'lng'               => $lng,
                'service-id'        => $rideTypeId,
                'action'            => 2,
                'date-of-add'       => Carbon::now()
            ]);
            $success['trip'] = $rideBooking;
            return $this->sendResponse($success, __('Found Drivers'));
        }

        $foundDriverId = array();
        foreach ($foundDrivers as $foundDriver) {
            $foundDriverId[] = $foundDriver['Found-driver-id'];
        }

        $suggestDriverId = DB::table('drivers-schedule')
            ->select('driver-id AS suggest-driver-id')
            ->when($roadWay == 'to', function ($query) use ($nowDay, $roadWay) {
                $query->where("$nowDay-$roadWay", '>=', Carbon::now()->subMinutes(15)->format('H:i:s'));
                $query->where("$nowDay-$roadWay", '<=', Carbon::now()->addHour()->format("H:i:s"));
            })
            ->when($roadWay == 'from', function ($query) use ($nowDay, $roadWay) {
                $query->where("$nowDay-$roadWay", '<=', Carbon::now()->subMinutes(15)->format('H:i:s'));
                $query->where("$nowDay-$roadWay", '>=', Carbon::now()->addHour()->format("H:i:s"));
            })
            ->whereIn('driver-id', $foundDriverId)
            ->get();

        if(count($suggestDriverId)) {
            $rideBooking = RideBooking::create([
                'passenger-id'      => $passengerId,
                'neighborhood-id'   => $universityId,
                'lat'               => $lat,
                'lng'               => $lng,
                'service-id'        => $rideTypeId,
                'action'            => 0,
                'date-of-add'       => Carbon::now()
            ]);

            $finalDriversId = array();

            foreach ($suggestDriverId as $driver) {
                $finalDriversId[] = $driver->{"suggest-driver-id"};
                SuggestionDriver::create([
                    'booking-id'    => $rideBooking->id,
                    'driver-id'     => $driver->{"suggest-driver-id"},
                    'action'        => 0,
                    'date-of-add'   => Carbon::now()
                ]);
            }

            $drivers = DriverInfo::with('driver')
                ->whereIn('driver-id', $finalDriversId)
                ->get();
            $success['drivers'] = [];//DriverInfoResource::collection($drivers);

            $neighborhood = Neighbour::findOrFail($neighborhoodId);
            $university = University::whereId($universityId)->first();
            $from = array();
            $to = array();

            if($roadWay == 'from') {
                $to['ar'] = $neighborhood->{"neighborhood-ar"};
                $to['en'] = $neighborhood->{"neighborhood-eng"};
                $from['ar'] = $university->{"name-ar"};
                $from['en'] = $university->{"name-eng"};
                $success['destination_lat'] = $lat;
                $success['destination_lng'] = $lng;
            } elseif ($roadWay == 'to') {
                $to['ar'] = $university->{"name-ar"};
                $to['en'] = $university->{"name-eng"};
                $from['ar'] = $neighborhood->{"neighborhood-ar"};
                $from['en'] = $neighborhood->{"neighborhood-eng"};
                $success['destination_lat'] = $university->lat;
                $success['destination_lng'] = $university->lng;
            }

            $success['to'] = $to;
            $success['from'] = $from;
            $success['estimated_time'] = 15;
            $success['trip'] = $rideBooking;

            return $this->sendResponse($success, __('Drivers'));
        }

        $rideBooking = RideBooking::create([
            'passenger-id'      => $passengerId,
            'neighborhood-id'   => $universityId,
            'lat'               => $lat,
            'lng'               => $lng,
            'service-id'        => $rideTypeId,
            'action'            => 3,
            'date-of-add'       => Carbon::now()
        ]);
        $success['trip'] = $rideBooking;
        return $this->sendResponse($success, __('No Drivers!'));
    }

    public function get(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'        => 'required|numeric',
            'locale'    => 'sometimes|nullable|string'
        ]);

        if($validator->fails())
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);

        $data = $validator->validated();

        $trip = RideBooking::whereId($data['id'])->first();

        if ($trip)
            $trip = new RideBookingResource($trip);

        return $this->sendResponse($trip, __('Trip Details'));
    }

    public function changeStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'        => 'required|numeric',
            'status'    => 'required|numeric',
        ]);

        if($validator->fails())
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);

        $data = $validator->validated();
        $id = $data['id'];
        $status = $data['status'];

        $trip = RideBooking::whereId($id)->first();

        if(!$trip)
            return $this->sendError(__('Trip not found'), [__('Trip not found')]);

        $trip->update([
            'status' => $status
        ]);

        return $this->sendResponse(new RideBookingResource($trip), __('Updated successfully'));
    }

    public function rate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rate'      => 'required|numeric|max:5',
            'driver-id' => 'required|numeric',
            'locale'    => 'sometimes|nullable|string'
        ]);

        if($validator->fails())
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);

        $data = $validator->validated();
        $driverId = $data['driver-id'];
        $rate = $data['rate'];

        $driverInfo = DriverInfo::where('driver-id', $driverId)->first();

        if (!$driverInfo)
            return $this->sendError(__('Driver not found'), [__('Driver not found')]);

        $newRate = ($driverInfo->{"driver-rate"} + $rate ) / 2;

        $driverInfo->update([
            'driver-rate' => $newRate
        ]);

        return $this->sendResponse(new DriverInfoResource($driverInfo), __('Updated successfully'));
    }
}
