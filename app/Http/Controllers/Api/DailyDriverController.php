<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DayRideBookingResource;
use App\Http\Resources\DriverInfoDayRideResource;
use App\Http\Resources\DriverInfoResource;
use App\Http\Resources\NeighbourResource;
use App\Http\Resources\SugDayDrivingResource;
use App\Http\Resources\UniversityResource;
use App\Models\DayRideBooking;
use App\Models\DriverInfo;
use App\Models\DriversServices;
use App\Models\Neighbour;
use App\Models\SugDayDriver;
use App\Models\University;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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
            'date'              => 'required|date_format:Y-m-d',
            'road_way'          => 'required|string',
            'time_go'           => Rule::requiredIf($request->request->get('road_way') == 'to' || $request->request->get('road_way') == 'both'),
            'time_back'         => Rule::requiredIf($request->request->get('road_way') == 'from' || $request->request->get('road_way') == 'both'),
            'locale'            => 'sometimes|nullable|string'
        ]);

        if($validator->fails())
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);

        $data = $validator->validated();

        $passengerId = $data['passenger_id'];
        $rideTypeId = $data['ride_type_id'];
        $universityId = $data['university_id'];
        $neighborhoodId = $data['neighborhood_id'];
        $roadWay = $data['road_way'];
        $date = $data['date'];
        $timeBack = isset($data['time_back']) ? $data['time_back'] : null;
        $timeGo =  isset($data['time_go']) ? $data['time_go'] : null;
        $lat = $data['lat'];
        $lng = $data['lng'];
        $dateDay = Carbon::parse($date)->format('l');

        if ($dateDay == 'Friday')
            return $this->sendError(__('Validation Error.'), [ __('Not Available Daily transport in Friday')], 422);

        $success = array();
        $to = array();
        $from = array();
        $success['drivers'] = array();
        $success['trip'] = array();

        $neighborhood = Neighbour::whereId($neighborhoodId)->first();
        $university = University::whereId($universityId)->first();

        $success['neighborhood'] = new NeighbourResource($neighborhood);
        $success['university'] = new UniversityResource($university);
        $success['roadWay'] = $roadWay;

        if($roadWay == 'from') {
            $from['ar'] = $university->{"name-ar"};
            $from['en'] = $university->{"name-eng"};
            $to['ar'] = $neighborhood->{"neighborhood-ar"};
            $to['en'] = $neighborhood->{"neighborhood-eng"};
            $success['destination_lat'] = $lat;
            $success['destination_lng'] = $lng;
        } else {
            $from['ar'] = $neighborhood->{"neighborhood-ar"};
            $from['en'] = $neighborhood->{"neighborhood-eng"};
            $to['ar'] = $university->{"name-ar"};
            $to['en'] = $university->{"name-eng"};
            $success['destination_lat'] = $university->lat;
            $success['destination_lng'] = $university->lng;
        }
        $success['to'] = $to;
        $success['from'] = $from;

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
                'passenger-id'      => $passengerId,
                'neighborhood-id'   => $neighborhoodId,
                'university-id'     => $universityId,
                'service-id'        => $rideTypeId,
                'date-of-ser'       => $date,
                'road-way'          => $roadWay,
                'time-go'           => $timeGo,
                'time-back'         => $timeBack,
                'action'            => 1,
                'lat'               => $lat,
                'lng'               => $lng,
                'date-of-add'       => Carbon::now()
            ]);
            $success['trip'] = new DayRideBookingResource($dayRideBooking);
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
                'passenger-id'      => $passengerId,
                'neighborhood-id'   => $neighborhoodId,
                'university-id'     => $universityId,
                'service-id'        => $rideTypeId,
                'date-of-ser'       => $date,
                'road-way'          => $roadWay,
                'time-go'           => $timeGo,
                'time-back'         => $timeBack,
                'lat'               => $lat,
                'lng'               => $lng,
                'action'            => 2,
                'date-of-add'       => Carbon::now()
            ]);
            $success['trip'] = new DayRideBookingResource($dayRideBooking);
            return $this->sendResponse($success, __('No Drivers'));
        }

        // Third Query
        $driversSchedule = array();

        if ($roadWay == 'to') {
            $driversSchedule = DB::table('drivers-schedule')
                ->select("driver-id AS suggest-driver-id")
                ->where("$dateDay-to" , '<=', "$timeGo")
                ->whereRaw('`' . "$dateDay-to" . '` + INTERVAL 2 HOUR >= ?', [$timeGo] )
                ->whereIn('driver-id', $rideTypeDrivers)
                ->get()
                ->toArray();
        } elseif ($roadWay == 'from') {
            $driversSchedule =  DB::table('drivers-schedule')
                ->select("driver-id AS suggest-driver-id")
                ->where("$dateDay-from" , '>=', "$timeGo")
                ->whereRaw('`' . "$dateDay-from" . '` - INTERVAL 2 HOUR <= ?', [$timeGo] )
                ->whereIn('driver-id', $rideTypeDrivers)
                ->get()
                ->toArray();
        } elseif ($roadWay == 'both') {
            $driversSchedule =  DB::table('drivers-schedule')
                ->select("driver-id AS suggest-driver-id")
                ->where("$dateDay-to" , '<=', "$timeGo")
                ->whereRaw('`' . "$dateDay-to" . '` + INTERVAL 2 HOUR >= ?', [$timeGo] )
                ->where("$dateDay-from" , '>=', "$timeGo")
                ->whereRaw('`' . "$dateDay-from" . '` - INTERVAL 2 HOUR <= ?', [$timeGo] )
                ->whereIn('driver-id', $rideTypeDrivers)
                ->get()
                ->toArray();
        }

        if(!count($driversSchedule)) {
            $dayRideBooking = DayRideBooking::create([
                'passenger-id'      => $passengerId,
                'neighborhood-id'   => $neighborhoodId,
                'university-id'     => $universityId,
                'service-id'        => $rideTypeId,
                'date-of-ser'       => $date,
                'road-way'          => $roadWay,
                'time-go'           => $timeGo,
                'time-back'         => $timeBack,
                'action'            => 3,
                'lat'               => $lat,
                'lng'               => $lng,
                'date-of-add'       => Carbon::now()
            ]);
            $success['trip'] = new DayRideBookingResource($dayRideBooking);

            return $this->sendResponse($success, __('No Drivers'));
        }

        $driversIds = array();
        foreach ($driversSchedule as $driverSchedule) {
            $driversIds[] = $driverSchedule->{'suggest-driver-id'};
        }

        $drivers = DriverInfo::with(['driver', 'schedule' => function($query) use ($dateDay) {
                $query->select("id", "driver-id", "$dateDay-to AS to", "$dateDay-from as from");
            }])
            ->whereIn('driver-id', $driversIds)
            ->get();

        $success['drivers'] = DriverInfoDayRideResource::collection($drivers);

        return $this->sendResponse($success, __('Drivers'));
    }

    public function selectDriver(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'passenger_id'      => 'required|numeric',
            'lat'               => 'required|string',
            'lng'               => 'required|string',
            'neighborhood_id'   => 'required|numeric',
            'university_id'     => 'required|numeric',
            'driver_id'         => 'required|numeric',
            'ride_type_id'      => 'required|numeric',
            'date'              => 'required|string',
            'time_go'           => 'sometimes|nullable|string',
            'time_back'         => 'sometimes|nullable|string',
            'road_way'          => 'required|string',
            'locale'            => 'sometimes|nullable|string'
        ]);

        if($validator->fails())
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);

        $data = $validator->validated();

        $rideTypeId = $data['ride_type_id'];
        $passengerId = auth()->user()->id ?? $data['passenger_id'];
        $neighborhoodId = $data['neighborhood_id'];
        $universityId = $data['university_id'];
        $driverId = $data['driver_id'];
        $roadWay = $data['road_way'];
        $date = $data['date'];
        $timeBack = $data['time_back'];
        $timeGo = $data['time_go'];
        $dayRideBooking = '';

        $savingData = [
            'passenger-id'      => $passengerId,
            'neighborhood-id'   => $neighborhoodId,
            'university-id'     => $universityId,
            'service-id'        => $rideTypeId,
            'date-of-ser'       => $date,
            'road-way'          => $roadWay,
            'action'            => 0,
            'date-of-add'       => Carbon::now()
        ];

        if ($roadWay == 'to' || $roadWay == 'both') {
            $savingData['time-go'] = $timeGo;
            $dayRideBooking = DayRideBooking::create($savingData);
        }
        if ($roadWay == 'from' || $roadWay == 'both') {
            $savingData['time-back'] = $timeBack;
            $dayRideBooking = DayRideBooking::create($savingData);
        }

        $sugDayDriver = SugDayDriver::create([
            'booking-id'        => $dayRideBooking->id,
            'driver-id'         => $driverId,
            'passenger-id'      => $passengerId,
            'action'            => 0,
            'date-of-add'       => Carbon::now()
        ]);

        $success = [];
        $success['trip'] = new DayRideBookingResource($dayRideBooking);
        $success['sug_day_driver'] = new SugDayDrivingResource($sugDayDriver);

        return $this->sendResponse($success, __('Success'));
    }

    public function sendToAllDrivers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'passenger_id'      => 'sometimes|nullable|numeric',
            'neighborhood_id'   => 'required|numeric',
            'university_id'     => 'required|numeric',
            'ride_type_id'      => 'required|numeric',
            'date'              => 'required|date_format:Y-m-d',
            'time_go'           => 'sometimes|nullable|string',
            'time_back'         => 'sometimes|nullable|string',
            'road_way'          => 'required|string',
            'locale'            => 'sometimes|nullable|string'
        ]);

        if($validator->fails())
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);

        $data = $validator->validated();
        $roadWay = $data['road_way'];
        $date = $data['date'];
        $rideTypeId = $data['ride_type_id'];
        $neighborhoodId = $data['neighborhood_id'];
        $universityId = $data['university_id'];
        $passengerId = auth()->user()->id ?? $data['passenger_id'];
        $timeGo = $data['time_go'];
        $timeBack = $data['time_back'];

        $dayRideBooking = DayRideBooking::create([
            'passenger-id'      => $passengerId,
            'neighborhood-id'   => $neighborhoodId,
            'university-id'     => $universityId,
            'service-id'        => $rideTypeId,
            'date-of-ser'       => $date,
            'road-way'          => $roadWay,
            'action'            => 4,
            'time-go'           => $timeGo,
            'time-back'         => $timeBack,
            'date-of-add'       => Carbon::now()
        ]);

        $success['trip'] = new DayRideBookingResource($dayRideBooking);

        return $this->sendResponse($success, __('Success'));
    }

    public function getUserNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'locale'        => 'sometimes|nullable|string'
        ]);

        if($validator->fails())
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);

        $passengerId = auth()->user()->id;

        $suggestedDrivers = SugDayDriver::with('driver', 'booking')
            ->where('passenger-id', $passengerId)
            ->where('viewed', 0)
            ->get();

        $success = [];
