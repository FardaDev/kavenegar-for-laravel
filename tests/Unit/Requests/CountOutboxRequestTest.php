<?php declare(strict_types=1);

use FardaDev\Kavenegar\Enums\MessageStatusEnum;
use FardaDev\Kavenegar\Exceptions\InputValidationException;
use FardaDev\Kavenegar\Requests\CountOutboxRequest;

it('creates request with required parameters', function () {
    $request = new CountOutboxRequest(
        startdate: 1735689600
    );

    expect($request->startdate)->toBe(1735689600)
        ->and($request->enddate)->toBeNull()
        ->and($request->status)->toBeNull();
});

it('creates request with all parameters', function () {
    $request = new CountOutboxRequest(
        startdate: 1735689600,
        enddate: 1735776000,
        status: MessageStatusEnum::DELIVERED
    );

    expect($request->startdate)->toBe(1735689600)
        ->and($request->enddate)->toBe(1735776000)
        ->and($request->status)->toBe(MessageStatusEnum::DELIVERED);
});

it('converts to API parameters', function () {
    $request = new CountOutboxRequest(
        startdate: 1735689600,
        enddate: 1735776000,
        status: MessageStatusEnum::DELIVERED
    );

    $params = $request->toApiParams();

    expect($params)->toBe([
        'startdate' => 1735689600,
        'enddate' => 1735776000,
        'status' => 10, // MessageStatusEnum::DELIVERED->value
    ]);
});

it('omits null optional parameters from API params', function () {
    $request = new CountOutboxRequest(
        startdate: 1735689600
    );

    $params = $request->toApiParams();

    expect($params)->toBe([
        'startdate' => 1735689600,
    ])
        ->and($params)->not->toHaveKey('enddate')
        ->and($params)->not->toHaveKey('status');
});

it('throws exception for enddate before startdate', function () {
    new CountOutboxRequest(
        startdate: 1735776000,
        enddate: 1735689600
    );
})->throws(InputValidationException::class, 'تاریخ پایان نمی‌تواند قبل از تاریخ شروع باشد');

it('throws exception for date range exceeding 1 day', function () {
    new CountOutboxRequest(
        startdate: 1735689600,
        enddate: 1735862400 // 2 days later
    );
})->throws(InputValidationException::class, 'بازه زمانی نمی‌تواند بیشتر از یک روز باشد');

it('accepts date range of exactly 1 day', function () {
    $request = new CountOutboxRequest(
        startdate: 1735689600,
        enddate: 1735776000 // exactly 1 day (86400 seconds)
    );

    expect($request->startdate)->toBe(1735689600)
        ->and($request->enddate)->toBe(1735776000);
});

it('accepts different message status enums', function () {
    $statuses = [
        MessageStatusEnum::IN_QUEUE,
        MessageStatusEnum::SCHEDULED,
        MessageStatusEnum::DELIVERED,
        MessageStatusEnum::UNDELIVERED,
        MessageStatusEnum::CANCELLED,
    ];

    foreach ($statuses as $status) {
        $request = new CountOutboxRequest(
            startdate: 1735689600,
            status: $status
        );

        expect($request->status)->toBe($status)
            ->and($request->toApiParams()['status'])->toBe($status->value);
    }
});

