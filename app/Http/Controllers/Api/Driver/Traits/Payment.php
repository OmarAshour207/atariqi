<?php

namespace App\Http\Controllers\Api\Driver\Traits;

use App\Models\DayRideBooking;
use App\Models\Service;
use App\Models\SuggestionDriver;
use App\Models\WeekRideBooking;

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
        $immediateRides = SuggestionDriver::finishedTrips($userId, $dates)->count();

        $dailyRides = DayRideBooking::finishedTrips($userId, $dates)->count();

        $weeklyRides = WeekRideBooking::finishedTrips($userId, $dates)->count();

        $servicesCost = $this->getServicesCost();

        $data = [
            'immediate' => $immediateRides * $servicesCost[1],
            'daily' => $dailyRides * $servicesCost[6],
            'weekly' => $weeklyRides * $servicesCost[8]
        ];

        $data['total'] = array_sum(array_values($data));

        return $data;
    }
}
