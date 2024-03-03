<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Api\BaseController;
use App\Models\DriversServices;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceController extends BaseController
{
    public function start(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_id'   => 'required|numeric|exists:services,id',
        ]);

        if($validator->fails()) {
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);
        }

        $user = User::with(['driverInfo', 'driverCar', 'driverNeighborhood', 'driverSchedule'])
            ->whereId(auth()->user()->id)
            ->first();

        foreach ($user->getRelations() as $name => $relation) {
            if($relation?->exists() == null) {
                return $this->sendError(__('Please complete your information to start working with us.'),
                    [__('Please complete your information to start working with us')], 422);
            }
        }

        DriversServices::create([
            'driver-id'     => auth()->user()->id,
            'service-id'    => $request->service_id
        ]);

        return $this->sendResponse([], __('Your service started'));
    }

    public function stop()
    {
        DriversServices::where('driver-id', auth()->user()->id)
            ->whereHas('service', function ($query) {
                $query->where('service-eng', 'like', '%immediately%');
            })
            ->delete();

        return $this->sendResponse([], __('You are out of service'));
    }
}
