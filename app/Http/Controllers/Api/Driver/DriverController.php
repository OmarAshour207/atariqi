<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Api\BaseController;
use App\Models\DriversServices;
use Illuminate\Support\Facades\DB;

class DriverController extends BaseController
{
    public function DriverRate()
    {
        $success = array();
        $success['rate'] = auth()->user()->driverInfo->{"driver-rate"};
        $success['finished_rides'] = $this->getFinishedRides();
        $success['cancelled_rides'] = $this->getCancelledRides();
        $success['service_started'] = $this->checkStartService();

        return $this->sendResponse($success, __('Data'));
    }

    private function checkStartService(): bool
    {
        $services = DriversServices::where('driver-id', auth()->user()->id)
            ->whereHas('service', function ($query) {
                $query->where('service-eng', 'like', '%immediately%');
            })->count();

        if (!$services) {
            return false;
        }
        return true;
    }

    private function getFinishedRides()
    {
        $driverId = auth()->user()->id;

        $query = DB::table(function ($subquery) use ($driverId) {
            $subquery->select('driver-id', 'action')
                ->from('sug-week-drivers')
                ->where('driver-id', $driverId)
                ->where('action', 6)
                ->unionAll(function ($subquery) use ($driverId) {
                    $subquery->select('driver-id', 'action')
                        ->from('sug-day-drivers')
                        ->where('driver-id', $driverId)
                        ->where('action', 6);
                })
                ->unionAll(function ($subquery) use ($driverId) {
                    $subquery->select('driver-id', 'action')
                        ->from('suggestions-drivers')
                        ->where('driver-id', $driverId)
                        ->where('action', 5);
                });
        }, 'subquery')
            ->selectRaw('COUNT(*)')
            ->get();

        return $query[0]->{"COUNT(*)"};
    }

    private function getCancelledRides()
    {
        $driverId = auth()->user()->id;

        $query = DB::table(function ($subquery) use ($driverId) {
            $subquery->select('driver-id', 'action')
                ->from('sug-week-drivers')
                ->where('driver-id', $driverId)
                ->where('action', 2)
                ->unionAll(function ($subquery) use ($driverId) {
                    $subquery->select('driver-id', 'action')
                        ->from('sug-day-drivers')
                        ->where('driver-id', $driverId)
                        ->where('action', 2);
                })
                ->unionAll(function ($subquery) use ($driverId) {
                    $subquery->select('driver-id', 'action')
                        ->from('suggestions-drivers')
                        ->where('driver-id', $driverId)
                        ->where('action', 4);
                });
        }, 'subquery')
            ->selectRaw('COUNT(*)')
            ->get();

        return $query[0]->{"COUNT(*)"};
    }

}
