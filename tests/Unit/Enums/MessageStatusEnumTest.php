<?php

declare(strict_types=1);

use FardaDev\Kavenegar\Enums\MessageStatusEnum;

describe('MessageStatusEnum', function () {
    it('has all status codes defined', function () {
        expect(MessageStatusEnum::IN_QUEUE->value)->toBe(1);
        expect(MessageStatusEnum::SCHEDULED->value)->toBe(2);
        expect(MessageStatusEnum::SENT_TO_OPERATOR_1->value)->toBe(4);
        expect(MessageStatusEnum::SENT_TO_OPERATOR_2->value)->toBe(5);
        expect(MessageStatusEnum::FAILED->value)->toBe(6);
        expect(MessageStatusEnum::DELIVERED->value)->toBe(10);
        expect(MessageStatusEnum::UNDELIVERED->value)->toBe(11);
        expect(MessageStatusEnum::CANCELLED->value)->toBe(13);
        expect(MessageStatusEnum::BLOCKED->value)->toBe(14);
        expect(MessageStatusEnum::INVALID->value)->toBe(100);
    });

    it('returns correct Persian message for each status', function () {
        expect(MessageStatusEnum::IN_QUEUE->message())->toBe('در صف ارسال');
        expect(MessageStatusEnum::SCHEDULED->message())->toBe('زمان بندی شده');
        expect(MessageStatusEnum::SENT_TO_OPERATOR_1->message())->toBe('ارسال شده به مخابرات');
        expect(MessageStatusEnum::SENT_TO_OPERATOR_2->message())->toBe('ارسال شده به مخابرات');
        expect(MessageStatusEnum::FAILED->message())->toBe('خطا در ارسال پیام');
        expect(MessageStatusEnum::DELIVERED->message())->toBe('رسیده به گیرنده');
        expect(MessageStatusEnum::UNDELIVERED->message())->toBe('نرسیده به گیرنده');
        expect(MessageStatusEnum::CANCELLED->message())->toBe('لغو شده');
        expect(MessageStatusEnum::BLOCKED->message())->toBe('بلاک شده');
        expect(MessageStatusEnum::INVALID->message())->toBe('شناسه نامعتبر');
    });

    it('correctly identifies delivered status', function () {
        expect(MessageStatusEnum::DELIVERED->isDelivered())->toBeTrue();
        expect(MessageStatusEnum::IN_QUEUE->isDelivered())->toBeFalse();
        expect(MessageStatusEnum::FAILED->isDelivered())->toBeFalse();
    });

    it('correctly identifies failed statuses', function () {
        expect(MessageStatusEnum::FAILED->isFailed())->toBeTrue();
        expect(MessageStatusEnum::UNDELIVERED->isFailed())->toBeTrue();
        expect(MessageStatusEnum::CANCELLED->isFailed())->toBeTrue();
        expect(MessageStatusEnum::BLOCKED->isFailed())->toBeTrue();

        expect(MessageStatusEnum::DELIVERED->isFailed())->toBeFalse();
        expect(MessageStatusEnum::IN_QUEUE->isFailed())->toBeFalse();
    });

    it('correctly identifies pending statuses', function () {
        expect(MessageStatusEnum::IN_QUEUE->isPending())->toBeTrue();
        expect(MessageStatusEnum::SCHEDULED->isPending())->toBeTrue();
        expect(MessageStatusEnum::SENT_TO_OPERATOR_1->isPending())->toBeTrue();
        expect(MessageStatusEnum::SENT_TO_OPERATOR_2->isPending())->toBeTrue();

        expect(MessageStatusEnum::DELIVERED->isPending())->toBeFalse();
        expect(MessageStatusEnum::FAILED->isPending())->toBeFalse();
    });

    it('can be created from integer value', function () {
        $status = MessageStatusEnum::from(10);
        expect($status)->toBe(MessageStatusEnum::DELIVERED);
        expect($status->value)->toBe(10);
    });

    it('throws exception for invalid status code', function () {
        MessageStatusEnum::from(999);
    })->throws(ValueError::class);
});
