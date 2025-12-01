<?php

declare(strict_types=1);

use FardaDev\Kavenegar\Enums\MessageTypeEnum;
use FardaDev\Kavenegar\Exceptions\InputValidationException;
use FardaDev\Kavenegar\Requests\SendMessageRequest;

describe('SendMessageRequest', function () {
    it('creates request with valid parameters', function () {
        $request = new SendMessageRequest(
            receptor: '09123456789',
            message: 'Test message'
        );

        expect($request->receptor)->toBe('09123456789');
        expect($request->message)->toBe('Test message');
    });

    it('accepts array of receptors', function () {
        $request = new SendMessageRequest(
            receptor: ['09123456789', '09987654321'],
            message: 'Test message'
        );

        expect($request->receptor)->toBeArray();
        expect($request->receptor)->toHaveCount(2);
    });

    it('accepts optional parameters', function () {
        $request = new SendMessageRequest(
            receptor: '09123456789',
            message: 'Test message',
            sender: '10004346',
            date: time() + 3600,
            type: MessageTypeEnum::NORMAL,
            localid: [123, 124],
            hide: 0,
            tag: 'test-tag',
            policy: 'test-policy'
        );

        expect($request->sender)->toBe('10004346');
        expect($request->type)->toBe(MessageTypeEnum::NORMAL);
        expect($request->tag)->toBe('test-tag');
    });

    it('throws exception for invalid receptor format', function () {
        expect(fn () => new SendMessageRequest(
            receptor: 'invalid',
            message: 'Test message'
        ))->toThrow(InputValidationException::class);
    });

    it('throws exception for empty message', function () {
        expect(fn () => new SendMessageRequest(
            receptor: '09123456789',
            message: ''
        ))->toThrow(InputValidationException::class);
    });

    it('throws exception for message exceeding 900 characters', function () {
        expect(fn () => new SendMessageRequest(
            receptor: '09123456789',
            message: str_repeat('a', 901)
        ))->toThrow(InputValidationException::class);
    });

    it('throws exception for invalid sender format', function () {
        expect(fn () => new SendMessageRequest(
            receptor: '09123456789',
            message: 'Test message',
            sender: 'invalid'
        ))->toThrow(InputValidationException::class);
    });

    it('throws exception for past date', function () {
        expect(fn () => new SendMessageRequest(
            receptor: '09123456789',
            message: 'Test message',
            date: time() - 3600
        ))->toThrow(InputValidationException::class);
    });

    it('throws exception for invalid tag format', function () {
        expect(fn () => new SendMessageRequest(
            receptor: '09123456789',
            message: 'Test message',
            tag: 'invalid tag!'
        ))->toThrow(InputValidationException::class);
    });

    it('throws exception for more than 200 receptors', function () {
        $receptors = array_fill(0, 201, '09123456789');

        expect(fn () => new SendMessageRequest(
            receptor: $receptors,
            message: 'Test message'
        ))->toThrow(InputValidationException::class);
    });

    it('converts to API parameters', function () {
        $request = new SendMessageRequest(
            receptor: '09123456789',
            message: 'Test message',
            sender: '10004346',
            type: MessageTypeEnum::NORMAL
        );

        $params = $request->toApiParams();

        expect($params)->toBeArray();
        expect($params)->toHaveKey('receptor');
        expect($params)->toHaveKey('message');
        expect($params)->toHaveKey('sender');
        expect($params)->toHaveKey('type');
        expect($params['type'])->toBe(1); // Enum converted to int
    });

    it('converts array receptors to comma-separated string in API params', function () {
        $request = new SendMessageRequest(
            receptor: ['09123456789', '09987654321'],
            message: 'Test message'
        );

        $params = $request->toApiParams();

        expect($params['receptor'])->toBe('09123456789,09987654321');
    });

    it('omits null optional parameters from API params', function () {
        $request = new SendMessageRequest(
            receptor: '09123456789',
            message: 'Test message'
        );

        $params = $request->toApiParams();

        expect($params)->not->toHaveKey('sender');
        expect($params)->not->toHaveKey('date');
        expect($params)->not->toHaveKey('type');
    });

    it('throws exception with validation errors for invalid receptor', function () {
        try {
            new SendMessageRequest(
                receptor: 'invalid',
                message: 'Test message'
            );
            expect(false)->toBeTrue('Should have thrown exception');
        } catch (InputValidationException $e) {
            expect($e->getErrors())->toBeInstanceOf(\Illuminate\Support\MessageBag::class)
                ->and($e->getErrors()->has('receptor.0'))->toBeTrue();
        }
    });
});
