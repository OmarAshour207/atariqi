<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Api\BaseController;
use App\Models\Order;
use App\Models\Package;
use App\Models\UserPackage;
use App\Models\UserPackageHistory;
use App\Services\TelrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends BaseController
{
    protected $telrService;

    public function __construct(TelrService $telrService)
    {
        $this->telrService = $telrService;
    }

    public function initiatePayment($payload, Order $order)
    {
        $orderData = [
            'order_id' => $payload['order_id'],
            'amount' => $payload['amount'],
            'description' => $payload['description'] ?? 'Order Payment',

            'user_id' => $payload['user_id'] ?? auth()->user()->id,
            'customer_email' => $payload['customer_email'],
            'customer_phone' => $payload['customer_phone'] ?? '',

            'customer_title' => $payload['customer_title'] ?? '',
            'customer_first_name' => $payload['customer_first_name'] ?? '',
            'customer_last_name' => $payload['customer_last_name'] ?? '',
        ];

        $result = $this->telrService->createSession($orderData);

        if ($result['success']) {

            $order->update([
                'payment_gateway_id' => $result['order_ref'],
            ]);

            return response()->json([
                'success' => true,
                'payment_url' => $result['payment_url'],
                'order_ref' => $result['order_ref'],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['error'],
        ], 400);
    }

    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'package_id'   => 'required|exists:packages,id',
            'type'    => 'required|string|in:monthly,yearly',
        ]);

        if($validator->fails()) {
            return $this->sendError(__('Validation Error.'), $validator->errors()->getMessages(), 422);
        }

        $userActivePackage = UserPackage::where('user_id', auth()->user()->id)->first();

        if($userActivePackage) {
            if($userActivePackage->package_id == $request->package_id && $userActivePackage->interval == $request->type) {
                return $this->sendError(__('You are already subscribed to this package.'), [
                    __('You are already subscribed to this package.')
                ], 422);
            }
        }

        $package = Package::find($request->package_id);

        if($package->status == Package::SOON) {
            return $this->sendError(__('This package is not active.'), [
                __('This package is not active.')
            ], 422);
        }

        $order = Order::create([
            'user_id' =>  auth()->user()->id,
            'package_id' => $package->id,
            'amount' => $request->type == 'monthly' ? $package->price_monthly : $package->price_annual,
            'status' => Order::STATUS_PENDING,
            'interval' => $request->type,
            'description' => $package->name_en,
            'type' => $userActivePackage ? Order::TYPE_UPGRADE : Order::TYPE_SUBSCRIPTION,
        ]);

        $payload = [
            'order_id' => $order->id,
            'amount' => $request->type == 'monthly' ? $package->price_monthly : $package->price_annual,
            'description' => $package->name_en,

            'user_id' => auth()->user()->id,

            'customer_email' => auth()->user()->email,
            'customer_phone' => auth()->user()->{"phone-no"},

            'customer_first_name' => auth()->user()->{"user-first-name"},
            'customer_last_name' =>  auth()->user()->{"user-last-name"},
        ];

        return $this->initiatePayment($payload, $order);
    }

    public function createOrder($data)
    {
        $order = Order::create($data);
        return $order;
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

        $package = Package::where('id',$request->package_id)->first();

        $order = Order::create([
            'user_id' =>  auth()->user()->id,
            'package_id' => $package->id,
            'amount' => $request->type == 'monthly' ? $package->price_monthly : $package->price_annual,
            'status' => Order::STATUS_PENDING,
            'interval' => $request->type,
            'description' => $package->name_en,
            'type' => Order::TYPE_UPGRADE,
        ]);

        $payload = [
            'order_id' => $order->id,
            'amount' => $request->type == 'monthly' ? $package->price_monthly : $package->price_annual,
            'description' => $package->name_en,

            'user_id' => auth()->user()->id,

            'customer_email' => auth()->user()->email,
            'customer_phone' => auth()->user()->{"phone-no"},

            'customer_first_name' => auth()->user()->{"user-first-name"},
            'customer_last_name' =>  auth()->user()->{"user-last-name"},
        ];

        return $this->initiatePayment($payload, $order);
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
                'status' => UserPackage::STATUS_CANCELLED,
                'start_date' => $userActivePackage->start_date,
                'end_date' => $userActivePackage->end_date,
                'canceled_date' => now(),
                'interval' => $userActivePackage->interval,
            ]);

            $userActivePackage->delete();

            $freePackage = Package::where('status', Package::FREE)->first();

            UserPackage::create([
                'package_id' => $freePackage->id,
                'user_id' => auth()->user()->id,
                'start_date' => now(),
                'end_date' => now()->addYear(),
                'status' => UserPackage::STATUS_ACTIVE,
                'interval' => 'yearly',
            ]);

            DB::commit();

            return $this->sendResponse([], __('You have successfully canceled the subscription.'));

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage(), [], 500);
        }
    }

}
