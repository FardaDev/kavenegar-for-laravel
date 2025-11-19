<?php

declare(strict_types=1);

use FardaDev\Kavenegar\Client\KavenegarClient;
use FardaDev\Kavenegar\Dto\AccountConfig;
use FardaDev\Kavenegar\Dto\AccountInfo;
use FardaDev\Kavenegar\Dto\MessageResponse;
use FardaDev\Kavenegar\Dto\StatusResponse;
use FardaDev\Kavenegar\Exceptions\KavenegarApiException;
use FardaDev\Kavenegar\Exceptions\KavenegarHttpException;
use FardaDev\Kavenegar\Exceptions\KavenegarValidationException;
use FardaDev\Kavenegar\Facades\Kavenegar;
use FardaDev\Kavenegar\Requests\SendArrayRequest;
use FardaDev\Kavenegar\Requests\SendMessageRequest;
use FardaDev\Kavenegar\Requests\StatusRequest;
use FardaDev\Kavenegar\Requests\VerifyLookupRequest;
use Illuminate\Support\Facades\Http;

describe('Send SMS', function () {
    it('sends SMS to single receptor', function () {
        Http::fake([
            '*' => Http::response([
                'return' => ['status' => 200, 'message' => 'تایید شد'],
                'entries' => [[
                    'messageid' => 123456,
                    'message' => 'Test message',
                    'status' => 1,
                    'statustext' => 'در صف ارسال',
                    'sender' => '10004346',
                    'receptor' => '09123456789',
                    'date' => 1356619709,
                    'cost' => 120,
                ]],
            ]),
        ]);

        $request = new SendMessageRequest(
            receptor: '09123456789',
            message: 'Test message'
        );
        
        $result = Kavenegar::send($request);

        expect($result)->toBeArray()
            ->toHaveCount(1)
            ->and($result[0])->toBeInstanceOf(MessageResponse::class)
            ->and($result[0]->messageid)->toBe(123456)
            ->and($result[0]->message)->toBe('Test message')
            ->and($result[0]->receptor)->toBe('09123456789');
    });

    it('sends SMS to multiple receptors', function () {
        Http::fake([
            '*' => Http::response([
                'return' => ['status' => 200, 'message' => 'تایید شد'],
                'entries' => [
                    [
                        'messageid' => 123456,
                        'message' => 'Test',
                        'status' => 1,
                        'statustext' => 'در صف ارسال',
                        'sender' => '10004346',
                        'receptor' => '09123456789',
                        'date' => time(),
                        'cost' => 120,
                    ],
                    [
                        'messageid' => 123457,
                        'message' => 'Test',
                        'status' => 1,
                        'statustext' => 'در صف ارسال',
                        'sender' => '10004346',
                        'receptor' => '09987654321',
                        'date' => time(),
                        'cost' => 120,
                    ],
                ],
            ]),
        ]);

        $request = new SendMessageRequest(
            receptor: ['09123456789', '09987654321'],
            message: 'Test'
        );
        
        $result = Kavenegar::send($request);

        expect($result)->toHaveCount(2);
    });

    it('sends SMS with all optional parameters', function () {
        Http::fake([
            '*' => Http::response([
                'return' => ['status' => 200, 'message' => 'تایید شد'],
                'entries' => [[
                    'messageid' => 123456,
                    'message' => 'Test',
                    'status' => 2,
                    'statustext' => 'زمان بندی شده',
                    'sender' => '10004346',
                    'receptor' => '09123456789',
                    'date' => time() + 3600,
                    'cost' => 120,
                ]],
            ]),
        ]);

        $request = new SendMessageRequest(
            receptor: '09123456789',
            message: 'Test',
            sender: '10004346',
            date: time() + 3600,
            type: \FardaDev\Kavenegar\Enums\MessageTypeEnum::NORMAL,
            localid: [123],
            hide: 1,
            tag: 'test-tag',
            policy: 'high-priority'
        );
        
        $result = Kavenegar::send($request);

        expect($result[0]->status->value)->toBe(2);

        Http::assertSent(function ($request) {
            $url = $request->url();

            return str_contains($url, 'sender=10004346')
                && str_contains($url, 'type=1')
                && str_contains($url, 'hide=1')
                && str_contains($url, 'tag=test-tag');
        });
    });

    it('throws exception for invalid receptor', function () {
        Http::fake([
            '*' => Http::response([
                'return' => ['status' => 411, 'message' => 'شماره گیرنده معتبر نمی باشد'],
                'entries' => [],
            ]),
        ]);

        expect(function () {
            $request = new SendMessageRequest(
                receptor: 'invalid',
                message: 'Test'
            );
            Kavenegar::send($request);
        })->toThrow(KavenegarValidationException::class);
    });

    it('throws exception for insufficient credit', function () {
        Http::fake([
            '*' => Http::response([
                'return' => ['status' => 418, 'message' => 'اعتبار کافی نیست'],
                'entries' => [],
            ]),
        ]);

        $request = new SendMessageRequest(
            receptor: '09123456789',
            message: 'Test'
        );
        
        expect(fn () => Kavenegar::send($request))
            ->toThrow(KavenegarApiException::class)
            ->and(fn () => Kavenegar::send($request))
            ->toThrow(fn (KavenegarApiException $e) => $e->errorCode === 418);
    });
});

