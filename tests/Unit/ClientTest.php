<?php

declare(strict_types=1);

use FardaDev\Kavenegar\Client\KavenegarClient;
use FardaDev\Kavenegar\Exceptions\KavenegarValidationException;
use FardaDev\Kavenegar\Requests\SendMessageRequest;
use Illuminate\Support\Facades\Http;

describe('Client Validation', function () {
    it('validates tag format - rejects tags over 200 characters', function () {
        $client = new KavenegarClient('test-api-key');

        Http::fake();

        expect(fn () => new SendMessageRequest(
            receptor: '09123456789',
            message: 'test',
            tag: str_repeat('a', 201)
        ))->toThrow(KavenegarValidationException::class, 'تگ tag نباید بیشتر از 200 کاراکتر باشد');
    });

    it('validates tag format - rejects special characters', function () {
        $client = new KavenegarClient('test-api-key');

        Http::fake();

        expect(fn () => new SendMessageRequest(
            receptor: '09123456789',
            message: 'test',
            tag: 'invalid@tag!'
        ))->toThrow(KavenegarValidationException::class, 'تگ tag فقط می‌تواند شامل حروف و اعداد انگلیسی، خط تیره و زیرخط باشد');
    });

    it('validates tag format - accepts valid tags', function () {
        $client = new KavenegarClient('test-api-key');

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

        // Valid tags
        $validTags = ['test', 'test-tag', 'test_tag', 'test123', 'TEST-TAG_123'];

        foreach ($validTags as $tag) {
            $request = new SendMessageRequest(
                receptor: '09123456789',
                message: 'test',
                tag: $tag
            );
            
            expect(fn () => $client->send($request))->not->toThrow(KavenegarValidationException::class);
        }
    });

    it('validates array lengths in sendArray', function () {
        $client = new KavenegarClient('test-api-key');

        Http::fake();

        // Mismatched array lengths
        expect(fn () => $client->sendArray(
            senders: ['10004346', '10004347'],
            receptors: ['09123456789'],
            messages: ['test1', 'test2']
        ))->toThrow(KavenegarValidationException::class, 'All arrays must have the same length');
    });

    it('validates array lengths with optional parameters', function () {
        $client = new KavenegarClient('test-api-key');

        Http::fake();

        // Types array length mismatch
        expect(fn () => $client->sendArray(
            senders: ['10004346', '10004347'],
            receptors: ['09123456789', '09987654321'],
            messages: ['test1', 'test2'],
            types: [1] // Only 1 type for 2 messages
        ))->toThrow(KavenegarValidationException::class);
    });

    it('accepts matching array lengths', function () {
        $client = new KavenegarClient('test-api-key');

        Http::fake([
            '*' => Http::response([
                'return' => ['status' => 200, 'message' => 'OK'],
                'entries' => [
                    [
                        'messageid' => 123,
                        'message' => 'test1',
                        'status' => 1,
                        'statustext' => 'queued',
                        'sender' => '10004346',
                        'receptor' => '09123456789',
                        'date' => time(),
                        'cost' => 120,
                    ],
                    [
                        'messageid' => 124,
                        'message' => 'test2',
                        'status' => 1,
                        'statustext' => 'queued',
                        'sender' => '10004347',
                        'receptor' => '09987654321',
                        'date' => time(),
                        'cost' => 120,
                    ],
                ],
            ]),
        ]);

        $result = $client->sendArray(
            senders: ['10004346', '10004347'],
            receptors: ['09123456789', '09987654321'],
            messages: ['test1', 'test2']
        );

        expect($result)->toHaveCount(2);
    });
});

describe('Client Constructor', function () {
    it('creates client with api key', function () {
        $client = new KavenegarClient('test-api-key');

        expect($client)->toBeInstanceOf(KavenegarClient::class);
    });

    it('creates client with all parameters', function () {
        $client = new KavenegarClient(
            apiKey: 'test-api-key',
            defaultSender: '10004346',
            timeout: 60
        );

        expect($client)->toBeInstanceOf(KavenegarClient::class);
    });
});

describe('Client Helper Methods', function () {
    it('converts array to comma-separated string', function () {
        $client = new KavenegarClient('test-api-key');

        Http::fake([
            '*' => Http::response([
                'return' => ['status' => 200, 'message' => 'OK'],
                'entries' => [
                    [
                        'messageid' => 123,
                        'message' => 'test',
                        'status' => 1,
                        'statustext' => 'queued',
                        'sender' => '10004346',
                        'receptor' => '09123456789',
                        'date' => time(),
                        'cost' => 120,
                    ],
                    [
                        'messageid' => 124,
                        'message' => 'test',
                        'status' => 1,
                        'statustext' => 'queued',
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
            message: 'test'
        );
        
        $result = $client->send($request);

        expect($result)->toHaveCount(2);

        Http::assertSent(function ($request) {
            $url = $request->url();

            // Check for either encoded or non-encoded comma
            return str_contains($url, '09123456789') && str_contains($url, '09987654321');
        });
    });

    it('handles string receptor', function () {
        $client = new KavenegarClient('test-api-key');

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
            message: 'test'
        );
        
        $result = $client->send($request);

        expect($result)->toHaveCount(1);
    });
});
