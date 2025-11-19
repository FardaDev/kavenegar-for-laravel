<?php

declare(strict_types=1);

namespace FardaDev\Kavenegar\Validation\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class KavenegarTag implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || $value === '') {
            $fail('تگ :attribute نمی‌تواند خالی باشد');

            return;
        }

        if (strlen($value) > 200) {
            $fail('تگ :attribute نباید بیشتر از 200 کاراکتر باشد');

            return;
        }

        if (! preg_match('/^[a-zA-Z0-9_-]+$/', $value)) {
            $fail('تگ :attribute فقط می‌تواند شامل حروف و اعداد انگلیسی، خط تیره و زیرخط باشد');
        }
    }
}
