<?php

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Vonage\Client\Exception\Exception;

function sendNotification($data): bool
{
    $title = $data['title'];
    $body = $data['body'];
    $tokens = isset($data['tokens']) ? $data['tokens'] : [];

    $FIREBASE_API_KEY = config('services.firebase.apikey');
    $url = 'https://fcm.googleapis.com/fcm/send';

    if (empty($FIREBASE_API_KEY)) {
        return true;
    }

    $notification = [
        'title' => $title,
        'body'  => $body,
        'sound' => 'default',
        'badge' => 1
    ];

    Log::info(print_r($tokens, true));

    $firebaseData = [
        'registration_ids'  => $tokens,
        'notification'  => $notification,
    ];

    if(isset($data['external'])) {
        $firebaseData['data'] = [
            $data['external']
        ];
    }

    $dataString = json_encode($firebaseData);

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

        $result = curl_exec ( $ch );
        curl_close ( $ch );
        Log::info('cURL Request Output:', ['output' => $result]);
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
function generateCode(): int
{
    $code = mt_rand(1000, 9999);
    while (true) {
        $user = User::where('code', $code)->first();
        if(!$user)
            break;
        $code = mt_rand(1000, 9999);
    }

    return $code;
}


function sendSMS($userNumber, $code): bool
{
    $config = array();
    $config['userName'] = config('services.msegat.user_name');
    $config['numbers'] = $userNumber;
    $config['userSender'] = config('services.msegat.user_sender');
    $config['apiKey'] = config('services.msegat.api_key');
    $config['msg'] = __('Pin Code is: ') . $code;

    try {
        $codes = [1, 'M0000'];
        $response = Http::post('https://www.msegat.com/gw/sendsms.php', $config);
        $response = $response->body();
        $response = json_decode($response, true);
        $messageCode = $response['code'];
        if (!in_array($messageCode, $codes)) {
            Log::error("Msegat error API: " . $response['message']);
            return false;
        }
    } catch (Exception $e) {
        Log::error($e->getMessage());
        return false;
    }

    return true;
}