//        $success['trips'] = SugDayDrivingResource::collection($suggestedDrivers);
        $messages = array();

        // send notification
        $message = '';
        $title = __('You have a notification from Atariqi');
        foreach ($suggestedDrivers as $suggestedDriver) {
            if ($suggestedDriver->action == 1)
                $message = __('Your trip accepted at date') . " " . $suggestedDriver->booking->{"date-of-ser"} . "\n" . __('with Driver') . " " . $suggestedDriver->driver->{"user-first-name"} . " " . $suggestedDriver->driver->{"user-last-name"};
            elseif ($suggestedDriver->action == 2)
                $message = __('Your trip rejected at date') . " " . $suggestedDriver->booking->{"date-of-ser"} . "\n" . __('with Driver') . " " . $suggestedDriver->driver->{"user-first-name"} . " " . $suggestedDriver->driver->{"user-last-name"};

            if (!empty($message)) {
                sendNotification(['title' => $title, 'body' => $message, 'tokens' => auth()->user()->fcm_token]);
                $suggestedDriver->update(['viewed' => 1]);
                $messages[] = $message;
            }
        }

        $success['messages'] = $messages;
        return $this->sendResponse($success, __('Trips'));
    }

    public function getUserSummary(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'locale'        => 'sometimes|nullable|string'
        ]);

        if($validator->fails())
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);

        $passengerId = auth()->user()->id;

        $suggestedDrivers = SugDayDriver::with('driver', 'booking')
            ->where('passenger-id', $passengerId)
            ->get();

        $success = [];
        $success['trips'] = SugDayDrivingResource::collection($suggestedDrivers);

        return $this->sendResponse($success, __('Trips'));
    }

    public function executeRide(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'locale'        => 'sometimes|nullable|string'
        ]);

        if($validator->fails())
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);

        $passengerId = auth()->user()->id;
        $nowDate = Carbon::now()->format('Y-m-d');
        $nowTime = Carbon::now()->format('H:i:s');

        $ride = DayRideBooking::where('date-of-ser', $nowDate)
            ->where(function ($query) use ($nowTime) {
                $query->where('time-go', $nowTime)
                    ->orWhere('time-back', $nowTime);
            })
            ->first();

        if ($ride) {
            $sugDayDriver = SugDayDriver::where([
                ['booking-id', $ride->id],
                ['action', 3],
                ['passenger-id', $passengerId]
            ])->first();

            $success = [];
            $success['sug_day_driver'] = new SugDayDrivingResource($sugDayDriver);
            if ($ride->{"road-way"} == 'from') {
                $success['destination_lat'] = $ride->lat;
                $success['destination_lng'] = $ride->lat;
                $success['source_lat'] = $ride->university->lat;
                $success['source_lng'] = $ride->university->lng;
            } else {
                $success['destination_lat'] = $ride->university->lat;
                $success['destination_lng'] = $ride->university->lng;
                $success['source_lat'] = $ride->lat;
                $success['source_lng'] = $ride->lng;
            }
            sendNotification([
                'title'     => __('You have a notification from Atariqi'),
                'body'      => __("an order from Atariqi to accept the ride"),
                'tokens'    => [auth()->user()->fcm_token]
            ]);

            return $this->sendResponse($success, __("an order from Atariqi to accept the ride"));
        }

        return $this->sendResponse([], __("Empty order"));
    }

    public function getTripDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sug_day_driver_id' => 'required|numeric',
            'locale'            => 'sometimes|nullable|string'
        ]);

        if($validator->fails())
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);

        $data = $validator->validated();
        $id = $data['sug_day_driver_id'];

        $sugDayDriver = SugDayDriver::whereId($id)
            ->where('passenger-id', auth()->user()->id)
            ->first();

        if (!$sugDayDriver)
            return $this->sendError(__('Trip not found!'), [__('Trip not found')]);

        return $this->sendResponse(new SugDayDrivingResource($sugDayDriver), __('Success'));
    }

    public function changeAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sug_day_driver_id' => 'required|numeric',
            'action'            => 'required|numeric',
            'locale'            => 'sometimes|nullable|string'
        ]);

        if($validator->fails())
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);

        $data = $validator->validated();
        $action = $data['action'];
        $id = (int) $data['sug_day_driver_id'];

        $sugDayDriver = SugDayDriver::where('id', $id)
            ->where('passenger-id', auth()->user()->id)
            ->first();
        if (!$sugDayDriver)
            return $this->sendError(__('Trip not found!'), [__('Trip not found')]);
        $sugDayDriver->update(['action' => $action]);

        $success = array();
        $success['sug_day_drivers'] = new SugDayDrivingResource($sugDayDriver);

        return $this->sendResponse($success, __('Success'));
    }

}
