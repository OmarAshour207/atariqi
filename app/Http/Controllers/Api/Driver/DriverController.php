<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Driver\SugDayDriverResource;
use App\Http\Resources\Driver\SugDriverResource;
use App\Http\Resources\Driver\SugWeeklyDriverResource;
use App\Models\SugDayDriver;
use App\Models\SuggestionDriver;
use App\Models\SugWeekDriver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DriverController extends BaseController
{
    public function summary(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'nullable|date_format:Y-m-d',
        ]);

        if($validator->fails()) {
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);
        }
        $date = isset($validator->validated()['date']) ? $validator->validated()['date'] : '';

        $driverId = auth()->user()->id;

        $weeklyRides = SugWeekDriver::with('passenger')->where('driver-id', $driverId)
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date-of-add', $date);
            })->get();

        $dailyRides = SugDayDriver::with('passenger')->where('driver-id', $driverId)
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date-of-add', $date);
            })->get();

        $immediateRides = SuggestionDriver::with('passenger')->where('driver-id', $driverId)
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date-of-add', $date);
            })->get();

        $success = array();
        $success['weekly'] = SugWeeklyDriverResource::collection($weeklyRides);
        $success['daily'] = SugDayDriverResource::collection($dailyRides);
        $success['immediate'] = SugDriverResource::collection($immediateRides);

        return $this->sendResponse($success, __('Data'));
    }

    public function DriverRate()
    {
        $success = array();
        $success['rate'] = auth()->user()->driverInfo->{"driver-rate"};
        $success['finished_rides'] = $this->getFinishedRides();
        $success['cancelled_rides'] = $this->getCancelledRides();

        return $this->sendResponse($success, __('Data'));
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
