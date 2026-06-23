<?php

namespace App\Support;

class TripLocationFormatter
{
    public static function resolveFromBooking($booking): ?array
    {
        if (!$booking) {
            return null;
        }

        $roadWay = $booking->{'road-way'};

        $universityEndpoint = self::buildUniversityEndpoint($booking);
        $pointEndpoint = self::buildPointEndpoint($booking);

        if ($roadWay === 'from') {
            return [
                'from' => $universityEndpoint,
                'to' => $pointEndpoint,
            ];
        }

        return [
            'from' => $pointEndpoint,
            'to' => $universityEndpoint,
        ];
    }

    private static function buildUniversityEndpoint($booking): array
    {
        $university = $booking->university;

        return [
            'type' => 'university',
            'label' => __('University'),
            'name' => $university?->{'name-ar'} ?? $university?->{'name-eng'} ?? __('Not Specified'),
            'address' => $university?->location,
            'lat' => $university?->lat,
            'lng' => $university?->lng,
        ];
    }

    private static function buildPointEndpoint($booking): array
    {
        $neighborhood = $booking->neighborhood;
        $neighborhoodName = $neighborhood?->{'neighborhood-ar'} ?? $neighborhood?->{'neighborhood-en'};

        return [
            'type' => 'point',
            'label' => __('Pickup/Dropoff Point'),
            'name' => $neighborhoodName ?: __('Not Specified'),
            'address' => $booking->location ?? null,
            'lat' => $booking->lat ?? null,
            'lng' => $booking->lng ?? null,
        ];
    }
}
