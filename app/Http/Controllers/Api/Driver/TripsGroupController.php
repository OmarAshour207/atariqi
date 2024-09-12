<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Driver\SugDayDriverDetailsResource;
use App\Models\DelDailyInfo;
use App\Models\DelWeekInfo;
use App\Models\SugDayDriver;
use App\Models\SugWeekDriver;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TripsGroupController extends BaseController
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type'          => 'required|string|in:daily,weekly',
            'trips'         => 'required|array|min:2|max:3',
            'trips.*.action'=> 'required|numeric|max:6',
            'trips.*.id'    => 'required|numeric',
            'trips.*.expect-arrived' => 'required' // |date_format:H:i
        ]);

        if($validator->fails()) {
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);
        }

        $sugModel = SugDayDriver::class;
        $delModel = DelDailyInfo::class;

        if($request->input('type') == 'weekly') {
            $sugModel = SugWeekDriver::class;
            $delModel = DelWeekInfo::class;
        }

        foreach ($request->input('trips') as $data) {
            $trip = $sugModel::with('passenger', 'booking', 'driver')
                ->where('id', $data['id'])
                ->where('driver-id', auth()->user()->id)
                ->first();

            if(!$trip) {
                continue;
            }

            $trip->update([
                'action' => $data['action'],
                'date-of-edit' => Carbon::now()
            ]);

//            if($data['action'] == 1 && $request->input('type') == 'daily') {
                sendNotification([
                    'title'     => __('You have a notification from Atariqi'),
                    'body'      => __('Your trip accepted at date') . " " . $trip->booking->{"date-of-ser"} . "\n" . __('with Driver') . " " . $trip->driver->{"user-first-name"} . " " . $trip->driver->{"user-last-name"},
                    'tokens'    => [$trip->passenger->fcm_token]
                ]);
//            }

            $delModel::updateOrCreate([
                'sug-id'            => $data['id']
            ], [
                'allow-disabilities'=> auth()->user()->driverInfo->{"allow-disabilities"},
                'expect-arrived'    => convertArabicDateToEnglish($data['expect-arrived'])
            ]);
        }

        return $this->sendResponse([], __('Started successfully'));
    }

    public function get(Request $request)
    {
        Log::info("Request: " , $request->all());
        Log::info("Type: " . gettype($request->input('trips')));

        Log::info("Trips: ", json_decode($request->input('trips', true)));

        $validator = Validator::make($request->all(), [
            'type'          => 'required|string|in:daily,weekly',
            'trips'         => 'required|array|min:2|max:3'
        ]);

        if($validator->fails()) {
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);
        }

        $sugModel = SugDayDriver::class;

        if ($request->input('type') == 'weekly') {
            $sugModel = SugWeekDriver::class;
        }

        $trips = $sugModel::with(['booking',
            'passenger',
            'booking.passenger',
            'booking.neighborhood',
            'booking.university',
            'booking.service',
            'deliveryInfo',
            'rate'])
            ->whereIn('id', $request->input('trips'))
            ->where('driver-id', auth()->user()->id)
            ->get();

        $data = SugDayDriverDetailsResource::collection($trips);

        return $this->sendResponse($data, __('Data'));
    }
}
