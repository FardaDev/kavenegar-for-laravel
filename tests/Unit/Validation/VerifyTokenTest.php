<?php

declare(strict_types=1);

use FardaDev\Kavenegar\Validation\Rules\VerifyToken;

describe('VerifyToken', function () {
    it('passes validation for valid tokens without spaces', function () {
        $rule = new VerifyToken();
        $validTokens = [
            '123456',
            'کد123',
            'VerifyCode',
            'کد_تایید_123',
            str_repeat('a', 100), // max length
        ];
        
        foreach ($validTokens as $token) {
            $fails = false;
            
            $rule->validate('token', $token, function () use (&$fails) {
                $fails = true;
            });
            
            expect($fails)->toBeFalse("Failed for token: {$token}");
        }
    });

    it('fails validation for token with spaces', function () {
        $rule = new VerifyToken();
        $fails = false;
        
        $rule->validate('token', 'کد تایید', function () use (&$fails) {
            $fails = true;
        });
        
        expect($fails)->toBeTrue();
    });

    it('fails validation for token exceeding 100 characters', function () {
        $rule = new VerifyToken();
        $fails = false;
        $message = '';
        
        $rule->validate('token', str_repeat('a', 101), function ($msg) use (&$fails, &$message) {
            $fails = true;
            $message = $msg;
        });
        
        expect($fails)->toBeTrue();
        expect($message)->toContain('100');
    });

    it('provides Persian error message for spaces', function () {
        $rule = new VerifyToken();
        $message = '';
        
        $rule->validate('token', 'test token', function ($msg) use (&$message) {
            $message = $msg;
        });
        
        expect($message)->toBeString();
        expect($message)->toContain('فاصله');
    });
});
