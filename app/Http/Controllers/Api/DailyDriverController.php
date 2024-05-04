<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\DayRideBookingResource;
use App\Http\Resources\DriverInfoDayRideResource;
use App\Http\Resources\NeighbourResource;
use App\Http\Resources\SugDayDrivingResource;
use App\Http\Resources\SuggestionDriver as SuggestionResource;
use App\Http\Resources\UniversityResource;
use App\Models\DayRideBooking;
use App\Models\DriverInfo;
use App\Models\DriversServices;
use App\Models\Neighbour;
use App\Models\Service;
use App\Models\SugDayDriver;
use App\Models\SuggestionDriver;
use App\Models\University;
use App\Models\User;
use App\Models\WeekRideBooking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DailyDriverController extends BaseController
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
            'date'              => 'required',
            'time_go'           => 'sometimes|nullable',
            'time_back'         => 'sometimes|nullable',
            'locale'            => 'sometimes|nullable|string'
        ]);

        if($validator->fails())
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);

        $data = $validator->validated();

        $passengerId = auth()->user()->id;
        $rideTypeId = $data['ride_type_id'];
        $universityId = $data['university_id'];
        $neighborhoodId = $data['neighborhood_id'];

        $service = $this->getService($rideTypeId);
        $roadWay = $service->{"road-way"};

        $date = $data['date'];

        $date = convertArabicDateToEnglish($date);
        $date = Carbon::createFromFormat('Y-m-d', $date)->format('Y-m-d');
        $timeBack = isset($data['time_back']) ? convertArabicDateToEnglish($data['time_back']) : null;
        $timeGo =  isset($data['time_go']) ? convertArabicDateToEnglish($data['time_go']) : null;

        $lat = $data['lat'];
        $lng = $data['lng'];
        $dateDay = Carbon::parse($date)->format('l');

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
        $success['action'] = 'daily/transport/trip';

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
                        ->where("drivers-neighborhoods.neighborhoods-$roadWay", 'LIKE', "%$neighborhoodId | %")
                        ->orWhere("drivers-neighborhoods.neighborhoods-$roadWay", 'LIKE', "%$neighborhoodId%");
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

        Log::info("Time Back : $timeBack");
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
                ->where("$dateDay-from" , '>=', "$timeBack")
                ->whereRaw('`' . "$dateDay-from" . '` - INTERVAL 2 HOUR <= ?', [$timeBack] )
                ->whereIn('driver-id', $rideTypeDrivers)
                ->get()
                ->toArray();
        } elseif ($roadWay == 'both') {
            $driversSchedule =  DB::table('drivers-schedule')
                ->select("driver-id AS suggest-driver-id")
                ->where("$dateDay-to" , '<=', "$timeGo")
                ->whereRaw('`' . "$dateDay-to" . '` + INTERVAL 2 HOUR >= ?', [$timeGo] )

                ->where("$dateDay-from" , '>=', "$timeBack")
                ->whereRaw('`' . "$dateDay-from" . '` - INTERVAL 2 HOUR <= ?', [$timeBack] )
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

    private function checkScheduleTime($time, $roadWay): bool
    {
        $passengerId = auth()->user()->id;
        $date = $time['date'];
        $timeBack = $time['time_back'];
        $timeGo = $time['time_go'];

        $weekRides = WeekRideBooking::where('passenger-id', $passengerId)
            ->where('date-of-ser', $date)
            ->when($roadWay == 'to' || $roadWay == 'both', function ($query) use ($timeGo) {
                $query->where('time-go', $timeGo);
            })->when($roadWay == 'from' || $roadWay == 'both', function ($query) use ($timeBack) {
                $query->where('time-back', $timeBack);
            })->get()
            ->toArray();

        if (count($weekRides))
            return false;


        $dailyRides = DayRideBooking::where('passenger-id', $passengerId)
            ->where('date-of-ser', $date)
            ->when($roadWay == 'to' || $roadWay == 'both', function ($query) use ($timeGo) {
                $query->where('time-go', $timeGo);
            })->when($roadWay == 'from' || $roadWay == 'both', function ($query) use ($timeBack) {
                $query->where('time-back', $timeBack);
            })->get()
            ->toArray();

        if (count($dailyRides))
            return false;

        return true;
    }

    public function selectDriver(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lat'               => 'required|string',
            'lng'               => 'required|string',
            'neighborhood_id'   => 'required|numeric',
            'university_id'     => 'required|numeric',
            'driver_id'         => 'required|numeric',
            'ride_type_id'      => 'required|numeric',
            'date'              => 'required|string',
            'time_go'           => 'sometimes|nullable|string',
            'time_back'         => 'sometimes|nullable|string',
            'locale'            => 'sometimes|nullable|string'
        ]);

        if($validator->fails()) {
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);
        }

        $data = $validator->validated();

        $passengerId = auth()->user()->id;
        $rideTypeId = $data['ride_type_id'];
        $neighborhoodId = $data['neighborhood_id'];
        $universityId = $data['university_id'];
        $driverId = $data['driver_id'];

        $service = $this->getService($rideTypeId);
        $roadWay = $service->{"road-way"};

        $lat = $data['lat'];
        $lng = $data['lng'];
        $date = $data['date'];

        $date = convertArabicDateToEnglish($date);
        $date = Carbon::createFromFormat('Y-m-d', $date)->format('Y-m-d');
        $timeBack = isset($data['time_back']) ? convertArabicDateToEnglish($data['time_back']) : null;
        $timeGo =  isset($data['time_go']) ? convertArabicDateToEnglish($data['time_go']) : null;

        $checkSchedule = $this->checkScheduleTime([
            'date'      => $date,
            'time_go'   => $timeGo,
            'time_back' => $timeGo
        ], $roadWay);

        if (!$checkSchedule)
            return $this->sendError(__('Validation Error.'), [ __("Sorry you already booked ride at the same date before")], 422);

        $savingData = [
            'passenger-id'      => $passengerId,
            'neighborhood-id'   => $neighborhoodId,
            'university-id'     => $universityId,
            'service-id'        => $rideTypeId,
            'date-of-ser'       => $date,
            'road-way'          => $roadWay,
            'action'            => 0,
            'lat'               => $lat,
            'lng'               => $lng,
            'date-of-add'       => Carbon::now()
        ];

        $sugDayDrivers = [];

        if ($roadWay == 'to' || $roadWay == 'both') {
            $savingData['time-go'] = $timeGo;
            $savingData['road-way'] = 'to';

            $dayRideBookingGo = DayRideBooking::create($savingData);

            $sugDayDriverGo = SugDayDriver::create([
                'booking-id'        => $dayRideBookingGo->id,
                'driver-id'         => $driverId,
                'passenger-id'      => $passengerId,
                'action'            => 0,
                'date-of-add'       => Carbon::now()
            ]);
            $sugDayDrivers[] = $sugDayDriverGo->id;
        }

        if ($roadWay == 'from' || $roadWay == 'both') {
            unset($savingData['time-go']);
            $savingData['road-way'] = 'from';
            $savingData['time-back'] = $timeBack;

            $dayRideBookingBack = DayRideBooking::create($savingData);

            $sugDayDriverBack = SugDayDriver::create([
                'booking-id'        => $dayRideBookingBack->id,
                'driver-id'         => $driverId,
                'passenger-id'      => $passengerId,
                'action'            => 0,
                'date-of-add'       => Carbon::now()
            ]);
            $sugDayDrivers[] = $sugDayDriverBack->id;
        }

        $sugDayDriver = SugDayDriver::with('driverinfo', 'driver')
            ->whereIn('id', $sugDayDrivers)
            ->get();

        $success = [];
        $success['trip'] = new DayRideBookingResource(isset($dayRideBookingGo) ? $dayRideBookingGo : $dayRideBookingBack);
        $success['sug_day_driver'] = SugDayDrivingResource::collection($sugDayDriver);

        return $this->sendResponse($success, __('Success'));
    }

    public function sendToAllDrivers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'neighborhood_id'   => 'required|numeric',
            'university_id'     => 'required|numeric',
            'ride_type_id'      => 'required|numeric',
            'date'              => 'required|string',
            'time_go'           => 'sometimes|nullable|string',
            'time_back'         => 'sometimes|nullable|string',
            'lat'               => 'required|string',
            'lng'               => 'required|string',
            'locale'            => 'sometimes|nullable|string'
        ]);

        if($validator->fails()) {
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);
        }

        $data = $validator->validated();

        $passengerId = auth()->user()->id;

        $rideTypeId = $data['ride_type_id'];
        $service = $this->getService($rideTypeId);
        $roadWay = $service->{"road-way"};

        $neighborhoodId = $data['neighborhood_id'];
        $universityId = $data['university_id'];
        $lat = $data['lat'];
        $lng = $data['lng'];

        $date = $data['date'];
        $date = convertArabicDateToEnglish($date);
        $date = Carbon::createFromFormat('Y-m-d', $date)->format('Y-m-d');
        $timeBack = isset($data['time_back']) ? convertArabicDateToEnglish($data['time_back']) : null;
        $timeGo =  isset($data['time_go']) ? convertArabicDateToEnglish($data['time_go']) : null;

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
            'lat'               => $lat,
            'lng'               => $lng,
            'date-of-add'       => Carbon::now()
        ]);

        $success['trip'] = new DayRideBookingResource($dayRideBooking);

        return $this->sendResponse($success, __('Success'));
    }

    public function getUserNotification(Request $request)
    {
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
            $message = '';
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

        $suggestedDailyDrivers = SugDayDriver::with('driver', 'booking')
            ->where('passenger-id', $passengerId)
            ->get();

        $suggestedImmediateDrivers = SuggestionDriver::with('driver', 'booking')
            ->where('passenger-id', $passengerId)
            ->get();

        $suggestedDrivers = SuggestionResource::collection($suggestedImmediateDrivers);

        $trips = $suggestedDrivers->merge(SugDayDrivingResource::collection($suggestedDailyDrivers));

        $success = [];
        $success['trips'] = $trips;

        return $this->sendResponse($success, __('Trips'));
    }

    public function executeRide(Request $request)
    {
        $success = array();
        $to = array();
        $from = array();

        $success['sug_day_driver'] = null;
        $success['to'] = $to;
        $success['from'] = $from;
        $success['destination_lat'] = null;
        $success['destination_lng'] = null;
        $success['source_lat'] = null;
        $success['source_lng'] = null;
        $success['estimated_time'] = 0;
        $success['action'] = 'daily/transport/trip';

        $passengerId = auth()->user()->id;
        $nowDate = Carbon::now()->format('Y-m-d');
        $subMinutes = Carbon::now()->subMinutes(5)->format('H:i');
        $addMinutes = Carbon::now()->addMinutes(5)->format('H:i');

        $ride = DayRideBooking::where('date-of-ser', $nowDate)
            ->where(function ($query) use ($subMinutes, $addMinutes) {
                $query->whereBetween('time-go', [$subMinutes, $addMinutes])
                    ->orWhereBetween('time-back', [$subMinutes, $addMinutes]);
            })
            ->where('passenger-id', $passengerId)
            ->first();

        if (!isset($ride->id)) {
            return $this->sendResponse($success, __("Not found trips"));
        }

        $sugDayDriver = SugDayDriver::with('booking', 'driverinfo', 'driverinfo.driver')->where([
            ['booking-id', $ride->id],
            ['action', 3],
            ['passenger-id', $passengerId]
        ])->first();

        if (!$sugDayDriver) {
            return $this->sendResponse($success, __("Not found trips"));
        }

        $success['sug_day_driver'] = new SugDayDrivingResource($sugDayDriver);
        if ($ride->{"road-way"} == 'from') {
            $from['ar'] = $ride->university->{"name-ar"};
            $from['en'] = $ride->university->{"name-eng"};
            $to['ar'] = $ride->neighborhood->{"neighborhood-ar"};
            $to['en'] = $ride->neighborhood->{"neighborhood-eng"};
            $success['destination_lat'] = $ride->lat;
            $success['destination_lng'] = $ride->lng;
            $success['source_lat'] = $ride->university->lat;
            $success['source_lng'] = $ride->university->lng;
        } else {
            $from['ar'] = $ride->neighborhood->{"neighborhood-ar"};
            $from['en'] = $ride->neighborhood->{"neighborhood-eng"};
            $to['ar'] = $ride->university->{"name-ar"};
            $to['en'] = $ride->university->{"name-eng"};
            $success['destination_lat'] = $ride->university->lat;
            $success['destination_lng'] = $ride->university->lng;
            $success['source_lat'] = $ride->lat;
            $success['source_lng'] = $ride->lng;
        }
        $success['to'] = $to;
        $success['from'] = $from;

        sendNotification([
            'title'     => __('You have a notification from Atariqi'),
            'body'      => __("an order from Atariqi to accept the ride"),
            'tokens'    => [auth()->user()->fcm_token]
        ]);

        return $this->sendResponse($success, __("an order from Atariqi to accept the ride"));
    }

    public function getTripDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'        => 'required|numeric',
            'locale'    => 'sometimes|nullable|string'
        ]);

        if($validator->fails())
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);

        $data = $validator->validated();
        $id = $data['id'];

        $sugDayDriver = SugDayDriver::with('booking', 'driverinfo')->whereId($id)
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

        $sugDayDriver = SugDayDriver::with('booking')->where('id', $id)
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
