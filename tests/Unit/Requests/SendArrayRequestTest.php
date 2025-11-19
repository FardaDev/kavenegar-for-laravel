<?php

declare(strict_types=1);

use FardaDev\Kavenegar\Enums\ApiErrorCodeEnum;
use FardaDev\Kavenegar\Enums\MessageTypeEnum;
use FardaDev\Kavenegar\Exceptions\KavenegarValidationException;
use FardaDev\Kavenegar\Requests\SendArrayRequest;

describe('SendArrayRequest', function () {
    it('creates request with valid arrays', function () {
        $request = new SendArrayRequest(
            senders: ['10004346', '10004347'],
            receptors: ['09123456789', '09987654321'],
            messages: ['Message 1', 'Message 2']
        );
        
        expect($request->senders)->toHaveCount(2);
        expect($request->receptors)->toHaveCount(2);
        expect($request->messages)->toHaveCount(2);
    });

    it('accepts optional parameters', function () {
        $request = new SendArrayRequest(
            senders: ['10004346'],
            receptors: ['09123456789'],
            messages: ['Test message'],
            date: time() + 3600,
            types: [MessageTypeEnum::NORMAL],
            localids: [123],
            hide: 0,
            tag: 'test-tag',
            policy: 'test-policy'
        );
        
        expect($request->date)->toBeInt();
        expect($request->types)->toHaveCount(1);
        expect($request->tag)->toBe('test-tag');
    });

    it('throws exception for missing senders', function () {
        expect(fn() => new SendArrayRequest(
            senders: [],
            receptors: ['09123456789'],
            messages: ['Test message']
        ))->toThrow(KavenegarValidationException::class);
    });

    it('throws exception for missing receptors', function () {
        expect(fn() => new SendArrayRequest(
            senders: ['10004346'],
            receptors: [],
            messages: ['Test message']
        ))->toThrow(KavenegarValidationException::class);
    });

    it('throws exception for missing messages', function () {
        expect(fn() => new SendArrayRequest(
            senders: ['10004346'],
            receptors: ['09123456789'],
            messages: []
        ))->toThrow(KavenegarValidationException::class);
    });

    it('throws exception for array length mismatch', function () {
        expect(fn() => new SendArrayRequest(
            senders: ['10004346', '10004347'],
            receptors: ['09123456789'],
            messages: ['Message 1', 'Message 2']
        ))->toThrow(KavenegarValidationException::class);
    });

    it('throws exception with ARRAY_LENGTH_MISMATCH error code', function () {
        try {
            new SendArrayRequest(
                senders: ['10004346', '10004347'],
                receptors: ['09123456789'],
                messages: ['Message 1', 'Message 2']
            );
            expect(false)->toBeTrue('Should have thrown exception');
        } catch (KavenegarValidationException $e) {
            expect($e->getCode())->toBe(ApiErrorCodeEnum::ARRAY_LENGTH_MISMATCH->value);
        }
    });

    it('throws exception for arrays exceeding 200 elements', function () {
        $senders = array_fill(0, 201, '10004346');
        $receptors = array_fill(0, 201, '09123456789');
        $messages = array_fill(0, 201, 'Test message');
        
        expect(fn() => new SendArrayRequest(
            senders: $senders,
            receptors: $receptors,
            messages: $messages
        ))->toThrow(KavenegarValidationException::class);
    });

    it('throws exception for invalid sender in array', function () {
        expect(fn() => new SendArrayRequest(
            senders: ['10004346', 'invalid'],
            receptors: ['09123456789', '09987654321'],
            messages: ['Message 1', 'Message 2']
        ))->toThrow(KavenegarValidationException::class);
    });

    it('throws exception for invalid receptor in array', function () {
        expect(fn() => new SendArrayRequest(
            senders: ['10004346', '10004347'],
            receptors: ['09123456789', 'invalid'],
            messages: ['Message 1', 'Message 2']
        ))->toThrow(KavenegarValidationException::class);
    });

    it('throws exception for message exceeding 900 characters', function () {
        expect(fn() => new SendArrayRequest(
            senders: ['10004346', '10004347'],
            receptors: ['09123456789', '09987654321'],
            messages: ['Message 1', str_repeat('a', 901)]
        ))->toThrow(KavenegarValidationException::class);
    });

    it('throws exception for past date', function () {
        expect(fn() => new SendArrayRequest(
            senders: ['10004346'],
            receptors: ['09123456789'],
            messages: ['Message 1'],
            date: time() - 3600
        ))->toThrow(KavenegarValidationException::class);
    });

    it('throws exception for invalid tag format', function () {
        expect(fn() => new SendArrayRequest(
            senders: ['10004346'],
            receptors: ['09123456789'],
            messages: ['Message 1'],
            tag: 'invalid tag!'
        ))->toThrow(KavenegarValidationException::class);
    });

    it('throws exception when types array length does not match', function () {
        expect(fn() => new SendArrayRequest(
            senders: ['10004346', '10004347'],
            receptors: ['09123456789', '09987654321'],
            messages: ['Message 1', 'Message 2'],
            types: [MessageTypeEnum::NORMAL]
        ))->toThrow(KavenegarValidationException::class);
    });

    it('throws exception when localids array length does not match', function () {
        expect(fn() => new SendArrayRequest(
            senders: ['10004346', '10004347'],
            receptors: ['09123456789', '09987654321'],
            messages: ['Message 1', 'Message 2'],
            localids: [123]
        ))->toThrow(KavenegarValidationException::class);
    });

    it('converts to API parameters', function () {
        $request = new SendArrayRequest(
            senders: ['10004346', '10004347'],
            receptors: ['09123456789', '09987654321'],
            messages: ['Message 1', 'Message 2'],
            types: [MessageTypeEnum::NORMAL, MessageTypeEnum::FLASH]
        );
        
        $params = $request->toApiParams();
        
        expect($params)->toBeArray();
        expect($params)->toHaveKey('sender');
        expect($params)->toHaveKey('receptor');
        expect($params)->toHaveKey('message');
        expect($params)->toHaveKey('type');
        expect($params['sender'])->toBe(['10004346', '10004347']);
        expect($params['type'])->toBe([1, 0]); // Enums converted to ints
    });

    it('omits null optional parameters from API params', function () {
        $request = new SendArrayRequest(
            senders: ['10004346'],
            receptors: ['09123456789'],
            messages: ['Message 1']
        );
        
        $params = $request->toApiParams();
        
        expect($params)->not->toHaveKey('date');
        expect($params)->not->toHaveKey('type');
        expect($params)->not->toHaveKey('localmessageids');
    });
});
