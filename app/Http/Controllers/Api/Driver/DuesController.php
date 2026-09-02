<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Api\BaseController;
use App\Models\Order;
use App\Services\DriverDuesService;
use App\Services\TelrService;
use Symfony\Component\HttpFoundation\JsonResponse;

class DuesController extends BaseController
{
    protected $telrService;

    public function __construct(
        TelrService $telrService,
        private DriverDuesService $driverDuesService
    ) {
        $this->telrService = $telrService;
    }

    public function getData(): JsonResponse
    {
        return $this->sendResponse($this->driverDuesService->summary(), __('Data'));
    }

    public function payDues(): JsonResponse
    {
        $currentDues = $this->driverDuesService->currentDuesAmount();

        if ($currentDues <= 0) {
            return $this->sendError(__('No dues to pay'));
        }

        $order = Order::create([
            'user_id' => auth()->user()->id,
            'amount' => $currentDues,
            'type' => Order::TYPE_PAY_DUE,
            'status' => Order::STATUS_PENDING,
            'interval' => 'one-time',
            'description' => 'Dues Payment',
        ]);

        $payload = [
            'order_id' => $order->id,
            'amount' => $currentDues,
            'description' => 'Payment for dues',

            'user_id' => auth()->user()->id,

            'customer_email' => auth()->user()->email,
            'customer_phone' => auth()->user()->{"phone-no"},

            'customer_first_name' => auth()->user()->{"user-first-name"},
            'customer_last_name' => auth()->user()->{"user-last-name"},
        ];

        return $this->intiatePayment($payload, $order);
    }

    private function intiatePayment($payload,  Order $order)
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
}
