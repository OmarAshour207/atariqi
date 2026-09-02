<?php

namespace App\Rules;

use App\Support\SaudiPhone;
use Illuminate\Contracts\Validation\Rule;

class SaudiMobileNumber implements Rule
{
    public function passes($attribute, $value): bool
    {
        return SaudiPhone::normalizeMobile((string) $value) !== null;
    }

    public function message(): string
    {
        return __('Only Saudi mobile numbers are allowed.');
    }
}
