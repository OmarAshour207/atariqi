<?php

namespace App\Http\Controllers\Api\Concerns;

use App\Models\User;
use App\Services\OtpRateLimiter;
use App\Support\SaudiPhone;
use Illuminate\Http\JsonResponse;

trait GuardsOtpSending
{
    protected function rejectNonSaudiPhone(): JsonResponse
    {
        return $this->sendError('s_saudiPhoneOnly', [__('Only Saudi phone numbers are allowed.')], 422);
    }

    protected function rejectOtpRateLimit(OtpRateLimiter $limiter, string $fullPhone): JsonResponse
    {
        $retryIn = $limiter->availableIn($fullPhone);
        $hours = max(1, (int) ceil($retryIn / 3600));

        return $this->sendError('s_otpRateLimit', [
            __('You can request the verification code up to :max times every 24 hours. Please try again in about :hours hours.', [
                'max' => $limiter->maxAttempts(),
                'hours' => $hours,
            ]),
        ], 429);
    }

    protected function guardOtpForUser(User $user, OtpRateLimiter $limiter): ?JsonResponse
    {
        $fullPhone = SaudiPhone::resolveForUser($user);

        if (! $fullPhone) {
            return $this->rejectNonSaudiPhone();
        }

        if ($limiter->tooManyAttempts($fullPhone)) {
            return $this->rejectOtpRateLimit($limiter, $fullPhone);
        }

        $limiter->hit($fullPhone);

        return null;
    }

    protected function guardOtpForRegistration(int $callKeyId, string $phone, OtpRateLimiter $limiter): ?JsonResponse
    {
        $fullPhone = SaudiPhone::resolve($callKeyId, $phone);

        if (! $fullPhone) {
            return $this->rejectNonSaudiPhone();
        }

        if ($limiter->tooManyAttempts($fullPhone)) {
            return $this->rejectOtpRateLimit($limiter, $fullPhone);
        }

        $limiter->hit($fullPhone);

        return null;
    }
}
