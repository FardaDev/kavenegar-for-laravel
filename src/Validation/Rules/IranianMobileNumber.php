<?php

declare(strict_types=1);

namespace FardaDev\Kavenegar\Validation\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Validates Iranian mobile phone numbers.
 *
 * Format: 09xxxxxxxxx (11 digits starting with 09)
 * Valid prefixes: 0910-0919, 0990-0992
 */
class IranianMobileNumber implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! preg_match('/^09\d{9}$/', $value)) {
            $fail('شماره موبایل :attribute باید با فرمت 09xxxxxxxxx باشد');
        }
    }
}
