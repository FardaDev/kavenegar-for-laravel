<?php declare(strict_types=1);

use FardaDev\Kavenegar\Dto\AccountConfig;
use FardaDev\Kavenegar\Dto\AccountInfo;
use FardaDev\Kavenegar\Dto\MessageResponse;
use FardaDev\Kavenegar\Dto\StatusResponse;

describe('MessageResponse DTO', function () {
    it('creates from array', function () {
        $data = [
            'messageid' => 123456,
            'message' => 'Test message',
            'status' => 10,
            'statustext' => 'رسیده به گیرنده',
            'sender' => '10004346',
            'receptor' => '09123456789',
            'date' => 1356619709,
            'cost' => 120,
        ];

        $response = MessageResponse::fromArray($data);

        expect($response->messageid)->toBe(123456)
            ->and($response->message)->toBe('Test message')
            ->and($response->status)->toBe(10)
            ->and($response->statustext)->toBe('رسیده به گیرنده')
            ->and($response->sender)->toBe('10004346')
            ->and($response->receptor)->toBe('09123456789')
            ->and($response->date)->toBe(1356619709)
            ->and($response->cost)->toBe(120);
    });

    it('identifies delivered messages', function () {
        $response = MessageResponse::fromArray([
            'messageid' => 1,
            'message' => 'test',
            'status' => 10,
            'statustext' => 'delivered',
            'sender' => '10004346',
            'receptor' => '09123456789',
            'date' => time(),
            'cost' => 120,
        ]);

        expect($response->isDelivered())->toBeTrue()
            ->and($response->isFailed())->toBeFalse()
            ->and($response->isPending())->toBeFalse();
    });

    it('identifies failed messages', function () {
        $failedStatuses = [6, 11, 13, 14];

        foreach ($failedStatuses as $status) {
            $response = MessageResponse::fromArray([
                'messageid' => 1,
                'message' => 'test',
                'status' => $status,
                'statustext' => 'failed',
                'sender' => '10004346',
                'receptor' => '09123456789',
                'date' => time(),
                'cost' => 120,
            ]);

            expect($response->isFailed())->toBeTrue()
                ->and($response->isDelivered())->toBeFalse()
                ->and($response->isPending())->toBeFalse();
        }
    });

    it('identifies pending messages', function () {
        $pendingStatuses = [1, 2, 4, 5];

        foreach ($pendingStatuses as $status) {
            $response = MessageResponse::fromArray([
                'messageid' => 1,
                'message' => 'test',
                'status' => $status,
                'statustext' => 'pending',
                'sender' => '10004346',
                'receptor' => '09123456789',
                'date' => time(),
                'cost' => 120,
            ]);

            expect($response->isPending())->toBeTrue()
                ->and($response->isDelivered())->toBeFalse()
                ->and($response->isFailed())->toBeFalse();
        }
    });

    it('is readonly', function () {
        $response = MessageResponse::fromArray([
            'messageid' => 1,
            'message' => 'test',
            'status' => 1,
            'statustext' => 'pending',
            'sender' => '10004346',
            'receptor' => '09123456789',
            'date' => time(),
            'cost' => 120,
        ]);

        expect(fn () => $response->messageid = 999)
            ->toThrow(Error::class);
    });
});

describe('StatusResponse DTO', function () {
    it('creates from array', function () {
        $data = [
            'messageid' => 123456,
            'status' => 10,
            'statustext' => 'رسیده به گیرنده',
        ];

        $response = StatusResponse::fromArray($data);

        expect($response->messageid)->toBe(123456)
            ->and($response->status)->toBe(10)
            ->and($response->statustext)->toBe('رسیده به گیرنده');
    });

    it('has status helper methods', function () {
        $delivered = StatusResponse::fromArray([
            'messageid' => 1,
            'status' => 10,
            'statustext' => 'delivered',
        ]);

        expect($delivered->isDelivered())->toBeTrue();

        $failed = StatusResponse::fromArray([
            'messageid' => 2,
            'status' => 11,
            'statustext' => 'failed',
        ]);

        expect($failed->isFailed())->toBeTrue();

        $pending = StatusResponse::fromArray([
            'messageid' => 3,
            'status' => 1,
            'statustext' => 'pending',
        ]);

        expect($pending->isPending())->toBeTrue();
    });
});

