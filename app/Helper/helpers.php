<?php

use Illuminate\Support\Facades\Log;

function sendNotification($data): bool
{
    $title = $data['title'];
    $body = $data['body'];
    $tokens = isset($data['tokens']) ? $data['tokens'] : [];

    $FIREBASE_API_KEY = config('services.firebase.apikey');
    $url = 'https://fcm.googleapis.com/fcm/send';

    if (empty($FIREBASE_API_KEY))
        return true;
    $notification = [
        'title' => $title,
        'body'  => $body,
        'sound' => 'default',
        'badge' => 1
    ];

    $data = [
        'registration_ids'  => $tokens,
        'notification'  => $notification
    ];

    $dataString = json_encode($data);

    $headers = array (
        'Authorization: key=' . $FIREBASE_API_KEY,
        'Content-Type: application/json'
    );

    try {
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_POST, true );
        curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $dataString );

        curl_exec ( $ch );
        curl_close ( $ch );
    } catch (\Exception $e) {
        Log::error("Notification can't send");
    }

    return true;
}

function convertArabicDateToEnglish($date)
{
    return strtr($date, [
        '٠' => '0',
        '١' => '1',
        '٢' => '2',
        '٣' => '3',
        '٤' => '4',
        '٥' => '5',
        '٦' => '6',
        '٧' => '7',
        '٨' => '8',
        '٩' => '9',
    ]);
}
