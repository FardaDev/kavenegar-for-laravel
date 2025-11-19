<?php

declare(strict_types=1);

use FardaDev\Kavenegar\Enums\ApiErrorCodeEnum;

describe('ApiErrorCodeEnum', function () {
    it('has all error codes defined', function () {
        expect(ApiErrorCodeEnum::SUCCESS->value)->toBe(200);
        expect(ApiErrorCodeEnum::INCOMPLETE_PARAMS->value)->toBe(400);
        expect(ApiErrorCodeEnum::INVALID_API_KEY->value)->toBe(401);
        expect(ApiErrorCodeEnum::OPERATION_FAILED->value)->toBe(402);
        expect(ApiErrorCodeEnum::METHOD_NOT_FOUND->value)->toBe(404);
        expect(ApiErrorCodeEnum::WRONG_HTTP_METHOD->value)->toBe(405);
        expect(ApiErrorCodeEnum::ACCESS_DENIED->value)->toBe(407);
        expect(ApiErrorCodeEnum::SERVER_UNAVAILABLE->value)->toBe(409);
        expect(ApiErrorCodeEnum::INVALID_RECEPTOR->value)->toBe(411);
        expect(ApiErrorCodeEnum::INVALID_SENDER->value)->toBe(412);
        expect(ApiErrorCodeEnum::INVALID_MESSAGE->value)->toBe(413);
        expect(ApiErrorCodeEnum::TOO_MANY_RECORDS->value)->toBe(414);
        expect(ApiErrorCodeEnum::INVALID_DATE->value)->toBe(417);
        expect(ApiErrorCodeEnum::INSUFFICIENT_CREDIT->value)->toBe(418);
        expect(ApiErrorCodeEnum::ARRAY_LENGTH_MISMATCH->value)->toBe(419);
        expect(ApiErrorCodeEnum::INVALID_TAG->value)->toBe(607);
    });

    it('returns correct Persian message for each error', function () {
        expect(ApiErrorCodeEnum::SUCCESS->message())->toBe('تایید شد');
        expect(ApiErrorCodeEnum::INCOMPLETE_PARAMS->message())->toBe('پارامترها ناقص هستند');
        expect(ApiErrorCodeEnum::INVALID_API_KEY->message())->toBe('کلید API نامعتبر است');
        expect(ApiErrorCodeEnum::OPERATION_FAILED->message())->toBe('عملیات ناموفق بود');
        expect(ApiErrorCodeEnum::METHOD_NOT_FOUND->message())->toBe('متد پیدا نشد');
        expect(ApiErrorCodeEnum::WRONG_HTTP_METHOD->message())->toBe('متد فراخوانی GET/POST اشتباه است');
        expect(ApiErrorCodeEnum::ACCESS_DENIED->message())->toBe('دسترسی غیرمجاز (محدودیت IP)');
        expect(ApiErrorCodeEnum::SERVER_UNAVAILABLE->message())->toBe('سرور در دسترس نیست');
        expect(ApiErrorCodeEnum::INVALID_RECEPTOR->message())->toBe('شماره گیرنده نامعتبر است');
        expect(ApiErrorCodeEnum::INVALID_SENDER->message())->toBe('شماره فرستنده نامعتبر است');
        expect(ApiErrorCodeEnum::INVALID_MESSAGE->message())->toBe('پیام خالی یا بیش از حد طولانی است');
        expect(ApiErrorCodeEnum::TOO_MANY_RECORDS->message())->toBe('تعداد رکوردها بیش از حد مجاز است');
        expect(ApiErrorCodeEnum::INVALID_DATE->message())->toBe('تاریخ نامعتبر است');
        expect(ApiErrorCodeEnum::INSUFFICIENT_CREDIT->message())->toBe('اعتبار کافی نیست');
        expect(ApiErrorCodeEnum::ARRAY_LENGTH_MISMATCH->message())->toBe('طول آرایه‌ها برابر نیست');
        expect(ApiErrorCodeEnum::INVALID_TAG->message())->toBe('فرمت تگ نامعتبر است');
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
