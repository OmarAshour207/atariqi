<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Api\BaseController;
use App\Models\Package;
use App\Models\UserPackage;
use App\Models\UserPackageHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        $userActivePackage = UserPackage::where('user_id', auth()->user()->id)->get();

        foreach($userActivePackage as $activePackage) {
            if($activePackage->package_id == $request->package_id) {
                return $this->sendError(__('You are already subscribed to this package.'), [
                    __('You are already subscribed to this package.')
                ], 422);
            }

            if($activePackage->status != Package::FREE) {
                return $this->sendError(__('You are already subscribed to package, upgrade package.'), [
                    __('You are already subscribed to package, upgrade package.')
                ], 422);
            }

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
        $data['interval'] = $request->type;

        UserPackage::create($data);

        return $this->sendResponse([], __('You have successfully subscribed to the package.'));
    }

    public function upgrade(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'package_id'   => 'required|exists:packages,id',
            'type'    => 'required|string|in:monthly,yearly',
        ]);

        if($validator->fails()) {
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);
        }

        $userActivePackage = UserPackage::where('user_id', auth()->user()->id)->first();

        if(!$userActivePackage) {
            return $this->sendError(__('You do not have an active subscription.'), [], 422);
        }

        if($userActivePackage->package_id == $request->package_id && $userActivePackage->interval == $request->type) {
            return $this->sendError(__('You are already subscribed to this package.'), [
                __('You are already subscribed to this package.')
                ], 422);
        }

        if($userActivePackage->interval == $request->type) {
            return $this->sendError(__("You can't upgrade to this package."), [
                __("You can't upgrade to this package.")
            ], 422);
        }

        try {
            DB::beginTransaction();

            UserPackageHistory::create([
                'user_id' => auth()->user()->id,
                'package_id' => $userActivePackage->package_id,
                'status' => $userActivePackage->status,
                'start_date' => $userActivePackage->start_date,
                'end_date' => $userActivePackage->end_date,
                'canceled_date' => now(),
                'interval' => $userActivePackage->interval,
            ]);

            $userActivePackage->delete();

            UserPackage::create([
                'user_id' => auth()->user()->id,
                'package_id' => $request->package_id,
                'status' => UserPackage::STATUS_ACTIVE,
                'start_date' => now(),
                'end_date' => $request->type == 'monthly' ? now()->addMonth() : now()->addYear(),
                'interval' => $request->type,
            ]);

            DB::commit();

            return $this->sendResponse([], __('You have successfully upgraded the package.'));

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage(), [], 500);
        }
    }

    public function cancel()
    {
        $userActivePackage = UserPackage::where('user_id', auth()->user()->id)
            ->where('status', UserPackage::STATUS_ACTIVE)
            ->first();

        if(!$userActivePackage) {
            return $this->sendError(__('You do not have an active subscription.'), [], 422);
        }

        if($userActivePackage->status == UserPackage::STATUS_CANCELLED) {
            return $this->sendError(__('You have already canceled your subscription.'), [], 422);
        }

        try {
            DB::beginTransaction();

            UserPackageHistory::create([
                'user_id' => auth()->user()->id,
                'package_id' => $userActivePackage->package_id,
                'status' => $userActivePackage->status,
                'start_date' => $userActivePackage->start_date,
                'end_date' => $userActivePackage->end_date,
                'canceled_date' => now(),
                'interval' => $userActivePackage->interval,
            ]);

            $userActivePackage->update([
                'status' => UserPackage::STATUS_CANCELLED,
            ]);

            DB::commit();

            return $this->sendResponse([], __('You have successfully canceled the subscription.'));

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage(), [], 500);
        }
    }

}
