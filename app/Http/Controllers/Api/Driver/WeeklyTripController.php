<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Driver\WeekRideBookingGroupDetails;
use App\Models\WeekRideBooking;
use Symfony\Component\HttpFoundation\JsonResponse;

class WeeklyTripController extends BaseController
{
    public function get($groupId): JsonResponse
    {
        $tripsGroup = WeekRideBooking::with(['rate', 'sugDriver', 'sugDriver.deliveryInfo'])
            ->where('group-id', $groupId)
            ->get();

        $tripsGroup = WeekRideBookingGroupDetails::collection($tripsGroup);

        return $this->sendResponse($tripsGroup, __('Data'));
    }
}
