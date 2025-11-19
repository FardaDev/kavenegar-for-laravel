<?php

declare(strict_types=1);

use FardaDev\Kavenegar\Enums\ApiErrorCodeEnum;
use FardaDev\Kavenegar\Enums\MessageTypeEnum;
use FardaDev\Kavenegar\Exceptions\KavenegarValidationException;
use FardaDev\Kavenegar\Requests\VerifyLookupRequest;

describe('VerifyLookupRequest', function () {
    it('creates request with required parameters', function () {
        $request = new VerifyLookupRequest(
            receptor: '09123456789',
            template: 'verify-template',
            token: '123456'
        );
        
        expect($request->receptor)->toBe('09123456789');
        expect($request->template)->toBe('verify-template');
        expect($request->token)->toBe('123456');
    });

    it('accepts optional token parameters', function () {
        $request = new VerifyLookupRequest(
            receptor: '09123456789',
            template: 'verify-template',
            token: '123456',
            token2: 'second',
            token3: 'third',
            token10: 'tenth',
            token20: 'twentieth'
        );
        
        expect($request->token2)->toBe('second');
        expect($request->token3)->toBe('third');
        expect($request->token10)->toBe('tenth');
        expect($request->token20)->toBe('twentieth');
    });

    it('accepts optional type parameter', function () {
        $request = new VerifyLookupRequest(
            receptor: '09123456789',
            template: 'verify-template',
            token: '123456',
            type: MessageTypeEnum::FLASH
        );
        
        expect($request->type)->toBe(MessageTypeEnum::FLASH);
    });

    it('throws exception for invalid receptor format', function () {
        expect(fn() => new VerifyLookupRequest(
            receptor: 'invalid',
            template: 'verify-template',
            token: '123456'
        ))->toThrow(KavenegarValidationException::class);
    });

    it('throws exception for empty template', function () {
        expect(fn() => new VerifyLookupRequest(
            receptor: '09123456789',
            template: '',
            token: '123456'
        ))->toThrow(KavenegarValidationException::class);
    });

    it('throws exception for template exceeding 100 characters', function () {
        expect(fn() => new VerifyLookupRequest(
            receptor: '09123456789',
            template: str_repeat('a', 101),
            token: '123456'
        ))->toThrow(KavenegarValidationException::class);
    });

    it('throws exception for empty token', function () {
        expect(fn() => new VerifyLookupRequest(
            receptor: '09123456789',
            template: 'verify-template',
            token: ''
        ))->toThrow(KavenegarValidationException::class);
    });

    it('throws exception for token exceeding 200 characters', function () {
        expect(fn() => new VerifyLookupRequest(
            receptor: '09123456789',
            template: 'verify-template',
            token: str_repeat('a', 201)
        ))->toThrow(KavenegarValidationException::class);
    });

    it('throws exception for token2 exceeding 200 characters', function () {
        expect(fn() => new VerifyLookupRequest(
            receptor: '09123456789',
            template: 'verify-template',
            token: '123456',
            token2: str_repeat('a', 201)
        ))->toThrow(KavenegarValidationException::class);
    });

    it('throws exception for token3 exceeding 200 characters', function () {
        expect(fn() => new VerifyLookupRequest(
            receptor: '09123456789',
            template: 'verify-template',
            token: '123456',
            token3: str_repeat('a', 201)
        ))->toThrow(KavenegarValidationException::class);
    });

    it('throws exception for token10 exceeding 200 characters', function () {
        expect(fn() => new VerifyLookupRequest(
            receptor: '09123456789',
            template: 'verify-template',
            token: '123456',
            token10: str_repeat('a', 201)
        ))->toThrow(KavenegarValidationException::class);
    });

    it('throws exception for token20 exceeding 200 characters', function () {
        expect(fn() => new VerifyLookupRequest(
            receptor: '09123456789',
            template: 'verify-template',
            token: '123456',
            token20: str_repeat('a', 201)
        ))->toThrow(KavenegarValidationException::class);
    });

    it('converts to API parameters', function () {
        $request = new VerifyLookupRequest(
            receptor: '09123456789',
            template: 'verify-template',
            token: '123456',
            token2: 'second',
            type: MessageTypeEnum::NORMAL
        );
        
        $params = $request->toApiParams();
        
        expect($params)->toBeArray();
        expect($params)->toHaveKey('receptor');
        expect($params)->toHaveKey('template');
        expect($params)->toHaveKey('token');
        expect($params)->toHaveKey('token2');
        expect($params)->toHaveKey('type');
        expect($params['type'])->toBe(1); // Enum converted to int
    });

    it('omits null optional parameters from API params', function () {
        $request = new VerifyLookupRequest(
            receptor: '09123456789',
            template: 'verify-template',
            token: '123456'
        );
        
        $params = $request->toApiParams();
        
        expect($params)->not->toHaveKey('token2');
        expect($params)->not->toHaveKey('token3');
        expect($params)->not->toHaveKey('token10');
        expect($params)->not->toHaveKey('token20');
        expect($params)->not->toHaveKey('type');
    });

    it('throws exception with proper error code for invalid receptor', function () {
        try {
            new VerifyLookupRequest(
                receptor: 'invalid',
                template: 'verify-template',
                token: '123456'
            );
            expect(false)->toBeTrue('Should have thrown exception');
        } catch (KavenegarValidationException $e) {
            expect($e->getCode())->toBe(ApiErrorCodeEnum::INVALID_RECEPTOR->value);
        }
    });
});
