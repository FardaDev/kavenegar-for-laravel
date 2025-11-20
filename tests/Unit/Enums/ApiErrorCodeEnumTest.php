<?php

declare(strict_types=1);

use FardaDev\Kavenegar\Enums\ApiErrorCodeEnum;

describe('ApiErrorCodeEnum', function () {
    it('has all 31 error codes from جدول ۱ defined', function () {
        expect(ApiErrorCodeEnum::SUCCESS->value)->toBe(200);
        expect(ApiErrorCodeEnum::INCOMPLETE_PARAMS->value)->toBe(400);
        expect(ApiErrorCodeEnum::INVALID_API_KEY->value)->toBe(401);
        expect(ApiErrorCodeEnum::OPERATION_FAILED->value)->toBe(402);
        expect(ApiErrorCodeEnum::ACCOUNT_INVALID->value)->toBe(403);
        expect(ApiErrorCodeEnum::METHOD_NOT_FOUND->value)->toBe(404);
        expect(ApiErrorCodeEnum::WRONG_HTTP_METHOD->value)->toBe(405);
        expect(ApiErrorCodeEnum::MANDATORY_PARAMS_EMPTY->value)->toBe(406);
        expect(ApiErrorCodeEnum::ACCESS_DENIED->value)->toBe(407);
        expect(ApiErrorCodeEnum::SERVER_UNAVAILABLE->value)->toBe(409);
        expect(ApiErrorCodeEnum::INVALID_RECEPTOR->value)->toBe(411);
        expect(ApiErrorCodeEnum::INVALID_SENDER->value)->toBe(412);
        expect(ApiErrorCodeEnum::INVALID_MESSAGE->value)->toBe(413);
        expect(ApiErrorCodeEnum::TOO_MANY_RECORDS->value)->toBe(414);
        expect(ApiErrorCodeEnum::START_INDEX_TOO_LARGE->value)->toBe(415);
        expect(ApiErrorCodeEnum::IP_MISMATCH->value)->toBe(416);
        expect(ApiErrorCodeEnum::INVALID_DATE->value)->toBe(417);
        expect(ApiErrorCodeEnum::INSUFFICIENT_CREDIT->value)->toBe(418);
        expect(ApiErrorCodeEnum::ARRAY_LENGTH_MISMATCH->value)->toBe(419);
        expect(ApiErrorCodeEnum::LINK_RESTRICTED->value)->toBe(420);
        expect(ApiErrorCodeEnum::INVALID_CHARACTER->value)->toBe(422);
        expect(ApiErrorCodeEnum::TEMPLATE_NOT_FOUND->value)->toBe(424);
        expect(ApiErrorCodeEnum::ADVANCED_SERVICE_REQUIRED->value)->toBe(426);
        expect(ApiErrorCodeEnum::LINE_ACCESS_LEVEL_REQUIRED->value)->toBe(427);
        expect(ApiErrorCodeEnum::VOICE_CALL_NOT_POSSIBLE->value)->toBe(428);
        expect(ApiErrorCodeEnum::IP_RESTRICTED->value)->toBe(429);
        expect(ApiErrorCodeEnum::INVALID_CODE_STRUCTURE->value)->toBe(431);
        expect(ApiErrorCodeEnum::CODE_PARAM_NOT_FOUND->value)->toBe(432);
        expect(ApiErrorCodeEnum::RATE_LIMIT_EXCEEDED->value)->toBe(451);
        expect(ApiErrorCodeEnum::TEST_MODE_ONLY->value)->toBe(501);
        expect(ApiErrorCodeEnum::INVALID_TAG->value)->toBe(607);
    });

    it('returns correct Persian message from جدول ۱ for each error', function () {
        expect(ApiErrorCodeEnum::SUCCESS->message())->toBe('تایید شد');
        expect(ApiErrorCodeEnum::INCOMPLETE_PARAMS->message())->toBe('پارامترها ناقص هستند');
        expect(ApiErrorCodeEnum::INVALID_API_KEY->message())->toBe('حساب کاربری غیرفعال شده است');
        expect(ApiErrorCodeEnum::OPERATION_FAILED->message())->toBe('عملیات ناموفق بود');
        expect(ApiErrorCodeEnum::ACCOUNT_INVALID->message())->toBe('کد شناسائی API-Key معتبر نمی‌باشد');
        expect(ApiErrorCodeEnum::METHOD_NOT_FOUND->message())->toBe('متد نامشخص است');
        expect(ApiErrorCodeEnum::WRONG_HTTP_METHOD->message())->toBe('متد Get/Post اشتباه است');
        expect(ApiErrorCodeEnum::MANDATORY_PARAMS_EMPTY->message())->toBe('پارامترهای اجباری خالی ارسال شده اند');
        expect(ApiErrorCodeEnum::ACCESS_DENIED->message())->toBe('دسترسی به اطلاعات مورد نظر برای شما امکان پذیر نیست');
        expect(ApiErrorCodeEnum::SERVER_UNAVAILABLE->message())->toBe('سرور قادر به پاسخگوئی نیست بعدا تلاش کنید');
        expect(ApiErrorCodeEnum::INVALID_RECEPTOR->message())->toBe('دریافت کننده نامعتبر است');
        expect(ApiErrorCodeEnum::INVALID_SENDER->message())->toBe('ارسال کننده نامعتبر است');
        expect(ApiErrorCodeEnum::INVALID_MESSAGE->message())->toBe('پیام خالی است و یا طول پیام بیش از حد مجاز می‌باشد');
        expect(ApiErrorCodeEnum::TOO_MANY_RECORDS->message())->toBe('حجم درخواست بیشتر از حد مجاز است');
        expect(ApiErrorCodeEnum::START_INDEX_TOO_LARGE->message())->toBe('اندیس شروع بزرگ تر از کل تعداد شماره های مورد نظر است');
        expect(ApiErrorCodeEnum::IP_MISMATCH->message())->toBe('IP سرویس مبدا با تنظیمات مطابقت ندارد');
        expect(ApiErrorCodeEnum::INVALID_DATE->message())->toBe('تاریخ ارسال اشتباه است و فرمت آن صحیح نمی باشد');
        expect(ApiErrorCodeEnum::INSUFFICIENT_CREDIT->message())->toBe('اعتبار شما کافی نمی‌باشد');
        expect(ApiErrorCodeEnum::ARRAY_LENGTH_MISMATCH->message())->toBe('طول آرایه متن و گیرنده و فرستنده هم اندازه نیست');
        expect(ApiErrorCodeEnum::LINK_RESTRICTED->message())->toBe('استفاده از لینک در متن پیام برای شما محدود شده است');
        expect(ApiErrorCodeEnum::INVALID_CHARACTER->message())->toBe('داده ها به دلیل وجود کاراکتر نامناسب قابل پردازش نیست');
        expect(ApiErrorCodeEnum::TEMPLATE_NOT_FOUND->message())->toBe('الگوی مورد نظر پیدا نشد');
        expect(ApiErrorCodeEnum::ADVANCED_SERVICE_REQUIRED->message())->toBe('استفاده از این متد نیازمند سرویس پیشرفته می‌باشد');
        expect(ApiErrorCodeEnum::LINE_ACCESS_LEVEL_REQUIRED->message())->toBe('استفاده از این خط نیازمند ایجاد سطح دسترسی می باشد');
        expect(ApiErrorCodeEnum::VOICE_CALL_NOT_POSSIBLE->message())->toBe('ارسال کد از طریق تماس تلفنی امکان پذیر نیست');
        expect(ApiErrorCodeEnum::IP_RESTRICTED->message())->toBe('IP محدود شده است');
        expect(ApiErrorCodeEnum::INVALID_CODE_STRUCTURE->message())->toBe('ساختار کد صحیح نمی‌باشد');
        expect(ApiErrorCodeEnum::CODE_PARAM_NOT_FOUND->message())->toBe('پارامتر کد در متن پیام پیدا نشد');
        expect(ApiErrorCodeEnum::RATE_LIMIT_EXCEEDED->message())->toBe('فراخوانی بیش از حد در بازه زمانی مشخص IP محدود شده');
        expect(ApiErrorCodeEnum::TEST_MODE_ONLY->message())->toBe('فقط امکان ارسال پیام تست به شماره صاحب حساب کاربری وجود دارد');
        expect(ApiErrorCodeEnum::INVALID_TAG->message())->toBe('نام تگ انتخابی اشتباه است');
    });

    it('correctly identifies client errors (4xx)', function () {
        expect(ApiErrorCodeEnum::INCOMPLETE_PARAMS->isClientError())->toBeTrue();
        expect(ApiErrorCodeEnum::INVALID_API_KEY->isClientError())->toBeTrue();
        expect(ApiErrorCodeEnum::INVALID_RECEPTOR->isClientError())->toBeTrue();
        expect(ApiErrorCodeEnum::INVALID_TAG->isClientError())->toBeFalse(); // 607 is not 4xx
        expect(ApiErrorCodeEnum::SUCCESS->isClientError())->toBeFalse();
    });

    it('correctly identifies server errors (5xx)', function () {
        expect(ApiErrorCodeEnum::SUCCESS->isServerError())->toBeFalse();
        expect(ApiErrorCodeEnum::INCOMPLETE_PARAMS->isServerError())->toBeFalse();
    });

    it('correctly identifies success', function () {
        expect(ApiErrorCodeEnum::SUCCESS->isSuccess())->toBeTrue();
        expect(ApiErrorCodeEnum::INCOMPLETE_PARAMS->isSuccess())->toBeFalse();
        expect(ApiErrorCodeEnum::INVALID_API_KEY->isSuccess())->toBeFalse();
    });

    it('can be created from integer value', function () {
        $error = ApiErrorCodeEnum::from(411);
        expect($error)->toBe(ApiErrorCodeEnum::INVALID_RECEPTOR);
        expect($error->value)->toBe(411);
    });

    it('throws exception for invalid error code', function () {
        ApiErrorCodeEnum::from(999);
    })->throws(ValueError::class);
});
