<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DriverInfoResource;
use App\Http\Resources\NeighbourResource;
use App\Http\Resources\UniversityResource;
use App\Models\DayRideBooking;
use App\Models\DriverInfo;
use App\Models\DriversServices;
use App\Models\Neighbour;
use App\Models\University;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DailyDriverController extends BaseController
{
    public function getDrivers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'neighborhood_id'   => 'required|numeric',
            'university_id'     => 'required|numeric',
            'ride_type_id'      => 'required|numeric',
            'passenger_id'      => 'required|numeric',
            'lat'               => 'required|string',
            'lng'               => 'required|string',
            'date'              => 'required|string',
            'time_go'           => 'required|string',
            'time_back'         => 'required|string',
            'road_way'          => 'required|string',
            'locale'            => 'sometimes|nullable|string'
        ]);

        if($validator->fails())
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);

        $data = $validator->validated();
        $data['date'] = Carbon::now()->format('l');

        $rideTypeId = $data['ride_type_id'];
        $universityId = $data['university_id'];
        $neighborhoodId = $data['neighborhood_id'];
        $roadWay = $data['road_way'];
        $date = $data['date'];
        $timeBack = $data['time_back'];
        $timeGo = $data['time_go'];

        // First Query
        $driversIds = User::select('users.id')
            ->join('university', 'users.university-id', '=', 'university.id')
            ->join('uni-driving-services', function ($join) use ($rideTypeId, $universityId) {
                $join->on('uni-driving-services.university-id', '=', 'users.university-id')
                    ->where('uni-driving-services.service-id', '=', $rideTypeId);
            })
            ->where('users.user-type', '=', 'driver')
            ->where('users.university-id', '=', $universityId)
            ->get()
            ->toArray();

        if(!count($driversIds)) {
            $dayRideBooking = DayRideBooking::create([
                'passenger-id'      => $data['passenger_id'],
                'neighborhood-id'   => $data['neighborhood_id'],
                'service-id'        => $data['ride_type_id'],
                'date-of-ser'       => $data['date'],
                'road-way'          => $data['road_way'],
                'time-go'           => $data['time_go'] ?? null,
                'time-back'         => $data['time_back'] ?? null,
                'action'            => 1,
                'date-of-add'       => Carbon::now()
            ]);
            $success['trip'] = $dayRideBooking;
            return $this->sendResponse($success, __('No Drivers'));
        }

        // Second Query
        $rideTypeDrivers = DriversServices::select('drivers-services.driver-id AS found-driver-id')
            ->when($roadWay == 'both', function ($query) use ($neighborhoodId, $roadWay) {
                $query->join('drivers-neighborhoods', function ($join) use ($neighborhoodId, $roadWay) {
                    $join->on('drivers-neighborhoods.driver-id', '=', 'drivers-services.driver-id')
                        ->where("drivers-neighborhoods.neighborhoods-to", 'LIKE', "%$neighborhoodId | %")
                        ->where("drivers-neighborhoods.neighborhoods-from", 'LIKE', "%$neighborhoodId | %");
                });
            })
            ->when($roadWay != 'both', function ($query) use ($neighborhoodId, $roadWay) {
                $query->join('drivers-neighborhoods', function ($join) use ($neighborhoodId, $roadWay) {
                    $join->on('drivers-neighborhoods.driver-id', '=', 'drivers-services.driver-id')
                        ->where("drivers-neighborhoods.neighborhoods-$roadWay", 'LIKE', "%$neighborhoodId | %");
                });
            })
            ->whereIn('drivers-services.driver-id', $driversIds)
            ->where('drivers-services.service-id', '=', $rideTypeId)
            ->get()
            ->toArray();

        if(!count($rideTypeDrivers)) {
            $dayRideBooking = DayRideBooking::create([
                'passenger-id'      => $data['passenger_id'],
                'neighborhood-id'   => $data['neighborhood_id'],
                'service-id'        => $data['ride_type_id'],
                'date-of-ser'       => $data['date'],
                'road-way'          => $data['road_way'],
                'time-go'           => $data['time_go'] ?? null,
                'time-back'         => $data['time_back'] ?? null,
                'action'            => 2,
                'date-of-add'       => Carbon::now()
            ]);
            $success['trip'] = $dayRideBooking;
            return $this->sendResponse($success, __('No Drivers'));
        }

        // Third Query
        $driversSchedule = array();

        if ($roadWay == 'to') {
            $driversSchedule = DB::table('drivers-schedule')
                ->select("driver-id AS suggest-driver-id")
                ->where("$date-to" , '<=', "$timeGo")
                ->whereRaw('`' . "$date-to" . '` + INTERVAL 2 HOUR >= ?', [$timeGo] )
                ->whereIn('driver-id', $rideTypeDrivers)
                ->get()
                ->toArray();
        } elseif ($roadWay == 'from') {
            $driversSchedule =  DB::table('drivers-schedule')
                ->select("driver-id AS suggest-driver-id")
                ->where("$date-from" , '>=', "$timeGo")
                ->whereRaw('`' . "$date-from" . '` - INTERVAL 2 HOUR <= ?', [$timeGo] )
                ->whereIn('driver-id', $rideTypeDrivers)
                ->get()
                ->toArray();
        } elseif ($roadWay == 'both') {
            $driversSchedule =  DB::table('drivers-schedule')
                ->select("driver-id AS suggest-driver-id")
                ->where("$date-to" , '<=', "$timeGo")
                ->whereRaw('`' . "$date-to" . '` + INTERVAL 2 HOUR >= ?', [$timeGo] )
                ->where("$date-from" , '>=', "$timeGo")
                ->whereRaw('`' . "$date-from" . '` - INTERVAL 2 HOUR <= ?', [$timeGo] )
                ->whereIn('driver-id', $rideTypeDrivers)
                ->get()
                ->toArray();
        }

        if(!count($driversSchedule)) {
            $dayRideBooking = DayRideBooking::create([
                'passenger-id'      => $data['passenger_id'],
                'neighborhood-id'   => $data['neighborhood_id'],
                'service-id'        => $data['ride_type_id'],
                'date-of-ser'       => $data['date'],
                'road-way'          => $data['road_way'],
                'time-go'           => $data['time_go'] ?? null,
                'time-back'         => $data['time_back'] ?? null,
                'action'            => 3,
                'date-of-add'       => Carbon::now()
            ]);
            $success['trip'] = $dayRideBooking;
            return $this->sendResponse($success, __('No Drivers'));
        }

        $driversIds = array();
        foreach ($driversSchedule as $driverSchedule) {
            $driversIds[] = $driverSchedule->{'suggest-driver-id'};
        }

        $neighborhood = Neighbour::whereId($neighborhoodId)->first();
        $university = University::whereId($universityId)->first();
        $drivers = DriverInfo::with('driver')
            ->whereIn('driver-id', $driversIds)
            ->get();

        $success['drivers'] = DriverInfoResource::collection($drivers);
        $success['neighborhood'] = new NeighbourResource($neighborhood);
        $success['university'] = new UniversityResource($university);
        $success['go'] = $timeGo;
        $success['back'] = $timeBack;

        return $this->sendResponse($success, __('Drivers'));
    }
}
