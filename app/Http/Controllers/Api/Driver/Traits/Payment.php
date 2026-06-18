<?php

namespace App\Http\Controllers\Api\Driver\Traits;

use App\Models\Service;
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
        ];
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

        $immediateRevenue = $immediateTrips->sum(fn ($trip) => (float) ($trip->booking?->service?->cost ?? 0));
        $dailyRevenue = $dailyTrips->sum(fn ($trip) => (float) ($trip->booking?->service?->cost ?? 0));
        $weeklyRevenue = $weeklyTrips->sum(fn ($trip) => (float) ($trip->booking?->service?->cost ?? 0));

        return [
            'immediate' => [
                'count' => $immediateTrips->count(),
                'revenue' => $immediateRevenue,
            ],
            'daily' => [
                'count' => $dailyTrips->count(),
                'revenue' => $dailyRevenue,
            ],
            'weekly' => [
                'count' => $weeklyTrips->count(),
                'revenue' => $weeklyRevenue,
            ],
            'total' => $immediateRevenue + $dailyRevenue + $weeklyRevenue,
        ];
    }
}
