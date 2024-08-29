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
            $query->whereDate('date-of-ser', $dayRideBooking->{"date-of-ser"})->where(function ($q) use ($dayRideBooking) {
                $q->where('time-go', $dayRideBooking->{"time-go"})->orWhere('time-back', $dayRideBooking->{"time-back"});
            });
        })
            ->whereIn('driver-id', auth()->user()->id)
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
        $nowTime = Carbon::now()->format('H:i');
        $beforeMin = Carbon::now()->subMinutes(10)->format('H:i');

        $trips = SugDayDriver::with([
            'booking',
            'passenger',
            'booking.neighborhood',
            'booking.university',
            'booking.service',
            'deliveryInfo',
            'rate'
        ])
            ->whereHas('booking', function ($query) use ($today, $beforeMin, $nowTime) {
                $query->whereDate('date-of-ser', $today)
                    ->where(function ($query) use ($nowTime, $beforeMin) {
                        $query->whereBetween('time-go', [$nowTime, $beforeMin])
                            ->orWhereBetween('time-back', [$nowTime, $beforeMin]);
                    });
            })
            ->where('action', 1)
            ->where('driver-id', auth()->user()->id)
            ->get()
            ->groupBy('booking.service.road-way');

        $toTrips = isset($trips['to']) ? $trips['to'] : null;
        $fromTrips = isset($trips['from']) ? $trips['from'] : null;

//        $toTripsCount = $toTrips?->count() ?? 0;
//        $fromTripsCount = $fromTrips?->count() ?? 0;
//
//        if ($toTripsCount == 0) {
//            $toMessage = __("No rides available now");
//        } elseif($toTripsCount == 1) {
//            $toMessage = __('you can’t have a group ride, because you have only one ride');
//        }
//
//        if ($fromTripsCount == 0) {
//            $fromMessage = __("No rides available now");
//        } elseif($fromTripsCount == 1) {
//            $fromMessage = __('you can’t have a group ride, because you have only one ride');
//        }

//        $data['to']['message'] = $toMessage;
        $data['to'] = $toTrips ? SugDayDriverDetailsResource::collection($toTrips) : [];

//        $data['from']['message'] = $fromMessage;
        $data['from'] = $fromTrips ? SugDayDriverDetailsResource::collection($fromTrips) : [];

        return $this->sendResponse($data, __('Data'));
    }
}
