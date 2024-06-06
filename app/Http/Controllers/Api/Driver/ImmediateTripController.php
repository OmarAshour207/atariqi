<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Api\BaseController;
use App\Models\SuggestionDriver;

class ImmediateTripController extends BaseController
{
    public function index()
    {
        $trips = SuggestionDriver::with(['deliveryInfo', 'rate'])
            ->where('action', 0)
            ->get();

        $trips = \App\Http\Resources\SuggestionDriver::collection($trips);

        return $this->sendResponse($trips, __('Data'));
    }
}
