<?php

namespace App\Http\Resources\Driver\Wasl;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class UpdateTripDataResource extends JsonResource
{
    public function toArray($request)
    {
        $originLatitude = $this->{"road-way"} == 'from' ? $this->booking->university->lat : $this->booking->lat;
        $originLongitude = $this->{"road-way"} == 'from' ? $this->booking->university->lng : $this->booking->lng;
        $destinationLatitude = $this->{"road-way"} == 'from' ? $this->booking->lat : $this->booking->university->lat;
        $destinationLongitude = $this->{"road-way"} == 'from' ? $this->booking->lng : $this->booking->university->lng;

        $data = [
            "sequenceNumber" => $this->driverinfo->{"sequence-number"},
            'driverId' => $this->driverinfo->identity_number,
            'tripId' => $this->id,

            'distanceInMeters' => $this->calculateDistance($originLatitude, $originLongitude, $destinationLatitude, $destinationLongitude),
            'durationInSeconds' => Carbon::parse($this->deliveryInfo->{"arrived-destination"})->diffInSeconds(Carbon::parse($this->deliveryInfo->{"arrived-location"})),

            'customerRating' => 5.0,//$this->deliveryInfo->{"arrived-destination"},
            'customerWaitingTimeInSeconds' => Carbon::parse($this->deliveryInfo->{"expect-arrived"})->diffInSeconds(Carbon::parse($this->deliveryInfo->{"arrived-location"})),
            'originCityNameInArabic' => $this->{"road-way"} == 'from' ? $this->booking->university->{"name-ar"} : $this->booking->neighborhood->{"neighborhood-ar"},
            'destinationCityNameInArabic' => $this->{"road-way"} == 'from' ? $this->booking->neighborhood->{"neighborhood-ar"} : $this->booking->university->{"name-ar"},
            'originLatitude' => $this->{"road-way"} == 'from' ? $this->booking->university->lat : $this->booking->lat,
            'originLongitude' => $this->{"road-way"} == 'from' ? $this->booking->university->lng : $this->booking->lng,
            'destinationLatitude' => $this->{"road-way"} == 'from' ? $this->booking->lat : $this->booking->university->lat,
            'destinationLongitude' => $this->{"road-way"} == 'from' ? $this->booking->lng : $this->booking->university->lng,
            'pickupTimestamp' => Carbon::parse($this->deliveryInfo->{"arrived-location"})->format('c'),
            'dropoffTimestamp' => Carbon::parse($this->deliveryInfo->{"arrived-destination"})->format('c'),
            'startedWhen' => Carbon::parse($this->deliveryInfo->{"arrived-location"})->format('c'),
            'tripCost' => (double) ($this->passenger->packages->first()->cost ?? 0),
            'driverArrivalTime' => Carbon::parse($this->deliveryInfo->{"arrived-location"})->format('c'),
            'driverAssignTime' => Carbon::parse($this->{"date-of-add"})->format('c')
        ];

        return $data;
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371000; // Earth's radius in meters

        $lat1Rad = deg2rad($lat1);
        $lon1Rad = deg2rad($lon1);
        $lat2Rad = deg2rad($lat2);
        $lon2Rad = deg2rad($lon2);

        $deltaLat = $lat2Rad - $lat1Rad;
        $deltaLon = $lon2Rad - $lon1Rad;

        $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
             cos($lat1Rad) * cos($lat2Rad) *
             sin($deltaLon / 2) * sin($deltaLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return (integer) ($earthRadius * $c);
    }
}
