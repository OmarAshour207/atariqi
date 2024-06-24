<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Api\Driver\Traits\Payment;
use App\Models\DayRideBooking;
use App\Models\FinancialDue;
use App\Models\Subscription;
use App\Models\SuggestionDriver;
use App\Models\WeekRideBooking;
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

        $dates = [$lastPayDate->{"date-of-add"}, Carbon::now()];
        $newRevenues = $this->getRevenue(auth()->user()->id, $dates);

        $subscriptionCost = Subscription::select('cost')->where('id', 4)->first();

        $currentDues = ($subscriptionCost->cost * $newRevenues['total']) / 100;

        return $this->sendResponse([
            'last_pay_date' => Carbon::parse($lastPayDate->{"date-of-add"})->format('Y/m/d'),
            'last_pay_cost' => $lastPayDate->amount,
            'new_revenues' => $newRevenues['total'],
            'current_dues' => $currentDues
        ], __('Data'));
    }
}
