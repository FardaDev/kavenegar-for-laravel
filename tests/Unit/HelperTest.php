<?php

declare(strict_types=1);

use FardaDev\Kavenegar\Client\KavenegarClient;
use FardaDev\Kavenegar\Helpers\KavenegarHelper;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config()->set('kavenegar.templates', [
        'login' => 'login-verify',
        'email_password' => 'email-pass',
        'two_factor' => 'email-2fa',
    ]);
});

describe('Helper Token Normalization', function () {
    it('normalizes tokens with whitespace', function () {
        $client = new KavenegarClient('test-api-key');
        $helper = new KavenegarHelper($client);

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

        $result = $helper->sendLoginCode('09123456789', '123 456');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '123-456');
        });
    });

    it('handles tokens with multiple spaces', function () {
        $client = new KavenegarClient('test-api-key');
        $helper = new KavenegarHelper($client);

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

        $result = $helper->sendLoginCode('09123456789', '1  2   3');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '1-2-3');
        });
    });
});

describe('Helper Environment Detection', function () {
    it('skips SMS in local environment', function () {
        config()->set('kavenegar.skip_in_development', true);
        app()->detectEnvironment(fn () => 'local');

        $client = new KavenegarClient('test-api-key');
        $helper = new KavenegarHelper($client);

        expect($helper->shouldSkipInDevelopment('09123456789'))->toBeTrue();

        $result = $helper->sendLoginCode('09123456789', '123456');

        expect($result)->toBeTrue();
    });

    it('skips SMS in dev environment', function () {
        config()->set('kavenegar.skip_in_development', true);
        app()->detectEnvironment(fn () => 'dev');

        $client = new KavenegarClient('test-api-key');
        $helper = new KavenegarHelper($client);

        expect($helper->shouldSkipInDevelopment('09123456789'))->toBeTrue();
    });

    it('skips SMS for test numbers in testing environment', function () {
        config()->set('kavenegar.skip_in_development', true);
        config()->set('kavenegar.test_phone_numbers', ['09112223344']);
        app()->detectEnvironment(fn () => 'testing');

        $client = new KavenegarClient('test-api-key');
        $helper = new KavenegarHelper($client);

        expect($helper->shouldSkipInDevelopment('09112223344'))->toBeTrue();
        expect($helper->shouldSkipInDevelopment('09123456789'))->toBeFalse();
    });

    it('does not skip when disabled', function () {
        config()->set('kavenegar.skip_in_development', false);
        app()->detectEnvironment(fn () => 'local');

        $client = new KavenegarClient('test-api-key');
        $helper = new KavenegarHelper($client);

        expect($helper->shouldSkipInDevelopment('09123456789'))->toBeFalse();
    });

    it('does not skip in production', function () {
        config()->set('kavenegar.skip_in_development', true);
        app()->detectEnvironment(fn () => 'production');

        $client = new KavenegarClient('test-api-key');
        $helper = new KavenegarHelper($client);

        expect($helper->shouldSkipInDevelopment('09123456789'))->toBeFalse();
    });
});

describe('Helper Send Methods', function () {
    it('sends login code', function () {
        config()->set('kavenegar.skip_in_development', false);

        $client = new KavenegarClient('test-api-key');
        $helper = new KavenegarHelper($client);

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

        $result = $helper->sendLoginCode('09123456789', '123456');

        expect($result)->toBeObject()
            ->and($result->messageid)->toBe(123);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'verify/lookup')
                && str_contains($request->url(), 'login-verify')
                && str_contains($request->url(), '123456');
        });
    });

    it('sends email password code', function () {
        config()->set('kavenegar.skip_in_development', false);

        $client = new KavenegarClient('test-api-key');
        $helper = new KavenegarHelper($client);

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

        $result = $helper->sendEmailPasswordCode('09123456789', '123456', 'user@example.com');

        expect($result)->toBeObject();

        Http::assertSent(function ($request) {
            $url = $request->url();
            return str_contains($url, 'email-pass')
                && (str_contains($url, 'user@example.com') || str_contains($url, 'user%40example.com'));
        });
    });

    it('sends two factor code', function () {
        config()->set('kavenegar.skip_in_development', false);

        $client = new KavenegarClient('test-api-key');
        $helper = new KavenegarHelper($client);

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

        $result = $helper->sendTwoFactorCode('09123456789', '654321', 'user@example.com');

        expect($result)->toBeObject();

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'email-2fa')
                && str_contains($request->url(), '654321');
        });
    });
});
