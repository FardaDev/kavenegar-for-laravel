<?php

declare(strict_types=1);

namespace FardaDev\Kavenegar\Enums;

/**
 * API error codes from Kavenegar (جدول ۱ - کدهای برگشتی).
 * 
 * Values and messages are from official Kavenegar documentation and must match exactly
 * for proper error handling. Messages are hardcoded in Persian as this is the official
 * API response language from Kavenegar service.
 * 
 * @see https://kavenegar.com/rest.html#result-general
 */
enum ApiErrorCodeEnum: int
{
    case SUCCESS = 200;
    case INCOMPLETE_PARAMS = 400;
    case INVALID_API_KEY = 401;
    case OPERATION_FAILED = 402;
    case METHOD_NOT_FOUND = 404;
    case WRONG_HTTP_METHOD = 405;
    case ACCESS_DENIED = 407;
    case SERVER_UNAVAILABLE = 409;
    case INVALID_RECEPTOR = 411;
    case INVALID_SENDER = 412;
    case INVALID_MESSAGE = 413;
    case TOO_MANY_RECORDS = 414;
    case INVALID_DATE = 417;
    case INSUFFICIENT_CREDIT = 418;
    case ARRAY_LENGTH_MISMATCH = 419;
    case INVALID_TAG = 607;

    public function message(): string
    {
        return match ($this) {
            self::SUCCESS => 'تایید شد',
            self::INCOMPLETE_PARAMS => 'پارامترها ناقص هستند',
            self::INVALID_API_KEY => 'کلید API نامعتبر است',
            self::OPERATION_FAILED => 'عملیات ناموفق بود',
            self::METHOD_NOT_FOUND => 'متد پیدا نشد',
            self::WRONG_HTTP_METHOD => 'متد فراخوانی GET/POST اشتباه است',
            self::ACCESS_DENIED => 'دسترسی غیرمجاز (محدودیت IP)',
            self::SERVER_UNAVAILABLE => 'سرور در دسترس نیست',
            self::INVALID_RECEPTOR => 'شماره گیرنده نامعتبر است',
            self::INVALID_SENDER => 'شماره فرستنده نامعتبر است',
            self::INVALID_MESSAGE => 'پیام خالی یا بیش از حد طولانی است',
            self::TOO_MANY_RECORDS => 'تعداد رکوردها بیش از حد مجاز است',
            self::INVALID_DATE => 'تاریخ نامعتبر است',
            self::INSUFFICIENT_CREDIT => 'اعتبار کافی نیست',
            self::ARRAY_LENGTH_MISMATCH => 'طول آرایه‌ها برابر نیست',
            self::INVALID_TAG => 'فرمت تگ نامعتبر است',
        };
    }

    public function isClientError(): bool
    {
        return $this->value >= 400 && $this->value < 500;
    }

    public function isServerError(): bool
    {
        return $this->value >= 500;
    }

    public function isSuccess(): bool
    {
        return $this === self::SUCCESS;
    }
}