describe('Send Array', function () {
    it('sends bulk SMS with different messages', function () {
        Http::fake([
            '*' => Http::response([
                'return' => ['status' => 200, 'message' => 'تایید شد'],
                'entries' => [
                    [
                        'messageid' => 123,
                        'message' => 'Message 1',
                        'status' => 1,
                        'statustext' => 'در صف ارسال',
                        'sender' => '10004346',
                        'receptor' => '09123456789',
                        'date' => time(),
                        'cost' => 120,
                    ],
                    [
                        'messageid' => 124,
                        'message' => 'Message 2',
                        'status' => 1,
                        'statustext' => 'در صف ارسال',
                        'sender' => '10004347',
                        'receptor' => '09987654321',
                        'date' => time(),
                        'cost' => 120,
                    ],
                ],
            ]),
        ]);

        $request = new SendArrayRequest(
            senders: ['10004346', '10004347'],
            receptors: ['09123456789', '09987654321'],
            messages: ['Message 1', 'Message 2']
        );
        
        $result = Kavenegar::sendArray($request);

        expect($result)->toHaveCount(2)
            ->and($result[0]->message)->toBe('Message 1')
            ->and($result[1]->message)->toBe('Message 2');

        Http::assertSent(function ($request) {
            return $request->method() === 'POST';
        });
    });

    it('throws exception for array length mismatch', function () {
        expect(fn () => new SendArrayRequest(
            senders: ['10004346'],
            receptors: ['09123456789', '09987654321'],
            messages: ['Message 1', 'Message 2']
        ))->toThrow(KavenegarValidationException::class, 'تعداد عناصر آرایه‌ها باید برابر باشد');
    });
});

