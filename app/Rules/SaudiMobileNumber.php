<?php

namespace App\Rules;

use App\Support\OtpBypass;
use App\Support\SaudiPhone;
use Illuminate\Contracts\Validation\Rule;

class SaudiMobileNumber implements Rule
{
    public function passes($attribute, $value): bool
    {
        if (OtpBypass::isBypassPhone((string) $value)) {
            return true;
        }

        return SaudiPhone::normalizeMobile((string) $value) !== null;
    }

    public function message(): string
    {
        return __('Only Saudi mobile numbers are allowed.');
    }
}
