<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\DayRideBookingResource;
use App\Models\DayRideBooking;
use App\Models\DriverNeighborhood;
use App\Models\DriversServices;
use App\Models\SugDayDriver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;

class DailyTripsController extends BaseController
{
    public function get(): JsonResponse
    {
        $driverServices = DriversServices::select('service-id')
            ->where('driver-id', auth()->user()->id)
            ->get()
            ->pluck('service-id')
            ->toArray();
        $driverNeighborhoods = DriverNeighborhood::where('driver-id', auth()->user()->id)->first();

        $neighborhoodsTo = explode('|', $driverNeighborhoods?->{"neighborhoods-to"});
        $neighborhoodsFrom = explode('|', $driverNeighborhoods?->{"neighborhoods-from"});

        $trips = DayRideBooking::with(['passenger', 'neighborhood', 'university', 'service', 'sugDriver.deliveryInfo'])
            ->where('action', 4)
            ->whereIn('service-id', $driverServices)
            ->where(function($query) use ($neighborhoodsTo, $neighborhoodsFrom) {
                $query->whereIn('neighborhood-id', $neighborhoodsTo)
                    ->orWhereIn('neighborhood-id', $neighborhoodsFrom);
            })
            ->get();

        return $this->sendResponse(DayRideBookingResource::collection($trips), __('Data'));
    }

    public function accept(Request $request)
    {
        Log::info("Accept daily ride with id: " . $request->input('id'));
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|exists:day-ride-booking,id'
        ]);

        if($validator->fails()) {
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);
        }

        $dues = new DuesController();
        $totalDues = $dues->getData();
        $canAcceptTrips = json_decode($totalDues->getContent(), true)['data']['can_accept_trips'];

        if ($request->input('action') == 1 && !$canAcceptTrips) {
            return $this->sendError(__('Please pay your dues to activate your services again. Note: you can deliver your previously accepted rides'), [__('Please pay your dues to activate your services again. Note: you can deliver your previously accepted rides')]);
        }

        $dayRideBooking = DayRideBooking::where('id', $request->input('id'))->first();

        SugDayDriver::create([
            'booking-id' => $request->input('id'),
            'driver-id' => auth()->user()->id,
            'passenger-id' => $dayRideBooking->{"passenger-id"},
            'action' => 1
        ]);

        $dayRideBooking->update([
            'action' => 0
        ]);

        Log::info("Add suggested driver and update day ride booking");

        return $this->sendResponse([], __('Success'));
    }

    public function reject(Request $request)
    {

    }
}
