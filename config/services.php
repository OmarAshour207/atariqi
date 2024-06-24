<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'twilio'  => [
        'account_sid'   => env('TWILIO_ACCOUNT_SID'),
        'auth_token'    => env('TWILIO_AUTH_TOKEN'),
        'phone_number'  => env('TWILIO_PHONE_NUMBER'),
        'service_id'    => env('TWILIO_SERVICE_ID'),
    ],

    'nexmo'  => [
        'key'       => env('NEXMO_KEY'),
        'secret'    => env('NEXMO_SECRET'),
    ],

    'msegat'  => [
        'api_key'        => env('APIKEY'),
        'user_sender'    => env('USER_SENDER'),
        'user_name'      => env('USER_NAME'),
        'msg_encoding'   => env('MSG_ENCODING', 'UTF8'),
    ],

    'firebase'  => [
        'apikey'    => env('FIREBASE_API_KEY'),
        'url'       => env("FIREBASE_API_URL", "https://fcm.googleapis.com/fcm/send")
    ]
];
