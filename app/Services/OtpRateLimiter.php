<?php

namespace App\Services;

use App\Support\OtpBypass;
use Illuminate\Support\Facades\RateLimiter;

class OtpRateLimiter
{
    public function key(string $fullPhone): string
    {
        return 'otp-send:' . sha1($this->canonicalKeyPhone($fullPhone));
    }

    public function maxAttempts(): int
    {
        return max(1, (int) config('otp.max_attempts', 3));
    }

    public function decaySeconds(): int
    {
        return max(60, (int) config('otp.decay_seconds', 86400));
    }

    public function tooManyAttempts(string $fullPhone): bool
    {
        if ($this->shouldBypass($fullPhone)) {
            $this->clear($fullPhone);

            return false;
        }

        return RateLimiter::tooManyAttempts($this->key($fullPhone), $this->maxAttempts());
    }

    public function availableIn(string $fullPhone): int
    {
        if ($this->shouldBypass($fullPhone)) {
            return 0;
        }

        return RateLimiter::availableIn($this->key($fullPhone));
    }

    public function remaining(string $fullPhone): int
    {
        if ($this->shouldBypass($fullPhone)) {
            return $this->maxAttempts();
        }

        return max(0, $this->maxAttempts() - RateLimiter::attempts($this->key($fullPhone)));
    }

    public function hit(string $fullPhone): void
    {
        if ($this->shouldBypass($fullPhone)) {
            return;
        }

        RateLimiter::hit($this->key($fullPhone), $this->decaySeconds());
    }

    public function clear(string $fullPhone): void
    {
        RateLimiter::clear($this->key($fullPhone));
    }

    private function shouldBypass(string $phone): bool
    {
        return OtpBypass::isBypassPhone($phone);
    }

    private function canonicalKeyPhone(string $phone): string
    {
        return OtpBypass::canonicalPhone($phone) ?: $phone;
    }
}
