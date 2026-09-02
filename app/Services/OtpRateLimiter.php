<?php

namespace App\Services;

use Illuminate\Support\Facades\RateLimiter;

class OtpRateLimiter
{
    public function key(string $fullPhone): string
    {
        return 'otp-send:' . sha1($fullPhone);
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
        return RateLimiter::tooManyAttempts($this->key($fullPhone), $this->maxAttempts());
    }

    public function availableIn(string $fullPhone): int
    {
        return RateLimiter::availableIn($this->key($fullPhone));
    }

    public function remaining(string $fullPhone): int
    {
        return max(0, $this->maxAttempts() - RateLimiter::attempts($this->key($fullPhone)));
    }

    public function hit(string $fullPhone): void
    {
        RateLimiter::hit($this->key($fullPhone), $this->decaySeconds());
    }
}
