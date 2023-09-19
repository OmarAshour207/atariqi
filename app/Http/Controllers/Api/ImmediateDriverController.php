<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\DriverInfo;
use App\Models\DriversServices;
use App\Models\RideBooking;
use App\Models\SuggestionDriver;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ImmediateDriverController extends BaseController
{
    public function get(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'neighborhood_id'   => 'required|numeric',
            'university_id'     => 'required|numeric',
            'road_way'          => 'required|string',
            'ride_type_id'      => 'required|numeric',
            'passenger_id'      => 'required|numeric',
            'now_day'           => 'required|string',
            'lat'               => 'required|string',
            'lng'               => 'required|string',
        ]);

        if($validator->fails())
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);

        $data = $validator->validated();

        $data['now_datetime'] = now();
        $rideTypeId = $data['ride_type_id'];
        $universityId = $data['university_id'];

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
            RideBooking::create([
                'passenger-id'      => $data['passenger_id'],
                'neighborhood-id'   => $data['university_id'],
                'lat'               => $data['lat'],
                'lng'               => $data['lng'],
                'service-id'        => $data['ride_type_id'],
                'action'            => 1,
                'date-of-add'       => $data['now_datetime']
            ]);
            return $this->sendResponse($drivers, __('Drivers'));
        }

        $driverIds = array();
        foreach ($drivers as $driver) {
            $driverIds[] = $driver['driver_id'];
        }
        $neighborhoodId = $data['neighborhood_id'];
        $roadWay = $data['road_way'];

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
            RideBooking::create([
                'passenger-id'      => $data['passenger_id'],
                'neighborhood-id'   => $data['university_id'],
                'lat'               => $data['lat'],
                'lng'               => $data['lng'],
                'service-id'        => $data['ride_type_id'],
                'action'            => 2,
                'date-of-add'       => $data['now_datetime']
            ]);
            return $this->sendResponse($foundDrivers, __('Found Drivers'));
        }

        $foundDriverId = array();
        foreach ($foundDrivers as $foundDriver) {
            $foundDriverId[] = $foundDriver['Found-driver-id'];
        }

        $now = Carbon::now();
        $nowDay = $data['now_day'];

        $suggestDriverId = DB::table('drivers-schedule')
            ->select('driver-id AS suggest-driver-id')
            ->where(function ($query) use ($now, $nowDay, $roadWay, $foundDriverId) {
                $query->where("$nowDay-$roadWay", '>', $now->subMinutes(15)->format('H:i:s'));
                $query->where("$nowDay-$roadWay", '<', $now->addMinutes(1)->format('H:i:s'));
                $query->whereIn('driver-id', $foundDriverId);
            })
            ->get();

        if(count($suggestDriverId)) {

            RideBooking::create([
                'passenger-id'      => $data['passenger_id'],
                'neighborhood-id'   => $data['university_id'],
                'lat'               => $data['lat'],
                'lng'               => $data['lng'],
                'service-id'        => $data['ride_type_id'],
                'action'            => 0,
                'date-of-add'       => $data['now_datetime']
            ]);

            $rideBooking = RideBooking::select('id as booking-id')
                ->where('passenger-id', $data['passenger_id'])
                ->where('date-of-add', $now)
                ->first();

            if($rideBooking) {
                foreach ($suggestDriverId as $driver) {
                    SuggestionDriver::create([
                        'booking-id'    => $rideBooking->{"booking-id"},
                        'driver-id'     => $driver->{"suggest-driver-id"},
                        'action'        => 0,
                        'date-of-add'   => $now
                    ]);
                }
            }
        } else {
            RideBooking::create([
                'passenger-id'      => $data['passenger_id'],
                'neighborhood-id'   => $data['university_id'],
                'lat'               => $data['lat'],
                'lng'               => $data['lng'],
                'service-id'        => $data['ride_type_id'],
                'action'            => 3,
                'date-of-add'       => $data['now_datetime']
            ]);

            $drivers = DriverInfo::whereIn('driver-id', $foundDriverId)->get();
            return $this->sendResponse($drivers, __('Drivers'));
        }
    }
}
