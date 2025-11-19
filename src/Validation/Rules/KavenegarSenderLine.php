<?php

declare(strict_types=1);

namespace FardaDev\Kavenegar\Validation\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Validates Kavenegar sender line numbers.
 *
 * Sender can be:
 * - Plain numeric: 10004346, 30002626 (4-15 digits)
 * - With + prefix: +9810004346
 * - With 00 prefix: 009810004346
 *
 * Based on official documentation format examples.
 *
 * @see https://kavenegar.com/rest.html
 */
class KavenegarSenderLine implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || trim($value) === '') {
            $fail('شماره فرستنده :attribute نمی‌تواند خالی باشد');

            return;
        }

        // Allow +98... or 0098... or plain numeric
        // Pattern: optional + or 00, then 4-15 digits
        if (! preg_match('/^(\+|00)?\d{4,15}$/', $value)) {
            $fail('شماره فرستنده :attribute نامعتبر است (فرمت مجاز: 10004346 یا +9810004346 یا 009810004346)');
        }
    }
}
