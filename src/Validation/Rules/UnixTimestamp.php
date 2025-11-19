<?php

declare(strict_types=1);

namespace FardaDev\Kavenegar\Validation\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class UnixTimestamp implements ValidationRule
{
    public function __construct(
        private readonly bool $allowPast = true,
        private readonly bool $allowFuture = true
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_numeric($value) || $value < 0) {
            $fail(':attribute باید یک تاریخ معتبر به فرمت UnixTime باشد');

            return;
        }

        $timestamp = (int) $value;
        $now = time();

        if (! $this->allowPast && $timestamp < $now) {
            $fail(':attribute نمی‌تواند در گذشته باشد');

            return;
        }

        if (! $this->allowFuture && $timestamp > $now) {
            $fail(':attribute نمی‌تواند در آینده باشد');
        }
    }
}
