<?php

declare(strict_types=1);

namespace FardaDev\Kavenegar\Validation\Rules;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

final class DateRange implements ValidationRule, DataAwareRule
{
    /**
     * @var array<string, mixed>
     */
    protected array $data = [];

    public function __construct(
        private readonly int $maxDays = 1
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $startdate = $this->data['startdate'] ?? null;
        $enddate = $value;

        if ($startdate === null || $enddate === null) {
            return;
        }

        if (! is_numeric($startdate) || ! is_numeric($enddate)) {
            return;
        }

        $difference = (int) $enddate - (int) $startdate;

        if ($difference > ($this->maxDays * 86400)) {
            $dayWord = $this->maxDays === 1 ? 'یک روز' : "{$this->maxDays} روز";
            $fail("بازه زمانی نمی‌تواند بیشتر از {$dayWord} باشد");
        }
    }
}
