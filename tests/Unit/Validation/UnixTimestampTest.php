<?php

declare(strict_types=1);

use FardaDev\Kavenegar\Validation\Rules\UnixTimestamp;

describe('UnixTimestamp', function () {
    it('passes validation for valid unix timestamps', function () {
        $rule = new UnixTimestamp();
        $validTimestamps = [
            time(),
            time() + 3600, // 1 hour from now
            time() - 3600, // 1 hour ago
            1735689600, // 2025-01-01 00:00:00 UTC
            0, // Unix epoch
        ];
        
        foreach ($validTimestamps as $timestamp) {
            $fails = false;
            
            $rule->validate('date', $timestamp, function () use (&$fails) {
                $fails = true;
            });
            
            expect($fails)->toBeFalse("Failed for timestamp: {$timestamp}");
        }
    });

    it('fails validation for negative timestamps', function () {
        $rule = new UnixTimestamp();
        $fails = false;
        
        $rule->validate('date', -1, function () use (&$fails) {
            $fails = true;
        });
        
        expect($fails)->toBeTrue();
    });

    it('fails validation for non-numeric values', function () {
        $rule = new UnixTimestamp();
        $invalidValues = [
            'not-a-number',
            '2025-01-01',
            null,
            [],
        ];
        
        foreach ($invalidValues as $value) {
            $fails = false;
            
            $rule->validate('date', $value, function () use (&$fails) {
                $fails = true;
            });
            
            expect($fails)->toBeTrue("Should fail for value: " . json_encode($value));
        }
    });

    it('rejects past timestamps when allowPast is false', function () {
        $rule = new UnixTimestamp(allowPast: false);
        $fails = false;
        
        $pastTimestamp = time() - 3600; // 1 hour ago
        
        $rule->validate('date', $pastTimestamp, function () use (&$fails) {
            $fails = true;
        });
        
        expect($fails)->toBeTrue();
    });

    it('accepts past timestamps when allowPast is true', function () {
        $rule = new UnixTimestamp(allowPast: true);
        $fails = false;
        
        $pastTimestamp = time() - 3600; // 1 hour ago
        
        $rule->validate('date', $pastTimestamp, function () use (&$fails) {
            $fails = true;
        });
        
        expect($fails)->toBeFalse();
    });

    it('rejects future timestamps when allowFuture is false', function () {
        $rule = new UnixTimestamp(allowFuture: false);
        $fails = false;
        
        $futureTimestamp = time() + 3600; // 1 hour from now
        
        $rule->validate('date', $futureTimestamp, function () use (&$fails) {
            $fails = true;
        });
        
        expect($fails)->toBeTrue();
    });

    it('accepts future timestamps when allowFuture is true', function () {
        $rule = new UnixTimestamp(allowFuture: true);
        $fails = false;
        
        $futureTimestamp = time() + 3600; // 1 hour from now
        
        $rule->validate('date', $futureTimestamp, function () use (&$fails) {
            $fails = true;
        });
        
        expect($fails)->toBeFalse();
    });

    it('provides Persian error message for invalid format', function () {
        $rule = new UnixTimestamp();
        $message = '';
        
        $rule->validate('date', 'invalid', function ($msg) use (&$message) {
            $message = $msg;
        });
        
        expect($message)->toBeString();
        expect($message)->toContain('UnixTime');
    });

    it('provides Persian error message for past date when not allowed', function () {
        $rule = new UnixTimestamp(allowPast: false);
        $message = '';
        
        $rule->validate('date', time() - 3600, function ($msg) use (&$message) {
            $message = $msg;
        });
        
        expect($message)->toBeString();
        expect($message)->toContain('گذشته');
    });

    it('provides Persian error message for future date when not allowed', function () {
        $rule = new UnixTimestamp(allowFuture: false);
        $message = '';
        
        $rule->validate('date', time() + 3600, function ($msg) use (&$message) {
            $message = $msg;
        });
        
        expect($message)->toBeString();
        expect($message)->toContain('آینده');
    });

    it('accepts numeric strings as valid timestamps', function () {
        $rule = new UnixTimestamp();
        $fails = false;
        
        $rule->validate('date', '1735689600', function () use (&$fails) {
            $fails = true;
        });
        
        expect($fails)->toBeFalse();
    });
});