describe('Status Checking', function () {
    it('checks status by message ID', function () {
        Http::fake([
            '*' => Http::response([
                'return' => ['status' => 200, 'message' => 'تایید شد'],
                'entries' => [[
                    'messageid' => 123456,
                    'status' => 10,
                    'statustext' => 'رسیده به گیرنده',
                ]],
            ]),
        ]);

        $result = Kavenegar::status(new StatusRequest('123456'));

        expect($result)->toBeArray()
            ->toHaveCount(1)
            ->and($result[0])->toBeInstanceOf(StatusResponse::class)
            ->and($result[0]->messageid)->toBe(123456)
            ->and($result[0]->status->value)->toBe(10)
            ->and($result[0]->isDelivered())->toBeTrue();
    });

    it('checks status for multiple messages', function () {
        Http::fake([
            '*' => Http::response([
                'return' => ['status' => 200, 'message' => 'تایید شد'],
                'entries' => [
                    ['messageid' => 123, 'status' => 10, 'statustext' => 'delivered'],
                    ['messageid' => 124, 'status' => 11, 'statustext' => 'failed'],
                ],
            ]),
        ]);

        $result = Kavenegar::status(new StatusRequest(['123', '124']));

        expect($result)->toHaveCount(2)
            ->and($result[0]->isDelivered())->toBeTrue()
            ->and($result[1]->isFailed())->toBeTrue();
    });

    it('returns status 100 for expired messages', function () {
        Http::fake([
            '*' => Http::response([
                'return' => ['status' => 200, 'message' => 'تایید شد'],
                'entries' => [[
                    'messageid' => 123456,
                    'status' => 100,
                    'statustext' => 'شناسه پیامک نامعتبر است',
                ]],
            ]),
        ]);

        $result = Kavenegar::status(new StatusRequest('123456'));

        expect($result[0]->status->value)->toBe(100);
    });
});

describe('Verify Lookup', function () {
    it('sends verification code with single token', function () {
        Http::fake([
            '*' => Http::response([
                'return' => ['status' => 200, 'message' => 'تایید شد'],
                'entries' => [[
                    'messageid' => 123456,
                    'message' => 'کد تایید: 123456',
                    'status' => 1,
                    'statustext' => 'در صف ارسال',
                    'sender' => '10004346',
                    'receptor' => '09123456789',
                    'date' => time(),
                    'cost' => 120,
                ]],
            ]),
        ]);

        $request = new VerifyLookupRequest(
            receptor: '09123456789',
            template: 'login-verify',
            token: '123456'
        );
        
        $result = Kavenegar::verifyLookup($request);

        expect($result)->toBeInstanceOf(MessageResponse::class)
            ->and($result->messageid)->toBe(123456);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'verify/lookup')
                && str_contains($request->url(), 'template=login-verify')
                && str_contains($request->url(), 'token=123456');
        });
    });

    it('sends verification with multiple tokens', function () {
        Http::fake([
            '*' => Http::response([
                'return' => ['status' => 200, 'message' => 'تایید شد'],
                'entries' => [[
                    'messageid' => 123456,
                    'message' => 'test',
                    'status' => 1,
                    'statustext' => 'queued',
                    'sender' => '10004346',
                    'receptor' => '09123456789',
                    'date' => time(),
                    'cost' => 120,
                ]],
            ]),
        ]);

        $request = new VerifyLookupRequest(
            receptor: '09123456789',
            template: 'email-pass',
            token: '123456',
            token2: 'user@example.com',
            token3: 'extra-data'
        );
        
        $result = Kavenegar::verifyLookup($request);

        expect($result)->toBeInstanceOf(MessageResponse::class);

        Http::assertSent(function ($request) {
            $url = $request->url();

            return (str_contains($url, 'token2=user@example.com') || str_contains($url, 'token2=user%40example.com'))
                && str_contains($url, 'token3=extra-data');
        });
    });
});

describe('Account Methods', function () {
    it('gets account info', function () {
        Http::fake([
            '*' => Http::response([
                'return' => ['status' => 200, 'message' => 'تایید شد'],
                'entries' => [[
                    'remaincredit' => 50000,
                    'expiredate' => time() + 86400,
                    'type' => 'premium',
                ]],
            ]),
        ]);

        $info = Kavenegar::info();

        expect($info)->toBeInstanceOf(AccountInfo::class)
            ->and($info->remaincredit)->toBe(50000)
            ->and($info->hasCredit())->toBeTrue()
            ->and($info->isExpired())->toBeFalse();
    });

    it('gets account config', function () {
        Http::fake([
            '*' => Http::response([
                'return' => ['status' => 200, 'message' => 'تایید شد'],
                'entries' => [[
                    'apilogs' => 'enabled',
                    'dailyreport' => 'disabled',
                    'debugmode' => '1',
                    'defaultsender' => '10004346',
                    'mincreditalarm' => 1000,
                    'resendfailed' => 'enabled',
                ]],
            ]),
        ]);

        $config = Kavenegar::config();

        expect($config)->toBeInstanceOf(AccountConfig::class)
            ->and($config->hasApiLogsEnabled())->toBeTrue()
            ->and($config->hasDebugModeEnabled())->toBeTrue();
    });
});

