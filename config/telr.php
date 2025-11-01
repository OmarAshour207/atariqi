<?php

return [
    'store_id'     => env('TELR_STORE_ID'),
    'auth_key'     => env('TELR_AUTH_KEY'),
    'secret_key'   => env('TELR_SECRET_KEY'),
    'endpoint'     => env('TELR_ENDPOINT', 'https://secure.telr.com/gateway/order.json'),
    'currency'     => env('TELR_CURRENCY', 'SAR'),

    'authorized_url' => env('TELR_SUCCESS_URL', '/payment/telr/success'),
    'cancelled_url' => env('TELR_CANCEL_URL', '/payment/telr/cancel'),
    'declined_url' => env('TELR_DECLINED_URL', '/payment/telr/declined'),

    'test_mode' => env('TELR_TEST_MODE', true),
];
