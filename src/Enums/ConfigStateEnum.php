<?php

declare(strict_types=1);

namespace FardaDev\Kavenegar\Enums;

/**
 * Configuration state enum for account settings.
 * Handles various formats returned by Kavenegar API (enabled/disabled, true/false, 1/0).
 */
enum ConfigStateEnum: string
{
    case ENABLED = 'enabled';
    case DISABLED = 'disabled';

    /**
     * Create enum from various API response formats.
     * API may return: 'enabled', 'disabled', 'true', 'false', '1', '0', 1, 0
     */
    public static function fromApiValue(mixed $value): self
    {
        // Normalize to string for comparison
        $normalized = strtolower(trim((string) $value));

        return match ($normalized) {
            'enabled', 'true', '1' => self::ENABLED,
            'disabled', 'false', '0' => self::DISABLED,
            default => throw new \ValueError("Invalid config state value: {$value}"),
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

    public function toApiValue(): string
    {
        return $this->value;
    }
}