describe('Error Handling', function () {
    it('throws API exception for invalid API key', function () {
        Http::fake([
            '*' => Http::response([
                'return' => ['status' => 401, 'message' => 'کلید API نامعتبر است'],
                'entries' => [],
            ]),
        ]);

        $request = new SendMessageRequest(
            receptor: '09123456789',
            message: 'Test'
        );
        
        expect(fn () => Kavenegar::send($request))
            ->toThrow(KavenegarApiException::class)
            ->and(fn () => Kavenegar::send($request))
            ->toThrow(fn (KavenegarApiException $e) => $e->errorCode === 401);
    });

    it('throws HTTP exception for connection timeout', function () {
        Http::fake(function () {
            throw new \Illuminate\Http\Client\ConnectionException('Connection timeout');
        });

        $request = new SendMessageRequest(
            receptor: '09123456789',
            message: 'Test'
        );
        
        expect(fn () => Kavenegar::send($request))
            ->toThrow(KavenegarHttpException::class, 'Failed to connect to Kavenegar API');
    });

    it('throws HTTP exception for non-200 HTTP status', function () {
        Http::fake([
            '*' => Http::response('Server Error', 500),
        ]);

        $request = new SendMessageRequest(
            receptor: '09123456789',
            message: 'Test'
        );
        
        expect(fn () => Kavenegar::send($request))
            ->toThrow(KavenegarHttpException::class);
    });

    it('includes context in exceptions', function () {
        Http::fake([
            '*' => Http::response([
                'return' => ['status' => 418, 'message' => 'اعتبار کافی نیست'],
                'entries' => [],
            ]),
        ]);

        $request = new SendMessageRequest(
            receptor: '09123456789',
            message: 'Test'
        );
        
        expect(fn () => Kavenegar::send($request))
            ->toThrow(function (KavenegarApiException $e) {
                expect($e->getContext())->toBeArray()
                    ->and($e->errorCode)->toBe(418);

                return true;
            });
    });
});

describe('Facade vs Direct Usage', function () {
    it('works with facade', function () {
        Http::fake([
            '*' => Http::response([
                'return' => ['status' => 200, 'message' => 'OK'],
                'entries' => [[
                    'messageid' => 123,
                    'message' => 'test',
                    'status' => 1,
                    'statustext' => 'queued',
                    'sender' => '10004346',
                    'receptor' => '09123456789',
                    'date' => time(),
                    'cost' => 120,
                ]],
            ]),
        ]);

        $request = new SendMessageRequest(
            receptor: '09123456789',
            message: 'Test'
        );
        
        $result = Kavenegar::send($request);

        expect($result)->toBeArray()->toHaveCount(1);
    });

    it('works with dependency injection', function () {
        Http::fake([
            '*' => Http::response([
                'return' => ['status' => 200, 'message' => 'OK'],
                'entries' => [[
                    'messageid' => 123,
                    'message' => 'test',
                    'status' => 1,
                    'statustext' => 'queued',
                    'sender' => '10004346',
                    'receptor' => '09123456789',
                    'date' => time(),
                    'cost' => 120,
                ]],
            ]),
        ]);

        $client = app(KavenegarClient::class);
        $request = new SendMessageRequest(
            receptor: '09123456789',
            message: 'Test'
        );
        
        $result = $client->send($request);

        expect($result)->toBeArray()->toHaveCount(1);
    });
});
