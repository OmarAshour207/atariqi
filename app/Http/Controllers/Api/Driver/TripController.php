<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Driver\SugDayDriverResource;
use App\Models\DayUnrideRate;
use App\Models\DelDailyInfo;
use App\Models\SugDayDriver;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TripController extends BaseController
{
    public function updateAction(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type'      => 'required|string',
            'action'    => 'required|numeric|max:6',
            'id'        => 'required|numeric'
        ]);

        if($validator->fails()) {
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);
        }

        $data = $validator->validated();

        $trip = array();

        if ($data['type'] == 'daily') {
            $trip = SugDayDriver::where('id', $data['id'])
                ->where('driver-id', auth()->user()->id)
                ->first();
            if(!$trip) {
                return $this->sendError(__('Trip not found!'), [__('Trip not found!')]);
            }

            $trip->update([
                'action' => $data['action'],
                'date-of-edit' => Carbon::now()
            ]);
        }

        return $this->sendResponse($trip, __('Success'));
    }

    public function get($type, $id): JsonResponse
    {
        $trip = [];

        if ($type == 'daily') {
            $trip = SugDayDriver::with('passenger', 'deliveryInfo')
                ->where('driver-id', auth()->user()->id)
                ->where('id', $id)
                ->first();
            if (!$trip) {
                return $this->sendError(__('Trip not found!'), [__('Trip not found!')]);
            }

            $trip = new SugDayDriverResource($trip);
        }

        return $this->sendResponse($trip, __('Data'));
    }

    public function updateDelivery(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type'              => 'required|string',
            'sug-id'            => 'required|numeric',
            'expect-arrived'    => 'nullable|date_format:H:i',
            'arrived-location'  => 'nullable|date_format:H:i',
            'arrived-destination' => 'nullable|date_format:H:i',
            'passenger-rate'    => 'nullable|numeric|max:5',
            'allow-disabilities' => 'nullable|string|in:yes,no'
        ]);

        if($validator->fails()) {
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);
        }

        $deliveryInfo = null;

        if ($request->input('type') == 'daily') {
            $deliveryInfo = DelDailyInfo::where('sug-id', $request->input('sug-id'))
                ->whereHas('ride', function ($query) {
                    $query->where('driver-id', auth()->user()->id);
                })
                ->first();
        }

        if (!$deliveryInfo) {
            return $this->sendError(__('Trip not found'), [__('Trip not found')]);
        }

        $deliveryInfo->update($validator->validated());

        return $this->sendResponse([], __('Data'));
    }

    public function start(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type'      => 'required|string',
            'action'    => 'required|numeric|max:6',
            'id'        => 'required|numeric',
            'expect-arrived' => 'required|date_format:H:i'
        ]);

        if($validator->fails()) {
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);
        }

        $data = $validator->validated();

        $response = $this->updateAction($request);
        $response = json_decode($response->getContent(), true);

        if (!$response['success']) {
            return $response;
        }

        $passenger = User::where('id', $response['data']['passenger-id'])->first();

        $title = __('Accept the trip');
        $message = __('Could you accept the trip ?');
        sendNotification(['title' => $title, 'body' => $message, 'tokens' => $passenger->fcm_token]);

        DelDailyInfo::updateOrCreate([
            'expect-arrived'    => $data['expect-arrived'],
            'sug-id'            => $data['id']
        ], [
            'allow-disabilities'=> auth()->user()->driverInfo->{"allow-disabilities"},
        ]);

        return $this->sendResponse([], __('Started successfully'));
    }

    public function rate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type'      => 'required|string',
            'rate'      => 'required|numeric|max:6',
            'comment'   => 'required|string',
            'sug-id'    => 'required|numeric',
        ]);

        if($validator->fails()) {
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);
        }

        if ($request->input('type') == 'daily') {
            DayUnrideRate::updateOrCreate([
                'sug-id'    => $request->input('sug-id')
            ], $validator->validated());
        }

        return $this->sendResponse([], __('Registered successfully'));
    }

}
