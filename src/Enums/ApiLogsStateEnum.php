<?php

declare(strict_types=1);

namespace FardaDev\Kavenegar\Enums;

/**
 * API logs configuration state enum.
 * Based on docs/api-endpoints.md - apilogs has three possible values.
 */
enum ApiLogsStateEnum: string
{
    case ENABLED = 'enabled';
    case DISABLED = 'disabled';
    case JUST_FAULTS = 'justfaults';

    /**
     * Create enum from API response value.
     */
    public static function fromApiValue(string $value): self
    {
        $normalized = strtolower(trim($value));

        return match ($normalized) {
            'enabled' => self::ENABLED,
            'disabled' => self::DISABLED,
            'justfaults' => self::JUST_FAULTS,
            default => throw new \ValueError("Invalid API logs state value: {$value}"),
        };
    }

    public function isEnabled(): bool
    {
        return $this === self::ENABLED;
    }

    public function isDisabled(): bool
    {
        return $this === self::DISABLED;
    }

    public function isJustFaults(): bool
    {
        return $this === self::JUST_FAULTS;
    }

    public function description(): string
    {
        return match ($this) {
            self::ENABLED => 'لاگ کلیه درخواست‌ها ذخیره می‌شود',
            self::DISABLED => 'لاگ هیچ درخواستی ذخیره نمی‌شود',
            self::JUST_FAULTS => 'فقط لاگ خطاها ذخیره می‌شود',
        };
    }
}
