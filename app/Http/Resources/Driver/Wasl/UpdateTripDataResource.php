<?php

namespace App\Http\Resources\Driver\Wasl;

use App\Models\City;
use App\Services\WaslService;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class UpdateTripDataResource extends JsonResource
{
    public function __construct($resource, protected ?float $customerRating = null)
    {
        parent::__construct($resource);
    }

    public function toArray($request)
    {
        $booking = $this->booking;
        $delivery = $this->deliveryInfo;
        $driverInfo = $this->driverinfo;
        $roadWay = $booking?->{"road-way"} ?? 'from';

        $originLat = $roadWay === 'from' ? $booking?->university?->lat : $booking?->lat;
        $originLng = $roadWay === 'from' ? $booking?->university?->lng : $booking?->lng;
        $destinationLat = $roadWay === 'from' ? $booking?->lat : $booking?->university?->lat;
        $destinationLng = $roadWay === 'from' ? $booking?->lng : $booking?->university?->lng;

        $universityCity = $this->resolveCityName(
            $booking?->university?->cityUni?->{'city-ar'} ?? $booking?->university?->city
        );
        $neighborhoodCityId = $booking?->neighborhood?->city_id ?? $booking?->neighborhood?->{'city-id'};
        $neighborhoodCity = $this->resolveCityName(
            $neighborhoodCityId ? City::find($neighborhoodCityId)?->{'city-ar'} : null
        ) ?: $universityCity;

        $originCity = $roadWay === 'from' ? $universityCity : $neighborhoodCity;
        $destinationCity = $roadWay === 'from' ? $neighborhoodCity : $universityCity;

        $tripDate = $this->resolveTripDate();
        $pickupAt = $this->combineDateAndTime($tripDate, $delivery?->{"arrived-location"});
        $dropoffAt = $this->combineDateAndTime($tripDate, $delivery?->{"arrived-destination"});
        $expectArrivedAt = $this->combineDateAndTime($tripDate, $delivery?->{"expect-arrived"});
        $assignAt = Carbon::parse($this->{"date-of-add"} ?? now());

        return [
            'sequenceNumber' => (string) ($driverInfo?->{"sequence-number"} ?? ''),
            'driverId' => (string) ($driverInfo?->identity_number ?? ''),
            'tripId' => (string) $this->id,
            'distanceInMeters' => $this->calculateDistance($originLat, $originLng, $destinationLat, $destinationLng),
            'durationInSeconds' => $this->secondsBetween($pickupAt, $dropoffAt),
            'customerRating' => (float) ($this->customerRating ?? $delivery?->{"passenger-rate"} ?? 0),
            'customerWaitingTimeInSeconds' => $this->secondsBetween($expectArrivedAt, $pickupAt),
            'originCityNameInArabic' => $originCity,
            'destinationCityNameInArabic' => $destinationCity,
            'originLatitude' => (float) ($originLat ?? 0),
            'originLongitude' => (float) ($originLng ?? 0),
            'destinationLatitude' => (float) ($destinationLat ?? 0),
            'destinationLongitude' => (float) ($destinationLng ?? 0),
            'pickupTimestamp' => $this->formatWaslTimestamp($pickupAt),
            'dropoffTimestamp' => $this->formatWaslTimestamp($dropoffAt),
            'startedWhen' => $this->formatWaslTimestamp($pickupAt),
            'tripCost' => $this->resolveTripCost(),
            'driverArrivalTime' => $this->formatWaslTimestamp($pickupAt),
            'driverAssignTime' => $this->formatWaslTimestamp($assignAt),
            'provinceId' => app(WaslService::class)->resolveProvinceIdForTrip(
                $originCity,
                $destinationCity
            ),
        ];
    }

    private function resolveTripDate(): Carbon
    {
        $bookingDate = $this->booking?->{"date-of-ser"} ?? $this->booking?->{"date-of-add"};

        if ($bookingDate) {
            return Carbon::parse($bookingDate);
        }

        return Carbon::parse($this->{"date-of-add"} ?? now());
    }

    private function combineDateAndTime(Carbon $date, $time): ?Carbon
    {
        if (!$time) {
            return null;
        }

        return Carbon::parse($date->format('Y-m-d') . ' ' . Carbon::parse($time)->format('H:i:s'));
    }

    private function formatWaslTimestamp(?Carbon $timestamp): ?string
    {
        return $timestamp?->format('Y-m-d\TH:i:s.000');
    }

    private function secondsBetween(?Carbon $from, ?Carbon $to): int
    {
        if (!$from || !$to) {
            return 0;
        }

        return abs($to->diffInSeconds($from));
    }

    private function resolveCityName(?string $city): string
    {
        return trim((string) ($city ?? ''));
    }

    private function resolveTripCost(): float
    {
        $package = $this->passenger?->activePackage;

        if (!$package) {
            return 0.0;
        }

        return (float) ($package->price_monthly ?? $package->price_annual ?? 0);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2): int
    {
        if (!$lat1 || !$lon1 || !$lat2 || !$lon2) {
            return 0;
        }

        $earthRadius = 6371000;

        $lat1Rad = deg2rad((float) $lat1);
        $lon1Rad = deg2rad((float) $lon1);
        $lat2Rad = deg2rad((float) $lat2);
        $lon2Rad = deg2rad((float) $lon2);

        $deltaLat = $lat2Rad - $lat1Rad;
        $deltaLon = $lon2Rad - $lon1Rad;

        $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
            cos($lat1Rad) * cos($lat2Rad) *
            sin($deltaLon / 2) * sin($deltaLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return (int) round($earthRadius * $c);
    }
}
