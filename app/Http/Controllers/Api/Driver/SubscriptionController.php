<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Api\BaseController;
use App\Models\Package;
use App\Models\UserPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends BaseController
{
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'package_id'   => 'required|exists:packages,id',
            'type'    => 'required|string|in:monthly,yearly',
        ]);

        if($validator->fails()) {
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);
        }

        $userActivePackage = UserPackage::where('user_id', auth()->user()->id)
            ->where('package_id', $request->package_id)
            ->first();
        if($userActivePackage) {
            return $this->sendError(__('You are already subscribed to this package.'), [
                __('You are already subscribed to this package.')
            ], 422);
        }

        $package = Package::find($request->package_id);

        if($package->status == Package::SOON) {
            return $this->sendError(__('This package is not active.'), [
                __('This package is not active.')
            ], 422);
        }

        $data = $validator->validated();
        $data['user_id'] = auth()->user()->id;
        $data['status'] = UserPackage::STATUS_ACTIVE;
        $data['start_date'] = now();
        $data['end_date'] = $request->type == 'monthly' ? now()->addMonth() : now()->addYear();

        UserPackage::create($data);

        return $this->sendResponse([], __('You have successfully subscribed to the package.'));
    }

}
