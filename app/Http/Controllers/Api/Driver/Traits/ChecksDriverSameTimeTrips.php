<?php

namespace App\Http\Controllers\Api\Driver\Traits;

use App\Models\SugDayDriver;
use App\Models\SugWeekDriver;

trait ChecksDriverSameTimeTrips
{
    /**
     * True if driver already has a daily or weekly trip on the same date and time.
     */
    protected function hasTripAtSameDateTime(
        string $date,
        ?string $timeGo,
        ?string $timeBack,
        ?int $excludeDailySugId = null,
        ?int $excludeWeeklySugId = null
    ): bool {
        $driverId = auth()->id();

        $sameTime = function ($query) use ($date, $timeGo, $timeBack) {
            $query->whereDate('date-of-ser', $date)
                ->where(function ($q) use ($timeGo, $timeBack) {
                    if ($timeGo) {
                        $q->orWhere('time-go', $timeGo)->orWhere('time-back', $timeGo);
                    }
                    if ($timeBack) {
                        $q->orWhere('time-go', $timeBack)->orWhere('time-back', $timeBack);
                    }
                });
        };

        $hasDaily = SugDayDriver::where('driver-id', $driverId)
            ->where('action', '!=', 2)
            ->when($excludeDailySugId, fn ($q) => $q->where('id', '!=', $excludeDailySugId))
            ->whereHas('booking', $sameTime)
            ->exists();

        if ($hasDaily) {
            return true;
        }

        return SugWeekDriver::where('driver-id', $driverId)
            ->where('action', '!=', 2)
            ->when($excludeWeeklySugId, fn ($q) => $q->where('id', '!=', $excludeWeeklySugId))
            ->whereHas('booking', $sameTime)
            ->exists();
    }
}
