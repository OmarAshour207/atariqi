<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Driver\SugDayDriverResource;
use App\Http\Resources\Driver\SugWeeklyDriverResource;
use App\Http\Resources\SuggestionDriver as SuggestionDriverResource;
use App\Models\SugDayDriver;
use App\Models\SugWeekDriver;
use App\Models\SuggestionDriver;

class TripController extends BaseController
{
    public function getCurrent()
    {
        $userType = auth()->user()->{"user-type"};

        $trip = SuggestionDriver::with('booking', 'passenger', 'deliveryInfo', 'booking.university', 'booking.passenger', 'rate')
            ->whereIn('action', [1, 2])
            ->where("$userType-id", auth()->user()->id)
            ->first();
        $result = new SuggestionDriverResource($trip);

        if (!$trip) {
            $trip = SugDayDriver::with('booking', 'passenger', 'deliveryInfo', 'booking.university', 'booking.passenger', 'rate')
                ->where('action', 4)
                ->where("$userType-id", auth()->user()->id)
                ->first();
            $result = new SugDayDriverResource($trip);
        }

        if (!$trip) {
            $trip = SugWeekDriver::with('booking', 'passenger', 'deliveryInfo', 'booking.university', 'booking.passenger', 'rate')
                ->where('action', 4)
                ->where("$userType-id", auth()->user()->id)
                ->first();
            $result = new SugWeeklyDriverResource($trip);
        }

        if (!$trip) {
            return $this->sendResponse([], __('Data'));
        }

        $result = $result->resolve();

        $roadWay = $trip->booking->{"road-way"};

        $result['destination_lat'] = $roadWay == 'from' ? $trip->booking->lat : $trip->booking->university->lat;
        $result['destination_lng'] = $roadWay == 'from' ? $trip->booking->lng : $trip->booking->university->lng;
        $result['source_lat'] = $roadWay == 'from' ? $trip->booking->university->lat : $trip->booking->lat;
        $result['source_lng'] = $roadWay == 'from' ? $trip->booking->university->lng : $trip->booking->lng;

        return $this->sendResponse($result, __('Data'));
    }
}
