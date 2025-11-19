<?php

declare(strict_types=1);

use FardaDev\Kavenegar\Validation\Rules\VerifyToken10;

describe('VerifyToken10', function () {
    it('passes validation for valid tokens with up to 5 spaces', function () {
        $rule = new VerifyToken10();
        $validTokens = [
            '123456',
            'کد تایید',
            'one two three four five',
            'a b c d e',
            str_repeat('a', 95) . '     ', // 5 spaces
        ];
        
        foreach ($validTokens as $token) {
            $fails = false;
            
            $rule->validate('token10', $token, function () use (&$fails) {
                $fails = true;
            });
            
            expect($fails)->toBeFalse("Failed for token: {$token}");
        }
    });

    it('fails validation for token with more than 5 spaces', function () {
        $rule = new VerifyToken10();
        $fails = false;
        $message = '';
        
        $rule->validate('token10', 'a b c d e f g', function ($msg) use (&$fails, &$message) {
            $fails = true;
            $message = $msg;
        });
        
        expect($fails)->toBeTrue();
        expect($message)->toContain('5');
    });

    it('fails validation for token exceeding 100 characters', function () {
        $rule = new VerifyToken10();
        $fails = false;
        $message = '';
        
        $rule->validate('token10', str_repeat('a', 101), function ($msg) use (&$fails, &$message) {
            $fails = true;
            $message = $msg;
        });
        
        expect($fails)->toBeTrue();
        expect($message)->toContain('100');
    });

    it('provides Persian error message', function () {
        $rule = new VerifyToken10();
        $message = '';
        
        $rule->validate('token10', 'a b c d e f g', function ($msg) use (&$message) {
            $message = $msg;
        });
        
        expect($message)->toBeString();
        expect($message)->toContain('فاصله');
    });
});
