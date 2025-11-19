<?php

declare(strict_types=1);

namespace FardaDev\Kavenegar\Enums;

/**
 * Message display types on recipient device (جدول ۳ - نوع نمایش پیام).
 *
 * Values and descriptions are from official Kavenegar documentation. These control
 * how the SMS is displayed and stored on the recipient's device.
 *
 * Note: Type parameter is only available for 3000-series lines.
 *
 * @see https://kavenegar.com/rest.html#result-msgmode
 */
enum MessageTypeEnum: int
{
    case FLASH = 0;
    case NORMAL = 1;
    case SIM_CARD = 2;
    case EXTERNAL_APP = 3;

    public function description(): string
    {
        return match ($this) {
            self::FLASH => 'پیامک فلش (نمایش مستقیم بدون ذخیره)',
            self::NORMAL => 'پیامک عادی (ذخیره در حافظه موبایل)',
            self::SIM_CARD => 'ذخیره در سیم‌کارت',
            self::EXTERNAL_APP => 'ذخیره در برنامه خارجی',
        };
    }
}
