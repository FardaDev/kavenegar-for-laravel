<?php

declare(strict_types=1);

use FardaDev\Kavenegar\Exceptions\InputValidationException;
use FardaDev\Kavenegar\Requests\LatestOutboxRequest;

describe('LatestOutboxRequest', function () {
    it('creates request with no parameters', function () {
        $request = new LatestOutboxRequest;

        expect($request->pagesize)->toBeNull();
        expect($request->sender)->toBeNull();
    });

    it('creates request with pagesize', function () {
        $request = new LatestOutboxRequest(pagesize: 100);

        expect($request->pagesize)->toBe(100);
    });

    it('creates request with sender', function () {
        $request = new LatestOutboxRequest(sender: '10004346');

        expect($request->sender)->toBe('10004346');
    });

    it('creates request with both parameters', function () {
        $request = new LatestOutboxRequest(
            pagesize: 100,
            sender: '10004346'
        );

        expect($request->pagesize)->toBe(100);
        expect($request->sender)->toBe('10004346');
    });

    it('throws exception for pagesize exceeding 500', function () {
        expect(fn () => new LatestOutboxRequest(pagesize: 501))
            ->toThrow(InputValidationException::class);
    });

    it('accepts pagesize of exactly 500', function () {
        $request = new LatestOutboxRequest(pagesize: 500);

        expect($request->pagesize)->toBe(500);
    });

    it('converts to API parameters', function () {
        $request = new LatestOutboxRequest(
            pagesize: 100,
            sender: '10004346'
        );

        $params = $request->toApiParams();

        expect($params)->toBeArray();
        expect($params)->toHaveKey('pagesize');
        expect($params)->toHaveKey('sender');
        expect($params['pagesize'])->toBe(100);
        expect($params['sender'])->toBe('10004346');
    });

    it('omits null parameters from API params', function () {
        $request = new LatestOutboxRequest;

        $params = $request->toApiParams();

        expect($params)->toBeArray();
        expect($params)->not->toHaveKey('pagesize');
        expect($params)->not->toHaveKey('sender');
    });

    it('caps pagesize at 500 in API params', function () {
        $request = new LatestOutboxRequest(pagesize: 500);

        $params = $request->toApiParams();

        expect($params['pagesize'])->toBe(500);
    });
});

