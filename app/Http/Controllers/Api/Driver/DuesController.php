<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Api\Driver\Traits\Payment;
use App\Models\FinancialDue;
use App\Models\Subscription;
use App\Models\Order;
use App\Services\TelrService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\JsonResponse;

class DuesController extends BaseController
{
    use Payment;

    protected $telrService;

    public function __construct(TelrService $telrService)
    {
        $this->telrService = $telrService;
    }

    public function getData(): JsonResponse
    {
        $lastPayDate = FinancialDue::select('amount', 'date-of-add')
            ->where('driver-id', auth()->user()->id)
            ->orderBy('id', 'desc')
            ->first();

        $dates['start_date'] = $lastPayDate?->{"date-of-add"};
        $dates['end_date'] = Carbon::now()->format('Y-m-d');

        $newRevenues = $this->getRevenue(auth()->user()->id, $dates);

        $subscriptionCost = Subscription::select('cost')->where('id', 4)->first();

        $currentDues = ($subscriptionCost->cost * $newRevenues['total']) / 100;

        return $this->sendResponse([
            'last_pay_date' => $lastPayDate?->{"date-of-add"} ? Carbon::parse($lastPayDate?->{"date-of-add"})->format('Y/m/d') : null,
            'last_pay_cost' => $lastPayDate->amount ?? 0,
            'new_revenues' => $newRevenues['total'],
            'current_dues' => $currentDues,
            'can_accept_trips' => auth()->user()->scopeCheckacceptTrips($currentDues)
        ], __('Data'));
    }

    public function payDues(): JsonResponse
    {
        $lastPayDate = FinancialDue::select('amount', 'date-of-add')
            ->where('driver-id', auth()->user()->id)
            ->orderBy('id', 'desc')
            ->first();

        $dates['start_date'] = $lastPayDate?->{"date-of-add"};
        $dates['end_date'] = Carbon::now()->format('Y-m-d');

        $newRevenues = $this->getRevenue(auth()->user()->id, $dates);

        $subscriptionCost = Subscription::select('cost')->where('id', 4)->first();

        $currentDues = ($subscriptionCost->cost * $newRevenues['total']) / 100;

        if($currentDues <= 0) {
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
