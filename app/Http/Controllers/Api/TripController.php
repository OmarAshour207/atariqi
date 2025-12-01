<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Driver\SugDayDriverResource;
use App\Http\Resources\Driver\SugWeeklyDriverResource;
use App\Http\Resources\Trip\SuggestDailyCurrentResource;
use App\Http\Resources\Trip\SuggestDriverCurrentResource;
use App\Http\Resources\Trip\SuggestWeeklyCurrentResource;
use App\Models\DayRideBooking;
use App\Models\RideBooking;
use App\Models\SugDayDriver;
use App\Models\SugWeekDriver;
use App\Models\SuggestionDriver;
use App\Models\WeekRideBooking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TripController extends BaseController
{
    public function getPassengerTrips()
    {
        $today = Carbon::today()->format('Y-m-d');

        $immediateTrips = SuggestionDriver::with([
            'booking', 'passenger',
            'deliveryInfo', 'booking.university',
            'booking.passenger', 'rate',
            'driver', 'driverinfo'
            ])
            ->whereHas('booking', function ($query) use ($today) {
                $query->whereDate('ride-booking.date-of-add', '=', $today);
            })
            ->whereIn('action', [1, 2])
            ->where("passenger-id", auth()->user()->id)
            ->get();

        $result['immediate'] = SuggestDriverCurrentResource::collection($immediateTrips);

        $dailyTrips = SugDayDriver::with([
            'booking', 'passenger',
            'driverinfo', 'deliveryInfo',
            'booking.university', 'booking.passenger',
            'rate'
            ])
            ->whereHas('booking', function ($query) use ($today) {
                $query->whereDate('day-ride-booking.date-of-ser', '=', $today);
            })
            ->where('action', 4)
            ->where("passenger-id", auth()->user()->id)
            ->get();

        $result['daily'] = SuggestDailyCurrentResource::collection($dailyTrips);

        $weeklyTrips = SugWeekDriver::with('booking', 'passenger', 'deliveryInfo', 'driverinfo','booking.university', 'booking.passenger', 'rate')
            ->whereHas('booking', function ($query) use ($today) {
                $query->whereDate('week-ride-booking.date-of-ser', '=', $today);
            })
            ->where('action', 4)
            ->where("passenger-id", auth()->user()->id)
            ->get();

        $result['weekly'] = SuggestWeeklyCurrentResource::collection($weeklyTrips);

        return $this->sendResponse($result, __('Data'));
    }

    public function getDriverTrips()
    {
        $today = Carbon::today()->format('Y-m-d');

        $immediateTrips = SuggestionDriver::with([
            'booking', 'passenger',
            'deliveryInfo', 'booking.university',
            'booking.passenger', 'rate',
            'driver', 'driverinfo'
        ])
            ->whereHas('booking', function ($query) use ($today) {
                $query->whereDate('ride-booking.date-of-add', '=', $today);
            })
            ->whereIn('action', [1, 2])
            ->where("driver-id", auth()->user()->id)
            ->get();

        $result['immediate'] = \App\Http\Resources\SuggestionDriver::collection($immediateTrips);

        $dailyTrips = SugDayDriver::with('booking', 'passenger', 'deliveryInfo', 'booking.university', 'booking.passenger', 'rate')
            ->whereHas('booking', function ($query) use ($today) {
                $query->whereDate('day-ride-booking.date-of-ser', '=', $today);
            })
            ->where('action', 4)
            ->where("driver-id", auth()->user()->id)
            ->get();

        $result['daily'] = SugDayDriverResource::collection($dailyTrips);

        $weeklyTrips = SugWeekDriver::with('booking', 'passenger', 'deliveryInfo', 'booking.university', 'booking.passenger', 'rate')
           ->whereHas('booking', function ($query) use ($today) {
                $query->whereDate('week-ride-booking.date-of-ser', '=', $today);
            })
            ->where('action', 4)
            ->where("driver-id", auth()->user()->id)
            ->get();

        $result['weekly'] = SugWeeklyDriverResource::collection($weeklyTrips);

        if (count($result['weekly'])) {
            foreach ($result['weekly'] as $index => $weeklyTrip) {
                $groupBookingCount = WeekRideBooking::where('group-id', $weeklyTrip->booking->{"group-id"})->count();
                $trip = $result['weekly'][$index]->resolve();
                $trip['trips_count'] = $groupBookingCount;
                $result['weekly'][$index] = collect($trip);
            }
        }

        return $this->sendResponse($result, __('Data'));
    }

    public function updateLocation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trip_id'   => 'required|numeric',
            'type'      => 'required|string',
            'lat'       => 'required|string',
            'lng'       => 'required|string'
        ]);

        if($validator->fails()) {
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);
        }

        $data = $validator->validated();

        if ($data['type'] == 'immediate') {
            $trip = RideBooking::where('id', $data['trip_id'])->first();
        } elseif ($data['type'] == 'daily') {
            $trip = DayRideBooking::where('id', $data['trip_id'])->first();
        } elseif ($data['type'] == 'weekly') {
            $trip = WeekRideBooking::where('id', $data['trip_id'])->first();
        } else {
            return $this->sendError(__('Invalid trip type'), [], 422);
        }

        $trip->update([
            'current-lat' => $data['lat'],
            'current-lng' => $data['lng'],
        ]);

        return $this->sendResponse([], __('Location updated successfully'));
    }
}
