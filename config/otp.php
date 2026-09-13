<?php

return [
    'max_attempts' => (int) env('OTP_MAX_ATTEMPTS', 3),
    'decay_seconds' => (int) env('OTP_DECAY_SECONDS', 86400),
    'saudi_calling_key' => env('OTP_SAUDI_CALLING_KEY', '966'),
    'bypass_code' => (int) env('OTP_BYPASS_CODE', 1234),
    'bypass_phones' => array_values(array_filter(array_map(
        'trim',
        explode(',', env('OTP_BYPASS_PHONES', '546650866,1124988930,504774399'))
    ))),
];
