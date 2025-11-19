<?php

declare(strict_types=1);

namespace FardaDev\Kavenegar\Enums;

/**
 * Message status codes from Kavenegar API (جدول ۲ - وضعیت پیامک‌ها).
 *
 * Values and messages are from official Kavenegar documentation and must match exactly
 * for proper API integration. Messages are hardcoded in Persian as this is the official
 * API response language from Kavenegar service.
 *
 * @see https://kavenegar.com/rest.html#result-send
 */
enum MessageStatusEnum: int
{
    case IN_QUEUE = 1;
    case SCHEDULED = 2;
    case SENT_TO_OPERATOR_1 = 4;
    case SENT_TO_OPERATOR_2 = 5;
    case FAILED = 6;
    case DELIVERED = 10;
    case UNDELIVERED = 11;
    case CANCELLED = 13;
    case BLOCKED = 14;
    case INVALID = 100;

    public function message(): string
    {
        return match ($this) {
            self::IN_QUEUE => 'در صف ارسال',
            self::SCHEDULED => 'زمان بندی شده',
            self::SENT_TO_OPERATOR_1, self::SENT_TO_OPERATOR_2 => 'ارسال شده به مخابرات',
            self::FAILED => 'خطا در ارسال پیام',
            self::DELIVERED => 'رسیده به گیرنده',
            self::UNDELIVERED => 'نرسیده به گیرنده',
            self::CANCELLED => 'لغو شده',
            self::BLOCKED => 'بلاک شده',
            self::INVALID => 'شناسه نامعتبر',
        };
    }

    public function isDelivered(): bool
    {
        return $this === self::DELIVERED;
    }

    public function isFailed(): bool
    {
        return in_array($this, [
            self::FAILED,
            self::UNDELIVERED,
            self::CANCELLED,
            self::BLOCKED,
        ], true);
    }

    public function isPending(): bool
    {
        return in_array($this, [
            self::IN_QUEUE,
            self::SCHEDULED,
            self::SENT_TO_OPERATOR_1,
            self::SENT_TO_OPERATOR_2,
        ], true);
    }
}
