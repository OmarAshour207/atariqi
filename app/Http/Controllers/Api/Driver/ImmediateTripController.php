<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Api\BaseController;
use App\Models\SuggestionDriver;
use Illuminate\Http\Request;

class ImmediateTripController extends BaseController
{
    public function index(Request $request)
    {
        $trips = SuggestionDriver::with([
            'deliveryInfo', 'rate'
        ])->when($request->input('action'), function ($query) {
            $query->where('action', 0);
        })
            ->get();

        $trips = \App\Http\Resources\SuggestionDriver::collection($trips);

        return $this->sendResponse($trips, __('Data'));
    }
}
