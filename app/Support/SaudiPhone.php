<?php

namespace App\Support;

use App\Models\CallingKey;
use App\Models\User;

class SaudiPhone
{
    public static function saudiCallingKey(): string
    {
        return (string) config('otp.saudi_calling_key', '966');
    }

    public static function saudiCallingKeyIds(): array
    {
        static $ids;

        if ($ids === null) {
            $ids = CallingKey::query()
                ->where('call-key', self::saudiCallingKey())
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();
        }

        return $ids;
    }

    public static function isSaudiCallingKeyId(?int $callKeyId): bool
    {
        if (! $callKeyId) {
            return false;
        }

        return in_array($callKeyId, self::saudiCallingKeyIds(), true);
    }

    public static function normalizeMobile(string $phone): ?string
    {
        $digits = preg_replace('/\D/', '', $phone) ?? '';

        if (str_starts_with($digits, self::saudiCallingKey())) {
            $digits = substr($digits, strlen(self::saudiCallingKey()));
        }

        $digits = ltrim($digits, '0');

        return preg_match('/^5\d{8}$/', $digits) ? $digits : null;
    }

    public static function resolve(int $callKeyId, string $phone): ?string
    {
        if (! self::isSaudiCallingKeyId($callKeyId)) {
            return null;
        }

        $mobile = self::normalizeMobile($phone);

        return $mobile ? self::saudiCallingKey() . $mobile : null;
    }

    public static function resolveForUser(User $user): ?string
    {
        $callKey = (string) ($user->callingKey?->{'call-key'} ?? '');

        if ($callKey !== self::saudiCallingKey()) {
            return null;
        }

        return self::resolve((int) $user->{'call-key-id'}, (string) $user->{'phone-no'});
    }

    public static function toE164(int $callKeyId, string $phone): ?string
    {
        $resolved = self::resolve($callKeyId, $phone);

        return $resolved ? '+' . $resolved : null;
    }

    public static function toE164ForUser(User $user): ?string
    {
        $resolved = self::resolveForUser($user);

        return $resolved ? '+' . $resolved : null;
    }
}
