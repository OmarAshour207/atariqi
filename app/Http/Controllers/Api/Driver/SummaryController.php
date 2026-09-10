<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Driver\SugDayDriverResource;
use App\Http\Resources\Driver\SugDriverResource;
use App\Http\Resources\Driver\SugWeeklyDriverResource;
use App\Http\Resources\Driver\WeekRideBookingGroupResource;
use App\Models\SugDayDriver;
use App\Models\SuggestionDriver;
use App\Models\SugWeekDriver;
use App\Models\Support\QueryFilters\SortByDate;
use App\Models\Support\QueryFilters\SortByRate;
use App\Models\WeekRideBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;

class SummaryController extends BaseController
{
    public function summaryAll(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'nullable|date_format:Y-m-d',
        ]);

        if($validator->fails()) {
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);
        }

        $driverId = auth()->user()->id;

        $weeklyRides = SugWeekDriver::with([
                'passenger',
                'booking',
                'booking.university',
                'booking.neighborhood',
                'booking.service',
                'deliveryInfo',
                'rate',
            ])
            ->where('driver-id', $driverId)
            ->when($request->input('date'), function ($query) use ($request) {
                $query->whereDate('date-of-add', $request->input('date'));
            })->get();

        $dailyRides = SugDayDriver::with('passenger', 'booking')
            ->where('driver-id', $driverId)
            ->when($request->input('date'), function ($query) use ($request) {
                $query->whereDate('date-of-add', $request->input('date'));
            })->get();

        $immediateRides = SuggestionDriver::with('passenger', 'booking')
            ->where('driver-id', $driverId)
            ->when($request->input('date'), function ($query) use ($request) {
                $query->whereDate('date-of-add', $request->input('date'));
            })->get();

        $success = array();
        $success['weekly'] = SugWeeklyDriverResource::collection($weeklyRides);
        $success['daily'] = SugDayDriverResource::collection($dailyRides);
        $success['immediate'] = SugDriverResource::collection($immediateRides);

        return $this->sendResponse($success, __('Data'));
    }

    public function summary(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string',
            'filter.date' => 'nullable|date_format:Y-m-d',
            'filter.action' => 'nullable|string',
            'filter.status' => 'nullable|numeric'
        ]);

        if($validator->fails()) {
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);
        }

        // Weekly "send to all" trips live on bookings (no sug row until accept)
        if ($request->input('type') == 'weekly') {
            return $this->weeklyAllTripsSummary($request);
        }

        $summaries = QueryBuilder::for($this->getModel($request))
            ->allowedFilters([
                AllowedFilter::scope('date'),
                AllowedFilter::scope('action'),
                AllowedFilter::scope('status'),
            ])
            ->allowedSorts([
                AllowedSort::custom('date', new SortByDate, $request->input('type')),
                AllowedSort::custom('rate', new SortByRate, $request->input('type'))
            ])
            ->with($this->eagerLoadsForType($request->input('type')))
            ->where('driver-id', auth()->user()->id)
            ->orderBy('date-of-add', 'desc')
            ->get();

        if($request->input('type') == 'daily') {
            $summaries = SugDayDriverResource::collection($summaries);
        } elseif ($request->input('type') == 'weekly') {
            $summaries = SugWeeklyDriverResource::collection($summaries);
        } else {
            $summaries = SugDriverResource::collection($summaries);
        }

        return $this->sendResponse($summaries, __('Data'));
    }

    private function weeklyAllTripsSummary(Request $request): JsonResponse
    {
        $summaries = QueryBuilder::for(WeekRideBooking::class)
            ->allowedFilters([
                AllowedFilter::scope('date'),
                AllowedFilter::scope('action'),
                AllowedFilter::scope('status'),
            ])
            ->with([
                'sugDriver',
                'sugDriver.deliveryInfo',
                'neighborhood',
                'passenger',
                'university',
                'service',
                'rate',
            ])
            ->when((string) $request->input('filteraction') === '0', function ($query) {
                $query->whereHas('sugDriver', function ($q) {
                    $q->where('driver-id', auth()->user()->id);
                });
            })
            ->orderBy('date-of-add', 'desc')
            ->get()
            ->groupBy('group-id');

        return $this->sendResponse(
            WeekRideBookingGroupResource::collection($summaries),
            __('Data')
        );
    }

    private function eagerLoadsForType(string $type): array
    {
        if ($type === 'weekly') {
            return [
                'booking',
                'booking.passenger',
                'booking.university',
                'booking.neighborhood',
                'booking.service',
                'passenger',
                'deliveryInfo',
                'rate',
            ];
        }

        return ['booking', 'booking.passenger', 'deliveryInfo', 'rate'];
    }

    private function getModel(Request $request)
    {
        $type = $request->input('type');

        if($type == 'daily') {
            return SugDayDriver::class;
        } elseif ($type == 'weekly') {
            return WeekRideBooking::class;
        }
        return SuggestionDriver::class;
    }
}
