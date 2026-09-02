<?php

return [
    'max_attempts' => (int) env('OTP_MAX_ATTEMPTS', 3),
    'decay_seconds' => (int) env('OTP_DECAY_SECONDS', 86400),
    'saudi_calling_key' => env('OTP_SAUDI_CALLING_KEY', '966'),
];
