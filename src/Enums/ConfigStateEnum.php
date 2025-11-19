<?php

declare(strict_types=1);

namespace FardaDev\Kavenegar\Enums;

enum ConfigStateEnum: string
{
    case ENABLED = 'enabled';
    case DISABLED = 'disabled';

    public static function fromApiValue(mixed $value): self
    {
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
