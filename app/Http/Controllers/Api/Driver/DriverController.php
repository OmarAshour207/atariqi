<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Controller;
use App\Models\SugDayDriver;
use App\Models\SuggestionDriver;
use App\Models\SugWeekDriver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DriverController extends Controller
{
    public function summary(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'nullable',
        ]);

        if($validator->fails()) {
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);
        }
        $date = $validator->validated()['date'];

        $driverId = auth()->user()->id;
        $weeklyRides = SugWeekDriver::where('driver-id', $driverId)
            ->when($date, function ($query) use ($date) {
                $query->where('created_at', $date);
            })->get();

        $DailyRides = SugDayDriver::where('driver-id', $driverId)
            ->when($date, function ($query) use ($date) {
                $query->where('created_at', $date);
            })->get();

        $ImmediateRides = SuggestionDriver::where('driver-id', $driverId)
            ->when($date, function ($query) use ($date) {
                $query->where('created_at', $date);
            })->get();
    }
}
