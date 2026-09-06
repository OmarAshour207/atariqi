<?php

namespace App\Rules;

use App\Support\OtpBypass;
use Illuminate\Contracts\Validation\Rule;

class SaudiMobileNumber implements Rule
{
    public function passes($attribute, $value): bool
    {
        if (OtpBypass::isBypassPhone((string) $value)) {
            return true;
        }

        // Temporarily skip Saudi (966) mobile number validation.
        // return SaudiPhone::normalizeMobile((string) $value) !== null;
        return true;
    }

    public function message(): string
    {
        return __('Only Saudi mobile numbers are allowed.');
    }
}
