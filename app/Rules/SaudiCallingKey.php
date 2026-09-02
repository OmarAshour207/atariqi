<?php

namespace App\Rules;

use App\Support\SaudiPhone;
use Illuminate\Contracts\Validation\Rule;

class SaudiCallingKey implements Rule
{
    public function passes($attribute, $value): bool
    {
        return SaudiPhone::isSaudiCallingKeyId((int) $value);
    }

    public function message(): string
    {
        return __('Only Saudi phone numbers are allowed.');
    }
}
