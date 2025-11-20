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
    case ACCOUNT_INVALID = 403;
    case METHOD_NOT_FOUND = 404;
    case WRONG_HTTP_METHOD = 405;
    case MANDATORY_PARAMS_EMPTY = 406;
    case ACCESS_DENIED = 407;
    case SERVER_UNAVAILABLE = 409;
    case INVALID_RECEPTOR = 411;
    case INVALID_SENDER = 412;
    case INVALID_MESSAGE = 413;
    case TOO_MANY_RECORDS = 414;
    case START_INDEX_TOO_LARGE = 415;
    case IP_MISMATCH = 416;
    case INVALID_DATE = 417;
    case INSUFFICIENT_CREDIT = 418;
    case ARRAY_LENGTH_MISMATCH = 419;
    case LINK_RESTRICTED = 420;
    case INVALID_CHARACTER = 422;
    case TEMPLATE_NOT_FOUND = 424;
    case ADVANCED_SERVICE_REQUIRED = 426;
    case LINE_ACCESS_LEVEL_REQUIRED = 427;
    case VOICE_CALL_NOT_POSSIBLE = 428;
    case IP_RESTRICTED = 429;
    case INVALID_CODE_STRUCTURE = 431;
    case CODE_PARAM_NOT_FOUND = 432;
    case RATE_LIMIT_EXCEEDED = 451;
    case TEST_MODE_ONLY = 501;
    case INVALID_TAG = 607;

    public function message(): string
    {
        return match ($this) {
            self::SUCCESS => 'تایید شد',
            self::INCOMPLETE_PARAMS => 'پارامترها ناقص هستند',
            self::INVALID_API_KEY => 'حساب کاربری غیرفعال شده است',
            self::OPERATION_FAILED => 'عملیات ناموفق بود',
            self::ACCOUNT_INVALID => 'کد شناسائی API-Key معتبر نمی‌باشد',
            self::METHOD_NOT_FOUND => 'متد نامشخص است',
            self::WRONG_HTTP_METHOD => 'متد Get/Post اشتباه است',
            self::MANDATORY_PARAMS_EMPTY => 'پارامترهای اجباری خالی ارسال شده اند',
            self::ACCESS_DENIED => 'دسترسی به اطلاعات مورد نظر برای شما امکان پذیر نیست',
            self::SERVER_UNAVAILABLE => 'سرور قادر به پاسخگوئی نیست بعدا تلاش کنید',
            self::INVALID_RECEPTOR => 'دریافت کننده نامعتبر است',
            self::INVALID_SENDER => 'ارسال کننده نامعتبر است',
            self::INVALID_MESSAGE => 'پیام خالی است و یا طول پیام بیش از حد مجاز می‌باشد',
            self::TOO_MANY_RECORDS => 'حجم درخواست بیشتر از حد مجاز است',
            self::START_INDEX_TOO_LARGE => 'اندیس شروع بزرگ تر از کل تعداد شماره های مورد نظر است',
            self::IP_MISMATCH => 'IP سرویس مبدا با تنظیمات مطابقت ندارد',
            self::INVALID_DATE => 'تاریخ ارسال اشتباه است و فرمت آن صحیح نمی باشد',
            self::INSUFFICIENT_CREDIT => 'اعتبار شما کافی نمی‌باشد',
            self::ARRAY_LENGTH_MISMATCH => 'طول آرایه متن و گیرنده و فرستنده هم اندازه نیست',
            self::LINK_RESTRICTED => 'استفاده از لینک در متن پیام برای شما محدود شده است',
            self::INVALID_CHARACTER => 'داده ها به دلیل وجود کاراکتر نامناسب قابل پردازش نیست',
            self::TEMPLATE_NOT_FOUND => 'الگوی مورد نظر پیدا نشد',
            self::ADVANCED_SERVICE_REQUIRED => 'استفاده از این متد نیازمند سرویس پیشرفته می‌باشد',
            self::LINE_ACCESS_LEVEL_REQUIRED => 'استفاده از این خط نیازمند ایجاد سطح دسترسی می باشد',
            self::VOICE_CALL_NOT_POSSIBLE => 'ارسال کد از طریق تماس تلفنی امکان پذیر نیست',
            self::IP_RESTRICTED => 'IP محدود شده است',
            self::INVALID_CODE_STRUCTURE => 'ساختار کد صحیح نمی‌باشد',
            self::CODE_PARAM_NOT_FOUND => 'پارامتر کد در متن پیام پیدا نشد',
            self::RATE_LIMIT_EXCEEDED => 'فراخوانی بیش از حد در بازه زمانی مشخص IP محدود شده',
            self::TEST_MODE_ONLY => 'فقط امکان ارسال پیام تست به شماره صاحب حساب کاربری وجود دارد',
            self::INVALID_TAG => 'نام تگ انتخابی اشتباه است',
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
