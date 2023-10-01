<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DayRideBooking;
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
            'date'              => 'required|date',
            'time_go'           => 'required|string',
            'time_back'         => 'required|string',
            'road_way'          => 'required|string',
            'locale'            => 'sometimes|nullable|string'
        ]);

        if($validator->fails())
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);

        $data = $validator->validated();

        $rideTypeId = $data['ride_type_id'];
        $universityId = $data['university_id'];

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
            DayRideBooking::create([
                'passenger-id'      => $data['passenger_id'],
                'neighborhood-id'   => $data['neighborhood_id'],
                'service-id'        => $data['service_id'],
                'date-of-ser'       => $data['date'],
                'road-way'          => $data['road_way'],
                'time-go'           => $data['time_go'] ?? null,
                'time-back'         => $data['time_back'] ?? null,
                'action'            => 1,
                'date-of-add'       => Carbon::now()
            ]);
        }
        dd($driversIds);
    }
}
