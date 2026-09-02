<?php

namespace App\Support;

class OtpBypass
{
    public static function fixedCode(): int
    {
        return (int) config('otp.bypass_code', 1234);
    }

    public static function phoneNumbers(): array
    {
        return config('otp.bypass_phones', []);
    }

    public static function isBypassPhone(string $phone): bool
    {
        $incoming = self::canonicalPhone($phone);

        if ($incoming === '') {
            return false;
        }

        foreach (self::phoneNumbers() as $listed) {
            if (self::canonicalPhone((string) $listed) === $incoming) {
                return true;
            }
        }

        return false;
    }

    public static function canonicalPhone(string $phone): string
    {
        $digits = self::digitsOnly($phone);

        if ($digits === '') {
            return '';
        }

        $saudiKey = SaudiPhone::saudiCallingKey();

        if (str_starts_with($digits, $saudiKey)) {
            $digits = substr($digits, strlen($saudiKey));
        }

        $digits = ltrim($digits, '0');

        $normalized = SaudiPhone::normalizeMobile($digits);

        return $normalized ?? $digits;
    }

    private static function digitsOnly(string $phone): string
    {
        return preg_replace('/\D/', '', $phone) ?? '';
    }
}
