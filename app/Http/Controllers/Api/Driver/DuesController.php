<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Api\Driver\Traits\Payment;
use App\Models\FinancialDue;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\JsonResponse;

class DuesController extends BaseController
{
    use Payment;
    public function getData(): JsonResponse
    {
        $lastPayDate = FinancialDue::select('amount', 'date-of-add')
            ->where('driver-id', auth()->user()->id)
            ->orderBy('id', 'desc')
            ->first();

        $dates[] = [$lastPayDate?->{"date-of-add"}, Carbon::now()];
        $newRevenues = $this->getRevenue(auth()->user()->id, $dates);

        $subscriptionCost = Subscription::select('cost')->where('id', 4)->first();

        $currentDues = ($subscriptionCost->cost * $newRevenues['total']) / 100;

        return $this->sendResponse([
            'last_pay_date' => $lastPayDate?->{"date-of-add"} ? Carbon::parse($lastPayDate?->{"date-of-add"})->format('Y/m/d') : null,
            'last_pay_cost' => $lastPayDate->amount ?? 0,
            'new_revenues' => $newRevenues['total'],
            'current_dues' => $currentDues,
            'can_start_trips' => auth()->user()->scopeCheckStartingTrips($currentDues)
        ], __('Data'));
    }
}
