<?php

declare(strict_types=1);

use FardaDev\Kavenegar\Enums\ConfigStateEnum;

describe('ConfigStateEnum', function () {
    it('has both states defined', function () {
        expect(ConfigStateEnum::cases())->toHaveCount(2)
            ->and(ConfigStateEnum::ENABLED->value)->toBe('enabled')
            ->and(ConfigStateEnum::DISABLED->value)->toBe('disabled');
    });

    it('creates from various API value formats', function () {
        // Test 'enabled' variations
        expect(ConfigStateEnum::fromApiValue('enabled'))->toBe(ConfigStateEnum::ENABLED)
            ->and(ConfigStateEnum::fromApiValue('ENABLED'))->toBe(ConfigStateEnum::ENABLED)
            ->and(ConfigStateEnum::fromApiValue('true'))->toBe(ConfigStateEnum::ENABLED)
            ->and(ConfigStateEnum::fromApiValue('TRUE'))->toBe(ConfigStateEnum::ENABLED)
            ->and(ConfigStateEnum::fromApiValue('1'))->toBe(ConfigStateEnum::ENABLED)
            ->and(ConfigStateEnum::fromApiValue(1))->toBe(ConfigStateEnum::ENABLED);

        // Test 'disabled' variations
        expect(ConfigStateEnum::fromApiValue('disabled'))->toBe(ConfigStateEnum::DISABLED)
            ->and(ConfigStateEnum::fromApiValue('DISABLED'))->toBe(ConfigStateEnum::DISABLED)
            ->and(ConfigStateEnum::fromApiValue('false'))->toBe(ConfigStateEnum::DISABLED)
            ->and(ConfigStateEnum::fromApiValue('FALSE'))->toBe(ConfigStateEnum::DISABLED)
            ->and(ConfigStateEnum::fromApiValue('0'))->toBe(ConfigStateEnum::DISABLED)
            ->and(ConfigStateEnum::fromApiValue(0))->toBe(ConfigStateEnum::DISABLED);
    });

    it('throws exception for invalid values', function () {
        ConfigStateEnum::fromApiValue('invalid');
    })->throws(ValueError::class);

    it('checks if enabled', function () {
        expect(ConfigStateEnum::ENABLED->isEnabled())->toBeTrue()
            ->and(ConfigStateEnum::DISABLED->isEnabled())->toBeFalse();
    });

    it('checks if disabled', function () {
        expect(ConfigStateEnum::DISABLED->isDisabled())->toBeTrue()
            ->and(ConfigStateEnum::ENABLED->isDisabled())->toBeFalse();
    });

    it('converts to API value', function () {
        expect(ConfigStateEnum::ENABLED->toApiValue())->toBe('enabled')
            ->and(ConfigStateEnum::DISABLED->toApiValue())->toBe('disabled');
    });
});
