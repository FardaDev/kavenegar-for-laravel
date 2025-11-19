<?php

declare(strict_types=1);

use FardaDev\Kavenegar\Enums\MessageTypeEnum;

describe('MessageTypeEnum', function () {
    it('has all message types defined', function () {
        expect(MessageTypeEnum::FLASH->value)->toBe(0);
        expect(MessageTypeEnum::NORMAL->value)->toBe(1);
        expect(MessageTypeEnum::SIM_CARD->value)->toBe(2);
        expect(MessageTypeEnum::EXTERNAL_APP->value)->toBe(3);
    });

    it('returns correct Persian description for each type', function () {
        expect(MessageTypeEnum::FLASH->description())->toBe('پیامک فلش (نمایش مستقیم بدون ذخیره)');
        expect(MessageTypeEnum::NORMAL->description())->toBe('پیامک عادی (ذخیره در حافظه موبایل)');
        expect(MessageTypeEnum::SIM_CARD->description())->toBe('ذخیره در سیم‌کارت');
        expect(MessageTypeEnum::EXTERNAL_APP->description())->toBe('ذخیره در برنامه خارجی');
    });

    it('can be created from integer value', function () {
        $type = MessageTypeEnum::from(1);
        expect($type)->toBe(MessageTypeEnum::NORMAL);
        expect($type->value)->toBe(1);
    });

    it('throws exception for invalid type code', function () {
        MessageTypeEnum::from(999);
    })->throws(ValueError::class);
});
