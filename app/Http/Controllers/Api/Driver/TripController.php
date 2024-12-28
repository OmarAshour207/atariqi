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
        Log::info("Update action with type {$request->input('type')} with Action: {$request->input('action')}");

        $validator = Validator::make($request->all(), [
            'type'      => 'required|string|in:daily,weekly,immediate',
            'action'    => 'required|numeric|max:6',
            'id'        => 'required|numeric',
//            'status'    => 'required_if:type,weekly|numeric'
        ]);

        if($validator->fails()) {
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);
        }

        $sugModel = $this->getSugModel($request->input('type'));

        $trip = $sugModel::with('passenger', 'booking', 'driver')
            ->where('id', $request->input('id'))
            ->where('driver-id', auth()->user()->id)
            ->first();

        if(!$trip) {
            Log::info("Trip not found when updating action");
            return $this->sendError(__('Trip not found!'), [__('Trip not found!')]);
        }

        if ($request->input('type') == 'immediate') {
            $this->deleteRestImmediateDrivers($request->input('id'));
        }

        if($request->input('action') == 1 && $request->input('type') == 'daily') {
            sendNotification([
                'title'     => __('You have a notification from Atariqi'),
                'body'      => __('Your trip accepted at date') . " " . $trip->booking->{"date-of-ser"} . "\n" . __('with Driver') . " " . $trip->driver->{"user-first-name"} . " " . $trip->driver->{"user-last-name"},
                'tokens'    => [$trip->passenger->fcm_token]
            ]);
        }

        $trip->update([
            'action' => $request->input('action'),
            'date-of-edit' => Carbon::now()
        ]);

        Log::info("Trip action updated successfully");
        return $this->sendResponse($trip, __('Success'));
    }

    private function deleteRestImmediateDrivers($id): void
    {
        Log::info("Deleting immediate rest trips for not id $id");
        $trip = \App\Models\SuggestionDriver::select('booking-id')->where('id', $id)->first();

        \App\Models\SuggestionDriver::where('booking-id', $trip->{"booking-id"})
            ->where('driver-id', '!=', auth()->user()->id)
            ->delete();
    }

    public function get($type, $id): JsonResponse
    {
        Log::info("Getting trip details with Type: $type and ID: $id");
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

        $roadWay = $trip->booking->{"road-way"};

        $result['destination_lat'] = $roadWay == 'from' ? $trip->booking->lat : $trip->booking->university->lat;
        $result['destination_lng'] = $roadWay == 'from' ? $trip->booking->lng : $trip->booking->university->lng;
        $result['source_lat'] = $roadWay == 'from' ? $trip->booking->university->lat : $trip->booking->lat;
        $result['source_lng'] = $roadWay == 'from' ? $trip->booking->university->lng : $trip->booking->lng;

        return $this->sendResponse($result, __('Data'));
    }

    public function updateDelivery(Request $request)
    {
        Log::info("Update delivery info for type: {$request->input('type')} with Sug-ID: {$request->input('sug-id')}");
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
        Log::info("Start a trip for type: {$request->input('type')} with ID: {$request->input('id')}");
        $validator = Validator::make($request->all(), [
            'type'      => 'required|string',
            'action'    => 'required|numeric|max:6',
            'id'        => 'required|numeric',
            'expect-arrived' => 'required' // |date_format:H:i
        ]);

        if($validator->fails()) {
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);
        }

        $data = $validator->validated();
        $data['expect-arrived'] = convertArabicDateToEnglish($data['expect-arrived']);

        $response = $this->updateAction($request);
        $response = json_decode($response->getContent(), true);

        if (!$response['success']) {
            return $response;
        }

        // Checking old trips and check if this converted to ride group
        if ($request->input('type') == 'immediate') {
            $oldTrips = \App\Models\SuggestionDriver::with('passenger', 'booking')
                ->where('action', 1)
                ->where('driver-id', auth()->user()->id)
                ->get();

            if (count($oldTrips) > 1) {
                foreach ($oldTrips as $oldTrip) {
                    $title = __('The ride changed to group ride');
                    $message = __('The driver change this ride to group ride and accept another passenger located at: ') . $oldTrip->booking->neighborhood->{"neighborhood-ar"};
                    sendNotification(['title' => $title, 'body' => $message, 'tokens' => [$oldTrip->passenger->fcm_token]]);
                }
            }
        }

        $passenger = User::where('id', $response['data']['passenger-id'])->first();

        $title = __('Accept the trip');
        $message = __('Could you accept the trip with driver ' . auth()->user()->{"user-first-name"});
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
