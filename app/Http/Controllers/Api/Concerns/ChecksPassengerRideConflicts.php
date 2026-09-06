<?php

namespace App\Http\Controllers\Api\Concerns;

use App\Models\DayRideBooking;
use App\Models\WeekRideBooking;
use Carbon\Carbon;

trait ChecksPassengerRideConflicts
{
    protected function cleanupFailedSearchDailyBookings(int $passengerId, ?string $date = null): void
    {
        $query = DayRideBooking::where('passenger-id', $passengerId)
            ->whereIn('action', [1, 2, 3])
            ->whereDoesntHave('sugDriver');

        if ($date) {
            $query->whereDate('date-of-ser', $date);
        }

        $query->delete();
    }

    /**
     * Returns conflict details when the passenger already has an active ride
     * on the same date with an overlapping time for the requested direction.
     */
    protected function findPassengerRideConflict(
        int $passengerId,
        string $date,
        string $roadWay,
        ?string $timeGo,
        ?string $timeBack,
        int $timeWindowHours = 4
    ): array {
        $this->cleanupFailedSearchDailyBookings($passengerId, $date);

        $legs = match ($roadWay) {
            'to' => [['field' => 'time-go', 'time' => $timeGo]],
            'from' => [['field' => 'time-back', 'time' => $timeBack]],
            'both' => [
                ['field' => 'time-go', 'time' => $timeGo],
                ['field' => 'time-back', 'time' => $timeBack],
            ],
            default => [],
        };

        foreach ($legs as $leg) {
            if (! filled($leg['time'])) {
                continue;
            }

            $dailyRide = $this->findActiveDailyConflict(
                $passengerId,
                $date,
                $leg['field'],
                $leg['time'],
                $timeWindowHours
            );

            if ($dailyRide) {
                return [
                    'time_go' => $dailyRide->{'time-go'},
                    'time_back' => $dailyRide->{'time-back'},
                    'date' => $dailyRide->{'date-of-ser'},
                    'type' => 'daily',
                    'road_way' => $dailyRide->{'road-way'},
                ];
            }

            $weeklyRide = $this->findActiveWeeklyConflict(
                $passengerId,
                $date,
                $leg['field'],
                $leg['time'],
                $timeWindowHours
            );

            if ($weeklyRide) {
                return [
                    'time_go' => $weeklyRide->{'time-go'},
                    'time_back' => $weeklyRide->{'time-back'},
                    'date' => $weeklyRide->{'date-of-ser'},
                    'type' => 'weekly',
                    'road_way' => $weeklyRide->{'road-way'},
                ];
            }
        }

        return [];
    }

    private function findActiveDailyConflict(
        int $passengerId,
        string $date,
        string $timeField,
        string $time,
        int $timeWindowHours
    ): ?DayRideBooking {
        [$from, $to] = $this->timeWindowBounds($time, $timeWindowHours);

        return $this->activeDailyBookingsQuery($passengerId, $date)
            ->whereNotNull($timeField)
            ->whereBetween($timeField, [$from, $to])
            ->first();
    }

    private function findActiveWeeklyConflict(
        int $passengerId,
        string $date,
        string $timeField,
        string $time,
        int $timeWindowHours
    ): ?WeekRideBooking {
        [$from, $to] = $this->timeWindowBounds($time, $timeWindowHours);

        return WeekRideBooking::where('passenger-id', $passengerId)
            ->whereDate('date-of-ser', $date)
            ->whereNotNull($timeField)
            ->whereBetween($timeField, [$from, $to])
            ->first();
    }

    private function activeDailyBookingsQuery(int $passengerId, string $date)
    {
        return DayRideBooking::where('passenger-id', $passengerId)
            ->whereDate('date-of-ser', $date)
            ->where(function ($query) {
                $query->where('action', 4)
                    ->orWhere(function ($active) {
                        $active->whereNotIn('action', [1, 2, 3])
                            ->whereHas('sugDriver', function ($sug) {
                                $sug->where('action', '!=', 2);
                            });
                    });
            });
    }

    private function timeWindowBounds(string $time, int $hours): array
    {
        $parsed = Carbon::parse($time);

        $from = $parsed->copy()->subHours($hours);
        if ($from->lt($parsed->copy()->startOfDay())) {
            $from = $parsed->copy()->startOfDay();
        }

        $to = $parsed->copy()->addHours($hours);
        if ($to->gt($parsed->copy()->endOfDay())) {
            $to = $parsed->copy()->endOfDay();
        }

        return [$from->format('H:i:s'), $to->format('H:i:s')];
    }
}
