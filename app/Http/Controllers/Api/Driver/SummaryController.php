<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Driver\SugDayDriverResource;
use App\Http\Resources\Driver\SugDriverResource;
use App\Http\Resources\Driver\SugWeeklyDriverResource;
use App\Models\SugDayDriver;
use App\Models\SuggestionDriver;
use App\Models\SugWeekDriver;
use App\Models\Support\QueryFilters\SortByDate;
use App\Models\Support\QueryFilters\SortByRate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

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
        $date = isset($validator->validated()['date']) ? $validator->validated()['date'] : '';

        $driverId = auth()->user()->id;

        $weeklyRides = SugWeekDriver::with('passenger', 'booking')
            ->where('driver-id', $driverId)
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date-of-add', $date);
            })->get();

        $dailyRides = SugDayDriver::with('passenger', 'booking')
            ->where('driver-id', $driverId)
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date-of-add', $date);
            })->get();

        $immediateRides = SuggestionDriver::with('passenger', 'booking')
            ->where('driver-id', $driverId)
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date-of-add', $date);
            })->get();

        $success = array();
        $success['weekly'] = SugWeeklyDriverResource::collection($weeklyRides);
        $success['daily'] = SugDayDriverResource::collection($dailyRides);
        $success['immediate'] = SugDriverResource::collection($immediateRides);

        return $this->sendResponse($success, __('Data'));
    }

    public function summary(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'nullable|string',
            'filter.date' => 'nullable|date_format:Y-m-d',
            'filter.action' => 'nullable|string|in:new,accepted,rejected,cancelled,done'
        ]);

        if($validator->fails()) {
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);
        }

        $summaries = QueryBuilder::for($this->getModel($request))
            ->allowedFilters([
                AllowedFilter::callback('date', fn (Builder $query, $value) => $query->whereDate('date-of-add', $value)),
                AllowedFilter::callback('action', fn (Builder $query, $value) => $query->{$validator->validated()['filter']['action']}() ) ,
            ])
            ->allowedSorts([
                AllowedSort::custom('date', new SortByDate, $request->input('type')),
                AllowedSort::custom('rate', new SortByRate)
            ])
            ->with(['booking', 'deliveryInfo'])
            ->get();

        return $this->sendResponse($summaries, __('Data'));
    }

    private function getModel(Request $request)
    {
        $type = $request->input('type');

        if($type == 'daily') {
            return SugDayDriver::class;
        } elseif ($type == 'weekly') {
            return SugWeekDriver::class;
        }
        return SuggestionDriver::class;
    }
}
