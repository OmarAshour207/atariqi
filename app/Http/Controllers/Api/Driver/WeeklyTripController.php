<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Driver\WeekRideBookingGroupDetails;
use App\Models\SugWeekDriver;
use App\Models\WeekRideBooking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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

    public function updateAction(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'group_id'  => 'required|numeric',
            'status'    => 'required|numeric',
            'tag'       => 'required|string|in:my,all',
            'action'    => 'required_if:tag,my|numeric'
        ]);

        if($validator->fails()) {
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);
        }

        $tripsGroup = WeekRideBooking::with('sugDriver')
            ->where('group-id', $request->input('group_id'))
            ->get();

        foreach ($tripsGroup as $tripGroup) {
            // move from all trips to my trips
            $tripGroup->update([
                'status' => $request->input('status'),
                'action' => 0
            ]);

            if ($request->input('tag') == 'my') {
                $tripGroup->sugDriver->update([
                    'action' => $request->input('action')
                ]);
            } else {
                SugWeekDriver::create([
                    'booking-id' => $tripGroup->id,
                    'driver-id' => auth()->user()->id,
                    'passenger-id' => $tripGroup->{"passenger-id"},
                    'action' => 1,
                    'date-of-add' => Carbon::now(),
                ]);
            }
        }

        if($request->input('action') == 1) {
            $title = __('You have a notification from Atariqi');
            $message = __('Your trip accepted with #') . $request->input('group');
            sendNotification(['title' => $title, 'body' => $message, 'tokens' => [$passenger->fcm_token]]);
        }

        return $this->sendResponse([], __('Data'));
    }
}
