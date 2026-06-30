<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LocationController extends BaseController
{
    public function update(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);
        }

        $driverInfo = auth()->user()->driverInfo;

        if (!$driverInfo) {
            return $this->sendError(__('Driver info not found.'), [], 404);
        }

        $now = now();

        $driverInfo->update([
            'current-lat' => (string) $request->input('lat'),
            'current-lng' => (string) $request->input('lng'),
            'current-location-at' => $now,
            'date-of-edit' => $now,
        ]);

        return $this->sendResponse([], __('Location updated successfully'));
    }
}
