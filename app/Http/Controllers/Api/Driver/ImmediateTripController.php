<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Driver\SuggestionDriverDetailsResource;
use App\Models\SuggestionDriver;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ImmediateTripController extends BaseController
{
    public function index()
    {
        $trips = SuggestionDriver::with(['deliveryInfo', 'rate', 'booking', 'booking.passenger', 'booking.university'])
            ->where('action', 0)
            ->where('driver-id', auth()->user()->id)
            ->get();

        $filteredTrips = $this->filterTrips($trips);

        Log::info("There is trips: ", $filteredTrips->toArray());

        $filteredTrips = SuggestionDriverDetailsResource::collection($filteredTrips);

        return $this->sendResponse($filteredTrips, __('Data'));
    }

    public function filterTrips($trips)
    {
        $currentTime = Carbon::now()->subMinute();

        Log::info("Filter immediate trips");

        $trips->each(function ($trip) use ($currentTime) {
            Log::info("Loop on Trip with ID: " . $trip->id);
            $dateOfAdd = Carbon::parse($trip->{'date-of-add'});

            if ($currentTime->greaterThan($dateOfAdd)) {
                Log::info("Update Trip with ID: " . $trip->id);
                $trip->update([
                    'action' => 4,
                    'date-of-edit' => Carbon::now()
                ]);
            }

        });

        return $trips->where('action', '!=', 4);
    }
}
