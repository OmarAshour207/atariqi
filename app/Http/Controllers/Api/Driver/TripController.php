<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Driver\SugDayDriverResource;
use App\Http\Resources\Driver\SugWeeklyDriverResource;
use App\Http\Resources\SuggestionDriver;
use App\Models\DayUnrideRate;
use App\Models\DelDailyInfo;
use App\Models\DelImmediateInfo;
use App\Models\DelWeekInfo;
use App\Models\ImmediateUnrideRate;
use App\Models\PassengerRate;
use App\Models\SugDayDriver;
use App\Models\SugWeekDriver;
use App\Models\User;
use App\Models\WeekRideBooking;
use App\Models\WeekUnrideRate;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TripController extends BaseController
{
    public function updateAction(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type'      => 'required|string|in:daily,weekly,immediate',
            'action'    => 'required|numeric|max:6',
            'id'        => 'required|numeric',
//            'status'    => 'required_if:type,weekly|numeric'
        ]);

        if($validator->fails()) {
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);
        }

        $trip = array();

        if ($request->input('type') == 'daily') {
            $trip = SugDayDriver::with('passenger')->where('id', $request->input('id'))
                ->where('driver-id', auth()->user()->id)
                ->first();

        } elseif($request->input('type') == 'weekly') {
            $trip = SugWeekDriver::with('booking')
                ->where('id', $request->input('id'))
                ->first();
        } else {
            $trip = \App\Models\SuggestionDriver::with('passenger')
                ->where('id', $request->input('id'))
                ->where('driver-id', auth()->user()->id)
                ->first();
        }

        if(!$trip) {
            return $this->sendError(__('Trip not found!'), [__('Trip not found!')]);
        }

        if($request->input('action') == 1 && $request->input('type') == 'daily') {
            sendNotification([
                'title'     => __('You have a notification from Atariqi'),
                'body'      => __("an order from Atariqi to accept the ride"),
                'tokens'    => [$trip->passenger->fcm_token]
            ]);
        }

        $trip->update([
            'action' => $request->input('action'),
            'date-of-edit' => Carbon::now()
        ]);

        return $this->sendResponse($trip, __('Success'));
    }

    public function get($type, $id): JsonResponse
    {
        $model = $this->getSugModel($type);

        $trip = $model::with('booking', 'passenger', 'deliveryInfo', 'booking.university', 'booking.passenger', 'rate')
            ->where('driver-id', auth()->user()->id)
            ->where('id', $id)
            ->first();

        if (!$trip) {
            return $this->sendError(__('Trip not found!'), [__('Trip not found!')]);
        }

        if ($type == 'daily') {
            $trip = new SugDayDriverResource($trip);
        } elseif ($type == 'weekly') {
            $trip = new SugWeeklyDriverResource($trip);
        } elseif ($type == 'immediate') {
            $trip = new SuggestionDriver($trip);
        } else {
            return $this->sendError(__('Unsupported action!'), [__('Unsupported action!')]);
        }

        $result = $trip->resolve();

        if ($trip->booking->{"road-way"} == 'from') {
            $result['destination_lat'] = $trip->booking->lat;
            $result['destination_lng'] = $trip->booking->lng;
            $result['source_lat'] = $trip->booking->university->lat;
            $result['source_lng'] = $trip->booking->university->lng;
        } else {
            $result['destination_lat'] = $trip->booking->university->lat;
            $result['destination_lng'] = $trip->booking->university->lng;
            $result['source_lat'] = $trip->booking->lat;
            $result['source_lng'] = $trip->booking->lng;
        }

        Log::info("$type Response with Trip ID: " . $trip->id, $result);

        return $this->sendResponse($result, __('Data'));
    }

    public function updateDelivery(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type'              => 'required|string|in:daily,weekly,immediate',
            'sug-id'            => 'required|numeric',
            'expect-arrived'    => 'nullable', // |date_format:H:i
            'arrived-location'  => 'nullable', // |date_format:H:i
            'arrived-destination' => 'nullable', // |date_format:H:i
            'passenger-rate'    => 'nullable|numeric|max:5',
            'allow-disabilities' => 'nullable|string|in:yes,no'
        ]);

        if($validator->fails()) {
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);
        }

        $model = $this->getModel($request->input('type'));

        $deliveryInfo = $model::with('ride')
            ->where('sug-id', $request->input('sug-id'))
            ->whereHas('ride', function ($query) {
                $query->where('driver-id', auth()->user()->id);
            })
            ->first();

        if (!$deliveryInfo) {
            return $this->sendError(__('Trip not found'), [__('Trip not found')]);
        }

        if ($request->input("passenger-rate")) {
            $passengerRate = PassengerRate::where('user-id', $deliveryInfo->ride->passenger->id)->first();
            $allRate = number_format(($request->{"passenger-rate"} + $passengerRate?->rate) / 2, 1);
            PassengerRate::updateOrCreate([
                'user-id' => $deliveryInfo->ride->passenger->id
            ], [
                'rate' => $allRate
            ]);
        }

        $data = $validator->validated();

        if (isset($data['expect-arrived'])) {
            $data['expect-arrived'] = convertArabicDateToEnglish($data['expect-arrived']);
        }

        if (isset($data['arrived-location'])) {
            $data['arrived-location'] = convertArabicDateToEnglish($data['arrived-location']);
        }

        if (isset($data['arrived-destination'])) {
            $data['arrived-destination'] = convertArabicDateToEnglish($data['arrived-destination']);
        }

        $deliveryInfo->update($data);

        return $this->sendResponse([], __('Data'));
    }

    public function start(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type'      => 'required|string',
            'action'    => 'required|numeric|max:6',
            'id'        => 'required|numeric',
            'expect-arrived' => 'required' // |date_format:H:i
        ]);

        if($validator->fails()) {
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);
        }

        Log::info("Request", $request->all());

        $data = $validator->validated();
        $data['expect-arrived'] = convertArabicDateToEnglish($data['expect-arrived']);

        Log::info("Before update action");
        $response = $this->updateAction($request);
        $response = json_decode($response->getContent(), true);

        if (!$response['success']) {
            return $response;
        }

        $passenger = User::where('id', $response['data']['passenger-id'])->first();

        $title = __('Accept the trip');
        $message = __('Could you accept the trip ?');
        sendNotification(['title' => $title, 'body' => $message, 'tokens' => [$passenger->fcm_token]]);

        $this->getModel($request->input('type'))::updateOrCreate([
            'sug-id'            => $data['id']
        ], [
            'allow-disabilities'=> auth()->user()->driverInfo->{"allow-disabilities"},
            'expect-arrived'    => $data['expect-arrived']
        ]);

        return $this->sendResponse([], __('Started successfully'));
    }

    private function getModel($type)
    {
        if($type == 'daily') {
            return DelDailyInfo::class;
        } elseif ($type == 'weekly') {
            return DelWeekInfo::class;
        }
        return DelImmediateInfo::class;
    }

    public function rate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type'      => 'required|string|in:daily,weekly,immediate',
            'rate'      => 'required|numeric|max:5',
            'comment'   => 'required|string',
            'sug-id'    => 'required|numeric',
        ]);

        if($validator->fails()) {
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);
        }

        $sugModel = $this->getSugModel($request->input('type'));
        $unrideRateModel = $this->getUnrideRateModel($request->input('type'));

        $ride = $sugModel::where('id', $request->input('sug-id'))->first();

        if (!$ride) {
            return $this->sendError(__('Trip not found!'), [__('Trip not found!')]);
        }

        $unrideRateModel::updateOrCreate([
            'sug-id'    => $request->input('sug-id')
        ], $validator->validated());

        return $this->sendResponse([], __('Registered successfully'));
    }

    private function getSugModel($type)
    {
        if ($type == 'daily') {
            return SugDayDriver::class;
        } elseif ($type == 'weekly') {
            return SugWeekDriver::class;
        }

        return \App\Models\SuggestionDriver::class;
    }

    private function getUnrideRateModel($type)
    {
        if ($type == 'daily') {
            return DayUnrideRate::class;
        } elseif ($type == 'weekly') {
            return WeekUnrideRate::class;
        }

        return ImmediateUnrideRate::class;
    }
}
