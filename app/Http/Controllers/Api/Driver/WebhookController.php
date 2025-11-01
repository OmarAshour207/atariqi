<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Api\BaseController;
use App\Models\Order;
use App\Models\UserPackage;
use App\Models\UserPackageHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WebhookController extends BaseController
{
    public function handleWebhook(Request $request)
    {
        Log::channel('payment')->info('Telr Webhook Received:', $request->all());

        $order = Order::where('id', $request->input('tran_cartid'))
            ->where('status', Order::STATUS_PENDING)
            ->first();

        if($request->input('tran_status') === 'A') {
            Log::channel('payment')->info('Payment Authorized for Order Ref: ' . $request->input('tran_cartid'));

            if($order && $order->type === Order::TYPE_SUBSCRIPTION) {
                return $this->subscribe($order);
            }

            if($order && $order->type === Order::TYPE_UPGRADE) {
                return $this->upgrade($order);
            }

            return response()->json(['status' => 'ignored']);
        }

        $order->update(['status' => Order::STATUS_FAILED]);
        Log::channel('payment')->info('Payment Failed for Order Ref: ' . $request->input('tran_cartid'));

        return response()->json(['status' => 'success']);
    }

    public function subscribe(Order $order)
    {
        try {
            DB::beginTransaction();

            Log::channel('payment')->info('Processing subscription for Order ID: ' . $order->id);

            $order->update(['status' => Order::STATUS_COMPLETED]);

            UserPackageHistory::create([
                'user_id' => $order->user_id,
                'package_id' => $order->package_id,
                'status' => UserPackage::STATUS_ACTIVE,
                'start_date' => now(),
                'end_date' => $order->interval == 'monthly' ? now()->addMonth() : now()->addYear(),
                'interval' => $order->interval,
            ]);

            UserPackage::create([
                'user_id' => $order->user_id,
                'package_id' => $order->package_id,
                'status' => UserPackage::STATUS_ACTIVE,
                'start_date' => now(),
                'end_date' => $order->interval == 'monthly' ? now()->addMonth() : now()->addYear(),
                'interval' => $order->interval,
            ]);

            DB::commit();

            Log::channel('payment')->info('Completed subscription for Order ID: ' . $order->id);

            return $this->sendResponse([], __('You have successfully subscribed to the package.'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('payment')->info('An error occurred while processing the webhook:', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
            return $this->sendError($e->getMessage(), [], 500);
        }
    }

    public function upgrade(Order $order)
    {
        try {
            $userActivePackage = UserPackage::where('user_id', $order->user_id)
                ->where('status', UserPackage::STATUS_ACTIVE)
                ->first();

            DB::beginTransaction();

            $order->update(['status' => Order::STATUS_COMPLETED]);

            UserPackageHistory::create([
                'user_id' => $order->user_id,
                'package_id' => $userActivePackage->package_id,
                'status' => $userActivePackage->status,
                'start_date' => $userActivePackage->start_date,
                'end_date' => $userActivePackage->end_date,
                'canceled_date' => now(),
                'interval' => $userActivePackage->interval,
            ]);

            $userActivePackage->delete();

            UserPackage::create([
                'user_id' => $order->user_id,
                'package_id' => $order->package_id,
                'status' => UserPackage::STATUS_ACTIVE,
                'start_date' => now(),
                'end_date' => $order->interval == 'monthly' ? now()->addMonth() : now()->addYear(),
                'interval' => $order->interval,
            ]);

            DB::commit();

            return $this->sendResponse([], __('You have successfully upgraded the package.'));

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage(), [], 500);
        }
    }

}
