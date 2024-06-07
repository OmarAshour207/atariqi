<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Api\BaseController;
use App\Models\SuggestionDriver;
use Carbon\Carbon;

class ImmediateTripController extends BaseController
{
    public function index()
    {
        $trips = SuggestionDriver::with(['deliveryInfo', 'rate', 'booking', 'booking.passenger'])
            ->where('action', 0)
            ->where('driver-id', auth()->user()->id)
            ->get();

        $filteredTrips = $this->filterTrips($trips);
        $filteredTrips = \App\Http\Resources\SuggestionDriver::collection($filteredTrips);

        return $this->sendResponse($filteredTrips, __('Data'));
    }

    public function filterTrips($trips)
    {
        $currentTime = Carbon::now()->subMinute();

        $trips->each(function ($trip) use ($currentTime) {
            $dateOfAdd = Carbon::parse($trip->{'date-of-add'});

            if ($currentTime->greaterThan($dateOfAdd)) {
                $trip->update([
                    'action' => 4,
                    'date-of-edit' => Carbon::now()
                ]);
            }

        });

        return $trips->where('action', '!=', 4);
    }
}
