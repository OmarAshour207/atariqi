<?php

namespace App\Services;

use App\Http\Controllers\Api\Driver\Traits\Payment;
use App\Models\FinancialDue;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;

class DriverDuesService
{
    use Payment;

    public function summary(?User $driver = null): array
    {
        $driver = $driver ?? auth()->user();

        $lastPayDate = FinancialDue::select('amount', 'date-of-add')
            ->where('driver-id', $driver->id)
            ->orderBy('id', 'desc')
            ->first();

        $dates = [
            'start_date' => $lastPayDate?->{"date-of-add"},
            'end_date' => Carbon::now()->format('Y-m-d'),
        ];

        $newRevenues = $this->getRevenue($driver->id, $dates);
        $duesPercentage = Subscription::generalDuesPercentageValue();
        $currentDues = ($duesPercentage * $newRevenues['total']) / 100;

        return [
            'last_pay_date' => $lastPayDate?->{"date-of-add"}
                ? Carbon::parse($lastPayDate?->{"date-of-add"})->format('Y/m/d')
                : null,
            'last_pay_cost' => $lastPayDate->amount ?? 0,
            'new_revenues' => $newRevenues['total'],
            'current_dues' => $currentDues,
            'can_accept_trips' => (int) $driver->approval === 1 && $driver->scopeCheckAcceptTrips($currentDues),
            'requires_abshir_update' => (int) $driver->approval === 4,
            'abshir_message' => $driver->{'reject-reason'},
        ];
    }

    public function canAcceptTrips(?User $driver = null): bool
    {
        return $this->summary($driver)['can_accept_trips'];
    }

    public function currentDuesAmount(?User $driver = null): float
    {
        return (float) $this->summary($driver)['current_dues'];
    }
}
