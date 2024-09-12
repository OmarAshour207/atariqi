<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\DayRideBookingResource;
use App\Http\Resources\Driver\SugDayDriverDetailsResource;
use App\Http\Resources\Driver\SugDayDriverResource;
use App\Models\DayRideBooking;
use App\Models\DriverNeighborhood;
use App\Models\DriversServices;
use App\Models\SugDayDriver;
use Carbon\Carbon;
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

        if ($this->checkTripsLimit($dayRideBooking)) {
            return $this->sendError(__("sorry you can't accept this ride, because you reach the delivery limit at the same time and date"), [
                __("sorry you can't accept this ride, because you reach the delivery limit at the same time and date")
            ], 402);
        }

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

    private function checkTripsLimit(DayRideBooking $dayRideBooking): bool
    {
        $sugDrivers = SugDayDriver::whereHas('booking', function ($query) use($dayRideBooking) {
            $query->whereDate('date-of-ser', $dayRideBooking->{"date-of-ser"})
                ->where(function ($q) use ($dayRideBooking) {
                    $q->where('time-go', $dayRideBooking->{"time-go"})
                        ->orWhere('time-back', $dayRideBooking->{"time-back"});
            });
        })
            ->where('driver-id', auth()->user()->id)
            ->get();

        if ($sugDrivers->count() > 2) {
            return true;
        }

        return false;
    }

    public function reject(Request $request)
    {

    }

    public function getToday(Request $request): JsonResponse
    {
        $today = Carbon::today()->format('Y-m-d');
        $nowTime = Carbon::now()->format('H:i:s');
        $beforeMin = Carbon::now()->subMinutes(10)->format('H:i:s');

        Log::info("Now time: $nowTime");
        Log::info("before time: $beforeMin");

        $trips = SugDayDriver::with([
            'booking',
            'passenger',
            'booking.passenger',
            'booking.neighborhood',
            'booking.university',
            'booking.service',
            'deliveryInfo',
            'rate'
        ])
            ->whereHas('booking', function ($query) use ($today, $beforeMin, $nowTime) {
                $query->where('date-of-ser', $today)
                    ->where(function ($query) use ($nowTime, $beforeMin) {

                        $query->where(function ($q) use ($nowTime, $beforeMin) {
                            $q->where('time-go', '<=', $nowTime)
                                ->where('time-go', '>=', $beforeMin);
                        })->orWhere(function ($q) use ($nowTime, $beforeMin) {
                            $q->where('time-back', '<=', $nowTime)
                                ->where('time-back', '>=', $beforeMin);
                        });
                    });
            })
            ->where('action', 1)
            ->where('driver-id', auth()->user()->id)
            ->get()
            ->groupBy('booking.road-way');

        $toTrips = isset($trips['to']) ? $trips['to'] : null;
        $fromTrips = isset($trips['from']) ? $trips['from'] : null;

        $data['to'] = $toTrips ? SugDayDriverDetailsResource::collection($toTrips) : [];
        $data['from'] = $fromTrips ? SugDayDriverDetailsResource::collection($fromTrips) : [];

        return $this->sendResponse($data, __('Data'));
    }
}
