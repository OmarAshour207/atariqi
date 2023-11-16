<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DriverInfoDayRideResource;
use App\Http\Resources\NeighbourResource;
use App\Http\Resources\SugWeekDriverResource;
use App\Http\Resources\UniversityResource;
use App\Http\Resources\WeekRideBookingResource;
use App\Models\DayRideBooking;
use App\Models\DriverInfo;
use App\Models\DriversServices;
use App\Models\Neighbour;
use App\Models\Service;
use App\Models\SugWeekDriver;
use App\Models\University;
use App\Models\User;
use App\Models\WeekRideBooking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WeeklyDriverController extends BaseController
{
    private function getService($id)
    {
        return Service::whereId($id)->first();
    }

    private function getGroupId()
    {
        $weekRideBooking = WeekRideBooking::where('passenger-id', auth()->user()->id)
            ->orderBy('id', 'desc')
            ->first();
        if (!$weekRideBooking)
            return 1;
        return $weekRideBooking->{"group-id"} + 1;
    }

    private function convertDate($dates, $skipFormat = false): array
    {
        $newDates = array();

        foreach ($dates as $date) {
            $date = convertArabicDateToEnglish($date);
            if (!$skipFormat)
                $date = Carbon::createFromFormat('Y-m-d', $date)->format('Y-m-d');
            $newDates[] = $date;
        }
        return $newDates;
    }

    private function saveWeekRideBooking($savingData, $times): void
    {
        $roadWay = $savingData['road-way'];

        $dates = $times['date'];

        for ($i = 0; $i < count($dates);$i++) {
            $savingData['date-of-ser'] = $dates[$i];
            if ($roadWay == 'to') {
                $savingData['time-go'] = $times['time_go'][$i];
                WeekRideBooking::create($savingData);
            } elseif ($roadWay == 'from') {
                $savingData['time-back'] = $times['time_back'][$i];
                WeekRideBooking::create($savingData);
            } elseif ($roadWay == 'both') {
                $savingData['time-go'] = $times['time_go'][$i];
                WeekRideBooking::create($savingData);
                unset($savingData['time-go']);
                $savingData['time-back'] = $times['time_back'][$i];
                WeekRideBooking::create($savingData);
            }

            unset($savingData['time-go']);
            unset($savingData['time-back']);
        }
    }

    private function checkFridayInDate($date): bool
    {
        for ($i = 0;$i < count($date);$i++) {
            $dateDay = Carbon::parse($date[$i])->format('l');
            if ($dateDay == 'Friday')
                return true;
        }
        return false;
    }

    public function getDrivers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'neighborhood_id'   => 'required|numeric',
            'university_id'     => 'required|numeric',
            'ride_type_id'      => 'required|numeric',
            'lat'               => 'required|string',
            'lng'               => 'required|string',
            'date'              => 'required|array',
            'time_go'           => 'sometimes|nullable|array',
            'time_back'         => 'sometimes|nullable|array',
            'locale'            => 'sometimes|nullable|string'
        ]);

        if($validator->fails())
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);

        $data = $validator->validated();

        $passengerId = auth()->user()->id;
        $neighborhoodId = $data['neighborhood_id'];
        $universityId = $data['university_id'];
        $rideTypeId = $data['ride_type_id'];

        $lat = $data['lat'];
        $lng = $data['lng'];

        $service = $this->getService($rideTypeId);
        $roadWay = $service->{"road-way"};

        $date = $data['date'];
        $date = $this->convertDate($date);
        $friday = $this->checkFridayInDate($date);
        if ($friday)
            return $this->sendError(__('Validation Error.'), [ __('Not Available Daily transport in Friday')], 422);

        $timeBack = isset($data['time_back']) ? $data['time_back'] : array();
        $timeBack = $this->convertDate($timeBack, true);

        $timeGo =  isset($data['time_go']) ? $data['time_go'] : array();
        $timeGo = $this->convertDate($timeGo, true);

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
        $success['action'] = 'weekly/transport/trip';

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

        $savingData = [
            'passenger-id'      => $passengerId,
            'neighborhood-id'   => $neighborhoodId,
            'service-id'        => $rideTypeId,
            'university-id'     => $universityId,
            'road-way'          => $roadWay,
            'lat'               => $lat,
            'lng'               => $lng
        ];
        $times = [
            'date'      => $date,
            'time_go'   => $timeGo,
            'time_back' => $timeBack,
        ];

        $uniDrivers = User::select('users.id')
            ->join('university', 'users.university-id', '=', 'university.id')
            ->join('uni-driving-services', function ($join) use ($rideTypeId, $universityId) {
                $join->on('uni-driving-services.university-id', '=', 'users.university-id')
                    ->where('uni-driving-services.service-id', '=', $rideTypeId);
            })
            ->where('users.user-type', '=', 'driver')
            ->where('users.university-id', '=', $universityId)
            ->get()
            ->toArray();

        if (!count($uniDrivers)) {
            $groupId = $this->getGroupId();
            $savingData['group-id'] = $groupId;
            $savingData['action'] = 1;
            $this->saveWeekRideBooking($savingData, $times);
            return $this->sendResponse($success, __('No Drivers to the University right now'));
        }

        $rideTypeDrivers = DriversServices::select('drivers-services.driver-id')
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
            ->whereIn('drivers-services.driver-id', $uniDrivers)
            ->where('drivers-services.service-id', '=', $rideTypeId)
            ->get()
            ->toArray();

        if (!count($rideTypeDrivers)) {
            $groupId = $this->getGroupId();
            $savingData['group-id'] = $groupId;
            $savingData['action'] = 2;
            $this->saveWeekRideBooking($savingData, $times);
            return $this->sendResponse($success, __('No Drivers to this Service right now'));
        }


        $driversSchedule =  DB::table('drivers-schedule')
            ->select("driver-id")
            ->when($roadWay == 'to' || $roadWay == 'both', function ($query) use ($date, $timeGo) {
                for ($i = 0; $i < count($date);$i++) {
                    $dateDay = Carbon::parse($date[$i])->format('l');
                    $query->where("$dateDay-to" , '<=', "$timeGo[$i]")
                        ->whereRaw('`' . "$dateDay-to" . '` + INTERVAL 2 HOUR >= ?', [$timeGo[$i]] );
                }
            })
            ->when($roadWay == 'from' || $roadWay == 'both', function ($query) use ($date, $timeBack) {
                for ($i = 0; $i < count($date);$i++) {
                    $dateDay = Carbon::parse($date[$i])->format('l');
                    $query->where("$dateDay-from" , '>=', "$timeBack[$i]")
                        ->whereRaw('`' . "$dateDay-from" . '` - INTERVAL 2 HOUR <= ?', [$timeBack[$i]] );
                }
            })
            ->whereIn('driver-id', $rideTypeDrivers)
            ->get()
            ->toArray();

        if (!count($driversSchedule)) {
            $groupId = $this->getGroupId();
            $savingData['group-id'] = $groupId;
            $savingData['action'] = 3;
            $this->saveWeekRideBooking($savingData, $times);
            return $this->sendResponse($success, __('No Drivers available right now'));
        }

        $driversIds = array();
        foreach ($driversSchedule as $driverSchedule) {
            $driversIds[] = $driverSchedule->{'driver-id'};
        }

        $dateDays = [];
        for ($i = 0;$i < count($date);$i++) {
            if ($roadWay == 'both') {
                $dateDays[] = Carbon::parse($date[$i])->format('l') . '-to';
                $dateDays[] = Carbon::parse($date[$i])->format('l') . '-from';
            } else
                $dateDays[] = Carbon::parse($date[$i])->format('l') . '-' . $roadWay;
        }

        $dateDays[] = "driver-id";
        $dateDays[] = "id";

        $drivers = DriverInfo::with(['driver', 'schedule' => function($query) use ($dateDays) {
            $query->select($dateDays);
        }])
            ->whereIn('driver-id', $driversIds)
            ->get();

        $success['drivers'] = DriverInfoDayRideResource::collection($drivers);

        return $this->sendResponse($success, __('Drivers'));
    }

    public function selectDriver(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'neighborhood_id'   => 'required|numeric',
            'university_id'     => 'required|numeric',
            'driver_id'         => 'required|numeric',
            'ride_type_id'      => 'required|numeric',
            'date'              => 'required|array',
            'time_go'           => 'sometimes|nullable|array',
            'time_back'         => 'sometimes|nullable|array',
            'lat'               => 'required|string',
            'lng'               => 'required|string'
        ]);

        if($validator->fails())
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);

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
        $date = $this->convertDate($date);
        $friday = $this->checkFridayInDate($date);

        if ($friday)
            return $this->sendError(__('Validation Error.'), [ __('Not Available Daily transport in Friday')], 422);

        $timeBack = isset($data['time_back']) ? $data['time_back'] : array();
        $timeBack = $this->convertDate($timeBack, true);

        $timeGo =  isset($data['time_go']) ? $data['time_go'] : array();
        $timeGo = $this->convertDate($timeGo, true);

        $weekRides = WeekRideBooking::where('passenger-id', $passengerId)
            ->whereIn('date-of-ser', $date)
            ->when($roadWay == 'to' || $roadWay == 'both', function ($query) use ($timeGo) {
                $query->whereIn('time-go', $timeGo);
            })->when($roadWay == 'from' || $roadWay == 'both', function ($query) use ($timeBack) {
                $query->whereIn('time-back', $timeBack);
            })->get()
            ->toArray();

        if (count($weekRides))
            return $this->sendError(__('Validation Error.'), [ __("Sorry we can't book the scheduled week drive for you, because you already have a scheduled ride at date and time to/from university")], 422);

        $dailyRides = DayRideBooking::where('passenger-id', $passengerId)
            ->whereIn('date-of-ser', $date)
            ->when($roadWay == 'to' || $roadWay == 'both', function ($query) use ($timeGo) {
                $query->whereIn('time-go', $timeGo);
            })->when($roadWay == 'from' || $roadWay == 'both', function ($query) use ($timeBack) {
                $query->whereIn('time-back', $timeBack);
            })->get()
            ->toArray();

        if (count($dailyRides))
            return $this->sendError(__('Validation Error.'), [ __("Sorry we can't book the scheduled week drive for you, because you already have a scheduled ride at date and time to/from university")], 422);

        $groupId = $this->getGroupId();
        $savingData = [
            'passenger-id'      => $passengerId,
            'neighborhood-id'   => $neighborhoodId,
            'service-id'        => $rideTypeId,
            'university-id'     => $universityId,
            'road-way'          => $roadWay,
            'lat'               => $lat,
            'lng'               => $lng
        ];
        $times = [
            'date'      => $date,
            'time_go'   => $timeGo,
            'time_back' => $timeBack,
        ];

        $savingData['group-id'] = $groupId;
        $savingData['action'] = 0;

        $this->saveWeekRideBooking($savingData, $times);

        $bookingRides = WeekRideBooking::where('passenger-id', $passengerId)
            ->where('group-id', $groupId)
            ->get();

        foreach ($bookingRides as $booking) {
            SugWeekDriver::create([
                'booking-id'    => $booking->id,
                'driver-id'     => $driverId,
                'action'        => 0,
                'date-of-add'   => Carbon::now(),
                'viewed'        => 0
            ]);
        }
        $success = array();
        $success['trips'] = WeekRideBookingResource::collection($bookingRides);

        return $this->sendResponse($success, __('Driver selected successfully'));
    }

    public function sendToAllDrivers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'neighborhood_id'   => 'required|numeric',
            'university_id'     => 'required|numeric',
            'ride_type_id'      => 'required|numeric',
            'date'              => 'required|array',
            'time_go'           => 'sometimes|nullable|array',
            'time_back'         => 'sometimes|nullable|array',
            'lat'               => 'required|string',
            'lng'               => 'required|string'
        ]);

        if($validator->fails())
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);

        $data = $validator->validated();

        $passengerId = auth()->user()->id;
        $rideTypeId = $data['ride_type_id'];
        $neighborhoodId = $data['neighborhood_id'];
        $universityId = $data['university_id'];

        $service = $this->getService($rideTypeId);
        $roadWay = $service->{"road-way"};

        $lat = $data['lat'];
        $lng = $data['lng'];

        $date = $data['date'];
        $date = $this->convertDate($date);
        $friday = $this->checkFridayInDate($date);

        if ($friday)
            return $this->sendError(__('Validation Error.'), [ __('Not Available Daily transport in Friday')], 422);

        $timeBack = isset($data['time_back']) ? $data['time_back'] : array();
        $timeBack = $this->convertDate($timeBack, true);

        $timeGo =  isset($data['time_go']) ? $data['time_go'] : array();
        $timeGo = $this->convertDate($timeGo, true);

        $weekRides = WeekRideBooking::where('passenger-id', $passengerId)
            ->whereIn('date-of-ser', $date)
            ->when($roadWay == 'to' || $roadWay == 'both', function ($query) use ($timeGo) {
                $query->whereIn('time-go', $timeGo);
            })->when($roadWay == 'from' || $roadWay == 'both', function ($query) use ($timeBack) {
                $query->whereIn('time-back', $timeBack);
            })->get()
            ->toArray();

        if (count($weekRides))
            return $this->sendError(__('Validation Error.'), [ __("Sorry we can't book the scheduled week drive for you, because you already have a scheduled ride at date and time to/from university")], 422);

        $dailyRides = DayRideBooking::where('passenger-id', $passengerId)
            ->whereIn('date-of-ser', $date)
            ->when($roadWay == 'to' || $roadWay == 'both', function ($query) use ($timeGo) {
                $query->whereIn('time-go', $timeGo);
            })->when($roadWay == 'from' || $roadWay == 'both', function ($query) use ($timeBack) {
                $query->whereIn('time-back', $timeBack);
            })->get()
            ->toArray();

        if (count($dailyRides))
            return $this->sendError(__('Validation Error.'), [ __("Sorry we can't book the scheduled week drive for you, because you already have a scheduled ride at date and time to/from university")], 422);

        $groupId = $this->getGroupId();
        $savingData = [
            'passenger-id'      => $passengerId,
            'neighborhood-id'   => $neighborhoodId,
            'service-id'        => $rideTypeId,
            'university-id'     => $universityId,
            'road-way'          => $roadWay,
            'lat'               => $lat,
            'lng'               => $lng
        ];
        $times = [
            'date'      => $date,
            'time_go'   => $timeGo,
            'time_back' => $timeBack,
        ];

        $savingData['group-id'] = $groupId;
        $savingData['action'] = 4;

        $this->saveWeekRideBooking($savingData, $times);

        $bookingRides = WeekRideBooking::where('passenger-id', $passengerId)
            ->where('group-id', $groupId)
            ->get();

        $success = array();
        $success['trips'] = WeekRideBookingResource::collection($bookingRides);

        return $this->sendResponse($success, __('Sent to all Drivers successfully'));
    }

    public function getUserNotification()
    {
        $passengerId = auth()->user()->id;

        $suggestedDrivers = SugWeekDriver::with('driver', 'booking')
            ->where('passenger-id', $passengerId)
            ->where('viewed', 0)
            ->get();

        $success = [];
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
                $message = '';
            }
        }

        $success['messages'] = $messages;
        return $this->sendResponse($success, __('Trips'));
    }

    public function getUserSummary()
    {
        $passengerId = auth()->user()->id;

        $suggestedDrivers = SugWeekDriver::with('driver', 'booking')
            ->where('passenger-id', $passengerId)
            ->get();

        $success = [];
        $success['trips'] = SugWeekDriverResource::collection($suggestedDrivers);

        return $this->sendResponse($success, __('Trips'));
    }

    public function executeRide()
    {
        $success = array();
        $to = array();
        $from = array();

        $success['sug_week_driver'] = null;
        $success['to'] = $to;
        $success['from'] = $from;
        $success['destination_lat'] = null;
        $success['destination_lng'] = null;
        $success['source_lat'] = null;
        $success['source_lng'] = null;
        $success['estimated_time'] = 0;
        $success['action'] = 'weekly/transport/trip';

        $passengerId = auth()->user()->id;
        $nowDate = Carbon::now()->format('Y-m-d');
        $subMinutes = Carbon::now()->subMinutes(5)->format('H:i');
        $addMinutes = Carbon::now()->addMinutes(5)->format('H:i');

        $ride = WeekRideBooking::where('date-of-ser', $nowDate)
            ->where(function ($query) use ($subMinutes, $addMinutes) {
                $query->whereBetween('time-go', [$subMinutes, $addMinutes])
                    ->orWhereBetween('time-back', [$subMinutes, $addMinutes]);
            })
            ->where('passenger-id', $passengerId)
            ->first();

        if (!isset($ride->id))
            return $this->sendResponse($success, __("Not found trips"));

        $sugDayDriver = SugWeekDriver::with('booking', 'driverinfo')->where([
            ['booking-id', $ride->id],
            ['action', 3],
            ['passenger-id', $passengerId]
        ])->first();

        $success['sug_day_driver'] = new SugWeekDriverResource($sugDayDriver);
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
        ]);

        if($validator->fails())
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);

        $id = $validator->validated()['id'];

        $sugWeekDriver = SugWeekDriver::with('booking', 'driverinfo')
            ->whereId($id)
            ->where('passenger-id', auth()->user()->id)
            ->first();

        if (!$sugWeekDriver)
            return $this->sendError(__('Trip not found!'), [__('Trip not found')]);

        return $this->sendResponse(new SugWeekDriverResource($sugWeekDriver), __('Success'));
    }

    public function changeAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sug_week_driver_id'=> 'required|numeric',
            'action'            => 'required|numeric',
        ]);

        if($validator->fails())
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);

        $data = $validator->validated();
        $action = $data['action'];
        $id = (int) $data['sug_week_driver_id'];

        $sugWeekDriver = SugWeekDriver::with('booking')
            ->whereId($id)
            ->where('passenger-id', auth()->user()->id)
            ->first();

        if (!$sugWeekDriver)
            return $this->sendError(__('Trip not found!'), [__('Trip not found')]);

        $sugWeekDriver->update(['action' => $action]);

        $success = array();
        $success['sug_day_drivers'] = new SugWeekDriverResource($sugWeekDriver);

        return $this->sendResponse($success, __('Success'));
    }

}