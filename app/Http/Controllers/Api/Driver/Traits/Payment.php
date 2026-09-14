<?php

namespace App\Http\Controllers\Api\Driver\Traits;

use App\Models\Service;
use App\Models\Subscription;
use App\Models\SugDayDriver;
use App\Models\SugWeekDriver;
use App\Models\SuggestionDriver;

trait Payment
{
    public $servicesCost;

    public function getServicesCost()
    {
        if($this->servicesCost) {
            return $this->servicesCost;
        }
        return Service::select('cost', 'id')->whereIn('id', [1, 6, 8])->pluck('cost', 'id');
    }

    public function getRevenue($userId, $dates)
    {
        $detailed = $this->getDetailedRevenue($userId, $dates);

        return [
            'immediate' => $detailed['immediate']['revenue'],
            'daily' => $detailed['daily']['revenue'],
            'weekly' => $detailed['weekly']['revenue'],
            'total' => $detailed['total'],
            'total_dues' => $detailed['total_dues'],
        ];
    }

    public function getDuesAmount($userId, $dates): float
    {
        return (float) $this->getDetailedRevenue($userId, $dates)['total_dues'];
    }

    public function getDetailedRevenue($userId, $dates): array
    {
        $endDate = isset($dates['end_date'])
            ? date('Y-m-d H:i:s', strtotime($dates['end_date'] . ' 23:59:59'))
            : null;

        $immediateTrips = SuggestionDriver::where('driver-id', $userId)
            ->where('action', 5)
            ->when($dates['start_date'] ?? null, function ($query, $startDate) {
                $query->where('date-of-add', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                $query->where('date-of-add', '<=', $endDate);
            })
            ->with('booking.service')
            ->get();

        $dailyTrips = SugDayDriver::where('driver-id', $userId)
            ->where('action', 6)
            ->when($dates['start_date'] ?? null, function ($query, $startDate) {
                $query->whereHas('booking', function ($bookingQuery) use ($startDate) {
                    $bookingQuery->whereDate('date-of-ser', '>=', $startDate);
                });
            })
            ->when($dates['end_date'] ?? null, function ($query, $endDate) {
                $query->whereHas('booking', function ($bookingQuery) use ($endDate) {
                    $bookingQuery->whereDate('date-of-ser', '<=', $endDate);
                });
            })
            ->with('booking.service')
            ->get();

        $weeklyTrips = SugWeekDriver::where('driver-id', $userId)
            ->where('action', 6)
            ->when($dates['start_date'] ?? null, function ($query, $startDate) {
                $query->whereHas('booking', function ($bookingQuery) use ($startDate) {
                    $bookingQuery->whereDate('date-of-ser', '>=', $startDate);
                });
            })
            ->when($dates['end_date'] ?? null, function ($query, $endDate) {
                $query->whereHas('booking', function ($bookingQuery) use ($endDate) {
                    $bookingQuery->whereDate('date-of-ser', '<=', $endDate);
                });
            })
            ->with('booking.service')
            ->get();

        $fallbackPercentage = Subscription::generalDuesPercentageValue();

        $summarize = function ($trips) use ($fallbackPercentage) {
            $revenue = 0.0;
            $dues = 0.0;

            foreach ($trips as $trip) {
                $cost = (float) ($trip->booking?->service?->cost ?? 0);
                $percentage = $trip->atariqi_percentage !== null
                    ? (float) $trip->atariqi_percentage
                    : $fallbackPercentage;

                $revenue += $cost;
                $dues += ($cost * $percentage) / 100;
            }

            return [
                'count' => $trips->count(),
                'revenue' => $revenue,
                'dues' => $dues,
            ];
        };

        $immediate = $summarize($immediateTrips);
        $daily = $summarize($dailyTrips);
        $weekly = $summarize($weeklyTrips);

        return [
            'immediate' => $immediate,
            'daily' => $daily,
            'weekly' => $weekly,
            'total' => $immediate['revenue'] + $daily['revenue'] + $weekly['revenue'],
            'total_dues' => $immediate['dues'] + $daily['dues'] + $weekly['dues'],
        ];
    }
}
