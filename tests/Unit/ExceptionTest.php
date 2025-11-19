<?php

declare(strict_types=1);

use FardaDev\Kavenegar\Exceptions\KavenegarApiException;
use FardaDev\Kavenegar\Exceptions\KavenegarException;
use FardaDev\Kavenegar\Exceptions\KavenegarHttpException;
use FardaDev\Kavenegar\Exceptions\InputValidationException;

it('creates api exception with error code and context', function () {
    $exception = new KavenegarApiException(
        message: 'Invalid API key',
        errorCode: 401,
        context: ['api_key' => 'invalid-key']
    );

    expect($exception)
        ->toBeInstanceOf(KavenegarException::class)
        ->and($exception->getMessage())->toBe('Invalid API key')
        ->and($exception->errorCode)->toBe(401)
        ->and($exception->getContext())->toBe(['api_key' => 'invalid-key']);
});

it('creates http exception for network errors', function () {
    $exception = new KavenegarHttpException(
        message: 'Connection timeout',
        errorCode: 0,
        context: ['timeout' => 30]
    );

    expect($exception)
        ->toBeInstanceOf(KavenegarException::class)
        ->and($exception->getMessage())->toBe('Connection timeout')
        ->and($exception->errorCode)->toBe(0)
        ->and($exception->getContext())->toBe(['timeout' => 30]);
});

it('creates validation exception for input errors', function () {
    $errors = new \Illuminate\Support\MessageBag([
        'arrays' => ['Array length mismatch'],
        'lengths' => ['Lengths do not match']
    ]);
    
    $exception = new InputValidationException($errors);

    expect($exception)
        ->toBeInstanceOf(Exception::class)
        ->and($exception->getMessage())->toBe("Array length mismatch\nLengths do not match")
        ->and($exception->getErrors())->toBe($errors)
        ->and($exception->getErrorsArray())->toBe([
            'arrays' => ['Array length mismatch'],
            'lengths' => ['Lengths do not match']
        ]);
});

it('allows null context', function () {
    $exception = new KavenegarApiException(
        message: 'Error without context',
        errorCode: 400
    );

    expect($exception->getContext())->toBeNull();
});

it('preserves previous exception', function () {
    $previous = new Exception('Original error');
    $exception = new KavenegarHttpException(
        message: 'Wrapped error',
        errorCode: 0,
        previous: $previous
    );

    expect($exception->getPrevious())->toBe($previous);
});

