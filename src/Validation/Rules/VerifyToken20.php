<?php

declare(strict_types=1);

namespace FardaDev\Kavenegar\Validation\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class VerifyToken20 implements ValidationRule
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

        // Count spaces (max 8 allowed for token20)
        $spaceCount = substr_count($value, ' ');
        if ($spaceCount > 8) {
            $fail(':attribute می‌تواند حداکثر 8 فاصله (Space) داشته باشد');
        }
    }
}
