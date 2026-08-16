<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Driver\PackageResource;
use App\Http\Resources\Driver\UserPackageResource;
use App\Http\Resources\DriverResource;
use App\Models\DriversServices;
use App\Models\User;
use App\Models\UserPackage;
use Illuminate\Support\Facades\DB;

class DriverController extends BaseController
{
    public function DriverRate()
    {
        $user = User::with([
            'driverInfo',
            'driverCar.driverType',
            'stage',
            'callingKey',
            'university.cityUni.neighbours',
        ])->findOrFail(auth()->id());

        $activePackage = UserPackage::where('user_id', $user->id)
            ->where('status', UserPackage::STATUS_ACTIVE)
            ->first();

        $success = [
            'rate' => $user->driverInfo?->{'driver-rate'} ?? 0,
            'finished_rides' => $this->getFinishedRides(),
            'cancelled_rides' => $this->getCancelledRides(),
            'service_started' => $this->checkStartService(),
            'active_package' => $activePackage ? new UserPackageResource($activePackage) : null,
            'driver' => new DriverResource($user),
        ];

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
