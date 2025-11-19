<?php

declare(strict_types=1);

namespace FardaDev\Kavenegar\Validation\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class VerifyToken implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail(':attribute باید یک رشته متنی باشد');

            return;
        }

        if (strlen($value) > 100) {
            $fail(':attribute نباید بیشتر از 100 کاراکتر باشد');

            return;
        }

        // Check for spaces (not allowed in token, token2, token3)
        if (str_contains($value, ' ')) {
            $fail(':attribute نمی‌تواند حاوی فاصله (Space) باشد');
        }
    }
}
