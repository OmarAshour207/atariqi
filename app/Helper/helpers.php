<?php

use App\Models\NewUserInfo;
use App\Models\User;
use App\Support\OtpBypass;
use App\Support\SaudiPhone;
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
    $header = [
        'alg' => 'RS256',
        'typ' => 'JWT'
    ];

    $now = time();
    $claims = [
        'iss' => config('services.firebase.client_email'),//$keyData['client_email'],
        'scope' => 'https://www.googleapis.com/auth/cloud-platform',
        'aud' => 'https://oauth2.googleapis.com/token',
        'exp' => $now + 3600,
        'iat' => $now
    ];

    $base64UrlHeader = base64UrlEncode(json_encode($header));
    $base64UrlClaims = base64UrlEncode(json_encode($claims));

    $signatureInput = $base64UrlHeader . '.' . $base64UrlClaims;

    $privateKey = config('services.firebase.private_key'); //$keyData['private_key'];

    openssl_sign($signatureInput, $signature, $privateKey, 'sha256WithRSAEncryption');
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

function user_upload_url(?int $userId, ?string $filename, ?string $placeholder = null): string
{
    $placeholder = $placeholder ?? 'https://ami-sni.com/wp-content/themes/consultix/images/no-image-found-360x250.png';

    if (!$filename || !$userId) {
        return $placeholder;
    }

    if (str_starts_with($filename, 'http://') || str_starts_with($filename, 'https://')) {
        return $filename;
    }

    if (str_starts_with($filename, 'uploads/')) {
        return url($filename);
    }

    return url("uploads/{$userId}/{$filename}");
}

function store_user_upload(\Illuminate\Http\UploadedFile $file, int $userId, string $prefix = 'image'): string
{
    $path = public_path("uploads/{$userId}");

    if (!\Illuminate\Support\Facades\File::exists($path)) {
        \Illuminate\Support\Facades\File::makeDirectory($path, 0777, true);
    }

    $extension = $file->extension();
    $imageName = $prefix . '_' . time() . '_' . uniqid() . '.' . $extension;
    $file->move($path, $imageName);

    return $imageName;
}

function merge_pending_user_profile_data(array $data, User $user): array
{
    $existing = NewUserInfo::where('user-id', $user->id)->first();

    $fields = [
        'user-first-name',
        'user-last-name',
        'phone-no',
        'gender',
        'email',
        'call-key-id',
        'user-stage-id',
        'university-id',
        'user-type',
    ];

    $merged = ['user-id' => $user->id];

    foreach ($fields as $field) {
        $submitted = array_key_exists($field, $data) && $data[$field] !== null && $data[$field] !== '';

        if ($submitted) {
            $merged[$field] = $data[$field];
        } elseif ($existing && filled($existing->{$field})) {
            $merged[$field] = $existing->{$field};
        } else {
            $merged[$field] = $user->{$field};
        }
    }

    if (!empty($data['image'])) {
        $merged['image'] = $data['image'];
    } elseif ($existing && filled($existing->image)) {
        $merged['image'] = $existing->image;
    } else {
        $merged['image'] = $user->image;
    }

    return $merged;
}

function pending_field_value(mixed $current, mixed $pending): string
{
    if ($pending === null || $pending === '') {
        return '';
    }

    return (string) $pending !== (string) $current ? (string) $pending : '';
}

function pending_image_filename(?string $current, ?string $pending): ?string
{
    if (!$pending || $pending === $current) {
        return null;
    }

    return $pending;
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

function resolve_otp_code(string $phoneNo): int
{
    if (OtpBypass::isBypassPhone($phoneNo)) {
        return OtpBypass::fixedCode();
    }

    return generateCode();
}

function deliver_otp_code(User $user, int $code, string $phoneNo): bool
{
    if (OtpBypass::isBypassPhone($phoneNo)) {
        return true;
    }

    $phoneNumber = SaudiPhone::toE164ForUser($user->loadMissing('callingKey'));

    if (! $phoneNumber) {
        return false;
    }

    return sendSMS($phoneNumber, $code);
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

/**
 * Human-readable trip action label by trip type (daily|weekly|immediate).
 */
function trip_action_label($action, string $type = 'daily'): string
{
    $action = (int) $action;

    if ($type === 'immediate') {
        return match ($action) {
            0 => __('trip_action.immediate.0'),
            1 => __('trip_action.immediate.1'),
            2 => __('trip_action.immediate.2'),
            3 => __('trip_action.immediate.3'),
            4 => __('trip_action.immediate.4'),
            5 => __('trip_action.immediate.5'),
            7 => __('trip_action.immediate.7'),
            default => __('Unknown') . " ($action)",
        };
    }

    // daily & weekly share the same lifecycle
    return match ($action) {
        0 => __('trip_action.scheduled.0'),
        1 => __('trip_action.scheduled.1'),
        2 => __('trip_action.scheduled.2'),
        3 => __('trip_action.scheduled.3'),
        4 => __('trip_action.scheduled.4'),
        5 => __('trip_action.scheduled.5'),
        6 => __('trip_action.scheduled.6'),
        default => __('Unknown') . " ($action)",
    };
}

function trip_action_badge_class($action, string $type = 'daily'): string
{
    $action = (int) $action;

    if ($type === 'immediate') {
        return match ($action) {
            1 => 'success',
            2 => 'primary',
            3, 4, 0 => 'danger',
            5 => 'dark',
            7 => 'secondary',
            default => 'light',
        };
    }

    return match ($action) {
        0 => 'secondary',
        1 => 'success',
        2, 5 => 'danger',
        3 => 'primary',
        4 => 'info',
        6 => 'dark',
        default => 'light',
    };
}

/**
 * Weekly group status (booking.status), not the individual trip action.
 */
function weekly_group_status_label($status): string
{
    return match ((int) $status) {
        0 => __('trip_status.weekly.0'),
        1 => __('trip_status.weekly.1'),
        2 => __('trip_status.weekly.2'),
        default => __('Unknown') . " ($status)",
    };
}

function weekly_group_status_badge_class($status): string
{
    return match ((int) $status) {
        0 => 'secondary',
        1 => 'success',
        2 => 'danger',
        default => 'light',
    };
}
