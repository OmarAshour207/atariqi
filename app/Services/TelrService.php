<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelrService
{
    protected $config;

    public function __construct()
    {
        $this->config = config('telr');
    }

    /**
     * Create a payment session (Hosted Payment Page)
     */
    public function createSession(array $orderData): array
    {
        $payload = [
            'method'        => 'create',
            'store'         => $this->config['store_id'],
            'authkey'      => $this->config['auth_key'],

            'order'         => [
                'cartid'    => $orderData['order_id'],
                'amount'    => number_format($orderData['amount'], 2, '.', ''),
                'description'=> $orderData['description'] ?? '',
                'currency'  => $this->config['currency'],
                'test'      => $this->config['test_mode'] ? 1 : 0,
            ],

            'customer'      => [
                'ref' => $orderData['user_id'] ?? '',
                'name' => [
                    'title' => $orderData['customer_title'] ?? '',
                    'forenames' => $orderData['customer_first_name'] ?? '',
                    'surname' => $orderData['customer_last_name'] ?? '',
                ],
                'email' => $orderData['customer_email'] ?? '',
                'phone' => $orderData['customer_phone'] ?? '',
            ],

            'return' => [
                'authorized'    => $this->config['authorized_url'],
                'cancelled'     => $this->config['cancelled_url'],
                'declined'    => $this->config['declined_url'],
            ]
        ];

        $response = Http::withHeaders(['Content-Type' => 'application/json'])
                        ->post($this->config['endpoint'], $payload)
                        ->json();

        if(isset($response['error'])) {
            return [
                'success' => false,
                'error' => $response['error']['message'] ?? 'Unknown error occurred',
            ];
        }

        return [
            'success' => true,
            'payment_url' => $response['order']['url'] ?? '',
            'order_ref' => $response['order']['ref'] ?? '',
        ];
    }

    /**
     * Check transaction status by order reference
     */
    public function checkStatus(string $orderRef): array
    {
        $payload = [
            'method' => 'check',
            'store' => $this->config['store_id'],
            'authkey' => $this->config['auth_key'],
            'order' => [
                'ref' => $orderRef
            ],
        ];

        $response = Http::withHeaders(['Content-Type' => 'application/json'])
                        ->post($this->config['endpoint'], $payload)
                        ->json();

        return $response;
    }
}
