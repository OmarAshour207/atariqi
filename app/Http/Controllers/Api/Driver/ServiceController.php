<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Api\BaseController;
use App\Models\DriversServices;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceController extends BaseController
{
    public function start(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'service_id' => 'required|numeric|exists:services,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);
        }

        $user = User::with(['driverInfo', 'driverCar', 'driverNeighborhood', 'driverSchedule'])
            ->whereId(auth()->user()->id)
            ->first();

        foreach ($user->getRelations() as $relation) {
            if ($relation?->exists() == null) {
                return $this->sendError(
                    __('Please complete your information to start working with us.'),
                    [__('Please complete your information to start working with us')],
                    422
                );
            }
        }

        DriversServices::firstOrCreate([
            'driver-id' => auth()->user()->id,
            'service-id' => $request->service_id,
        ]);

        $user->update(['is-receiving-rides' => true]);

        return $this->sendResponse([
            'is-receiving-rides' => true,
        ], __('Your service started'));
    }

    public function stop(): JsonResponse
    {
        DriversServices::where('driver-id', auth()->user()->id)
            ->whereHas('service', function ($query) {
                $query->where('service-eng', 'like', '%immediately%');
            })
            ->delete();

        auth()->user()->update(['is-receiving-rides' => false]);

        return $this->sendResponse([
            'is-receiving-rides' => false,
        ], __('You are out of service'));
    }

    public function toggleReceivingRides(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'enabled' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);
        }

        $enabled = $request->boolean('enabled');
        $user = auth()->user();

        if ($enabled) {
            $profileError = $this->validateDriverProfileComplete($user);
            if ($profileError) {
                return $profileError;
            }

            if ((int) $user->approval !== 1) {
                return $this->sendError(__('Your account is not approved to receive rides.'), [], 403);
            }
        }

        $user->update(['is-receiving-rides' => $enabled]);

        return $this->sendResponse([
            'is-receiving-rides' => $enabled,
        ], $enabled
            ? __('You are now receiving rides.')
            : __('You are no longer receiving rides.'));
    }

    public function receivingRidesStatus(): JsonResponse
    {
        return $this->sendResponse([
            'is-receiving-rides' => (bool) auth()->user()->{'is-receiving-rides'},
        ], __('Data'));
    }

    private function validateDriverProfileComplete(User $user): ?JsonResponse
    {
        $user->loadMissing(['driverInfo', 'driverCar', 'driverNeighborhood', 'driverSchedule']);

        foreach ($user->getRelations() as $relation) {
            if ($relation?->exists() == null) {
                return $this->sendError(
                    __('Please complete your information to start working with us.'),
                    [__('Please complete your information to start working with us')],
                    422
                );
            }
        }

        return null;
    }
}
