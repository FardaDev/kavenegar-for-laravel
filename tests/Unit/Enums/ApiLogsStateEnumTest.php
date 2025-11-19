<?php

declare(strict_types=1);

use FardaDev\Kavenegar\Enums\ApiLogsStateEnum;

describe('ApiLogsStateEnum', function () {
    it('has all three states defined', function () {
        expect(ApiLogsStateEnum::cases())->toHaveCount(3)
            ->and(ApiLogsStateEnum::ENABLED->value)->toBe('enabled')
            ->and(ApiLogsStateEnum::DISABLED->value)->toBe('disabled')
            ->and(ApiLogsStateEnum::JUST_FAULTS->value)->toBe('justfaults');
    });

    it('creates from API value', function () {
        expect(ApiLogsStateEnum::fromApiValue('enabled'))->toBe(ApiLogsStateEnum::ENABLED)
            ->and(ApiLogsStateEnum::fromApiValue('ENABLED'))->toBe(ApiLogsStateEnum::ENABLED)
            ->and(ApiLogsStateEnum::fromApiValue('disabled'))->toBe(ApiLogsStateEnum::DISABLED)
            ->and(ApiLogsStateEnum::fromApiValue('DISABLED'))->toBe(ApiLogsStateEnum::DISABLED)
            ->and(ApiLogsStateEnum::fromApiValue('justfaults'))->toBe(ApiLogsStateEnum::JUST_FAULTS)
            ->and(ApiLogsStateEnum::fromApiValue('JUSTFAULTS'))->toBe(ApiLogsStateEnum::JUST_FAULTS);
    });

    it('throws exception for invalid values', function () {
        ApiLogsStateEnum::fromApiValue('invalid');
    })->throws(ValueError::class);

    it('checks if enabled', function () {
        expect(ApiLogsStateEnum::ENABLED->isEnabled())->toBeTrue()
            ->and(ApiLogsStateEnum::DISABLED->isEnabled())->toBeFalse()
            ->and(ApiLogsStateEnum::JUST_FAULTS->isEnabled())->toBeFalse();
    });

    it('checks if disabled', function () {
        expect(ApiLogsStateEnum::DISABLED->isDisabled())->toBeTrue()
            ->and(ApiLogsStateEnum::ENABLED->isDisabled())->toBeFalse()
            ->and(ApiLogsStateEnum::JUST_FAULTS->isDisabled())->toBeFalse();
    });

    it('checks if just faults', function () {
        expect(ApiLogsStateEnum::JUST_FAULTS->isJustFaults())->toBeTrue()
            ->and(ApiLogsStateEnum::ENABLED->isJustFaults())->toBeFalse()
            ->and(ApiLogsStateEnum::DISABLED->isJustFaults())->toBeFalse();
    });

    it('returns correct Persian description', function () {
        expect(ApiLogsStateEnum::ENABLED->description())->toBe('لاگ کلیه درخواست‌ها ذخیره می‌شود')
            ->and(ApiLogsStateEnum::DISABLED->description())->toBe('لاگ هیچ درخواستی ذخیره نمی‌شود')
            ->and(ApiLogsStateEnum::JUST_FAULTS->description())->toBe('فقط لاگ خطاها ذخیره می‌شود');
    });
});
