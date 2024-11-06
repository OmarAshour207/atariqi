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

    $serverKey = getToken();
    $url = config('services.firebase.url');

    if (empty($tokens) || !$serverKey) {
        return false;
    }

//        'sound' => 'default',
//        'badge' => 1

    $firebaseData = [
        "message" => [
            "token" => $tokens[0],
            "notification"  => [
                "title" => $title,
                "body"  => $body
            ],
            "data" => [
                "title" => $title,
                "body"  => $body,
            ]
        ]
    ];

    $encodedData = json_encode($firebaseData);

    if(isset($data['external'])) {
        $firebaseData['data'] = [
            $data['external']
        ];
    }

    $headers = array (
        'Authorization: Bearer ' . $serverKey,
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

        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $encodedData );

        $result = curl_exec ( $ch );
        curl_close ( $ch );
        Log::info('cURL Request Output:', ['output' => $result]);
    } catch (\Exception $e) {
        Log::error("Notification can't send");
    }

    return true;
}

function getToken()
{
    $keyFilePath = public_path('documents/firebase.json');
    $keyData = json_decode(file_get_contents($keyFilePath), true);

    $header = [
        'alg' => 'RS256',
        'typ' => 'JWT'
    ];

    $now = time();
    $claims = [
        'iss' => $keyData['client_email'],
        'scope' => 'https://www.googleapis.com/auth/cloud-platform',
        'aud' => 'https://oauth2.googleapis.com/token',
        'exp' => $now + 3600,
        'iat' => $now
    ];

    $base64UrlHeader = base64UrlEncode(json_encode($header));
    $base64UrlClaims = base64UrlEncode(json_encode($claims));

    $signatureInput = $base64UrlHeader . '.' . $base64UrlClaims;

    openssl_sign($signatureInput, $signature, $keyData['private_key'], 'sha256WithRSAEncryption');
    $base64UrlSignature = base64UrlEncode($signature);

    $jwt = $signatureInput . '.' . $base64UrlSignature;

    $postFields = http_build_query([
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion' => $jwt
    ]);

    $ch = curl_init ();

    curl_setopt($ch, CURLOPT_URL, 'https://oauth2.googleapis.com/token');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

    $response = curl_exec($ch);

    if ($response === FALSE) {
        Log::info("Failed getting the access token from Firebase");
        return;
    }

    $responseData = json_decode($response, true);
    curl_close($ch);

    return $responseData['access_token'];
}

function base64UrlEncode($data)
{
    return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
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

function sendSMS($userNumber, $code = null, $message = null): bool
{
    $config = array();
    $config['userName'] = config('services.msegat.user_name');
    $config['numbers'] = $userNumber;
    $config['userSender'] = config('services.msegat.user_sender');
    $config['apiKey'] = config('services.msegat.api_key');
    $config['msg'] = $message ?? __('Pin Code is: ') . $code;

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
