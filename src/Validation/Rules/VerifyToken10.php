<?php

declare(strict_types=1);

namespace FardaDev\Kavenegar\Validation\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class VerifyToken10 implements ValidationRule
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

        // Count spaces (max 5 allowed for token10)
        $spaceCount = substr_count($value, ' ');
        if ($spaceCount > 5) {
            $fail(':attribute می‌تواند حداکثر 5 فاصله (Space) داشته باشد');
        }
    }
}
