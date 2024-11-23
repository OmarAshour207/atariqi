<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Driver\SugDayDriverResource;
use App\Http\Resources\Driver\SugWeeklyDriverResource;
use App\Http\Resources\Trip\SuggestDailyCurrentResource;
use App\Http\Resources\Trip\SuggestDriverCurrentResource;
use App\Http\Resources\Trip\SuggestWeeklyCurrentResource;
use App\Models\SugDayDriver;
use App\Models\SugWeekDriver;
use App\Models\SuggestionDriver;

class TripController extends BaseController
{
    public function getPassengerTrips()
    {
        $immediateTrips = SuggestionDriver::with([
            'booking', 'passenger',
            'deliveryInfo', 'booking.university',
            'booking.passenger', 'rate',
            'driver', 'driverinfo'
            ])
            ->whereIn('action', [1, 2])
            ->where("passenger-id", auth()->user()->id)
            ->get();

        $result['immediate'] = SuggestDriverCurrentResource::collection($immediateTrips);

        $dailyTrips = SugDayDriver::with('booking', 'passenger', 'deliveryInfo', 'booking.university', 'booking.passenger', 'rate')
            ->where('action', 4)
            ->where("passenger-id", auth()->user()->id)
            ->get();

        $result['daily'] = SuggestDailyCurrentResource::collection($dailyTrips);

        $weeklyTrips = SugWeekDriver::with('booking', 'passenger', 'deliveryInfo', 'booking.university', 'booking.passenger', 'rate')
            ->where('action', 4)
            ->where("passenger-id", auth()->user()->id)
            ->get();

        $result['weekly'] = SuggestWeeklyCurrentResource::collection($weeklyTrips);

        return $this->sendResponse($result, __('Data'));
    }

    public function getDriverTrips()
    {
        $immediateTrips = SuggestionDriver::with([
            'booking', 'passenger',
            'deliveryInfo', 'booking.university',
            'booking.passenger', 'rate',
            'driver', 'driverinfo'
        ])
            ->whereIn('action', [1, 2])
            ->where("driver-id", auth()->user()->id)
            ->get();

        $result['immediate'] = \App\Http\Resources\SuggestionDriver::collection($immediateTrips);

        $dailyTrips = SugDayDriver::with('booking', 'passenger', 'deliveryInfo', 'booking.university', 'booking.passenger', 'rate')
            ->where('action', 4)
            ->where("driver-id", auth()->user()->id)
            ->get();

        $result['daily'] = SugDayDriverResource::collection($dailyTrips);

        $weeklyTrips = SugWeekDriver::with('booking', 'passenger', 'deliveryInfo', 'booking.university', 'booking.passenger', 'rate')
            ->where('action', 4)
            ->where("driver-id", auth()->user()->id)
            ->get();

        $result['weekly'] = SugWeeklyDriverResource::collection($weeklyTrips);

        return $this->sendResponse($result, __('Data'));
    }
}