describe('AccountInfo DTO', function () {
    it('creates from array', function () {
        $data = [
            'remaincredit' => 50000,
            'expiredate' => time() + 86400,
            'type' => 'premium',
        ];

        $info = AccountInfo::fromArray($data);

        expect($info->remaincredit)->toBe(50000)
            ->and($info->expiredate)->toBeInt()
            ->and($info->type)->toBe('premium');
    });

    it('checks if has credit', function () {
        $withCredit = AccountInfo::fromArray([
            'remaincredit' => 1000,
            'expiredate' => time(),
            'type' => 'basic',
        ]);

        expect($withCredit->hasCredit())->toBeTrue();

        $noCredit = AccountInfo::fromArray([
            'remaincredit' => 0,
            'expiredate' => time(),
            'type' => 'basic',
        ]);

        expect($noCredit->hasCredit())->toBeFalse();
    });

    it('checks if expired', function () {
        $expired = AccountInfo::fromArray([
            'remaincredit' => 1000,
            'expiredate' => time() - 86400,
            'type' => 'basic',
        ]);

        expect($expired->isExpired())->toBeTrue();

        $active = AccountInfo::fromArray([
            'remaincredit' => 1000,
            'expiredate' => time() + 86400,
            'type' => 'basic',
        ]);

        expect($active->isExpired())->toBeFalse();
    });

    it('gets credit amount', function () {
        $info = AccountInfo::fromArray([
            'remaincredit' => 12345,
            'expiredate' => time(),
            'type' => 'basic',
        ]);

        expect($info->getCreditAmount())->toBe(12345);
    });

    it('gets expiry date as DateTime', function () {
        $timestamp = time() + 86400;
        $info = AccountInfo::fromArray([
            'remaincredit' => 1000,
            'expiredate' => $timestamp,
            'type' => 'basic',
        ]);

        $date = $info->getExpiryDate();

        expect($date)->toBeInstanceOf(DateTime::class)
            ->and($date->getTimestamp())->toBe($timestamp);
    });
});

describe('AccountConfig DTO', function () {
    it('creates from array', function () {
        $data = [
            'apilogs' => 'enabled',
            'dailyreport' => 'disabled',
            'debugmode' => '1',
            'defaultsender' => '10004346',
            'mincreditalarm' => 1000,
            'resendfailed' => 'enabled',
        ];

        $config = AccountConfig::fromArray($data);

        expect($config->apilogs)->toBe('enabled')
            ->and($config->dailyreport)->toBe('disabled')
            ->and($config->debugmode)->toBe('1')
            ->and($config->defaultsender)->toBe('10004346')
            ->and($config->mincreditalarm)->toBe(1000)
            ->and($config->resendfailed)->toBe('enabled');
    });

    it('checks if api logs enabled', function () {
        $enabled = AccountConfig::fromArray([
            'apilogs' => 'enabled',
            'dailyreport' => 'disabled',
            'debugmode' => '0',
            'defaultsender' => '10004346',
            'mincreditalarm' => 1000,
            'resendfailed' => 'disabled',
        ]);

        expect($enabled->hasApiLogsEnabled())->toBeTrue();

        $enabledNumeric = AccountConfig::fromArray([
            'apilogs' => '1',
            'dailyreport' => 'disabled',
            'debugmode' => '0',
            'defaultsender' => '10004346',
            'mincreditalarm' => 1000,
            'resendfailed' => 'disabled',
        ]);

        expect($enabledNumeric->hasApiLogsEnabled())->toBeTrue();
    });

    it('checks all boolean helpers', function () {
        $config = AccountConfig::fromArray([
            'apilogs' => 'enabled',
            'dailyreport' => '1',
            'debugmode' => 'enabled',
            'defaultsender' => '10004346',
            'mincreditalarm' => 1000,
            'resendfailed' => '1',
        ]);

        expect($config->hasApiLogsEnabled())->toBeTrue()
            ->and($config->hasDailyReportEnabled())->toBeTrue()
            ->and($config->hasDebugModeEnabled())->toBeTrue()
            ->and($config->hasResendFailedEnabled())->toBeTrue();
    });
});
