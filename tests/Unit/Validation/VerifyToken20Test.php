<?php

declare(strict_types=1);

use FardaDev\Kavenegar\Validation\Rules\VerifyToken20;

describe('VerifyToken20', function () {
    it('passes validation for valid tokens with up to 8 spaces', function () {
        $rule = new VerifyToken20();
        $validTokens = [
            '123456',
            'کد تایید شماره یک',
            'one two three four five six seven eight',
            'a b c d e f g h',
            str_repeat('a', 92) . '        ', // 8 spaces
        ];
        
        foreach ($validTokens as $token) {
            $fails = false;
            
            $rule->validate('token20', $token, function () use (&$fails) {
                $fails = true;
            });
            
            expect($fails)->toBeFalse("Failed for token: {$token}");
        }
    });

    it('fails validation for token with more than 8 spaces', function () {
        $rule = new VerifyToken20();
        $fails = false;
        $message = '';
        
        $rule->validate('token20', 'a b c d e f g h i j', function ($msg) use (&$fails, &$message) {
            $fails = true;
            $message = $msg;
        });
        
        expect($fails)->toBeTrue();
        expect($message)->toContain('8');
    });

    it('fails validation for token exceeding 100 characters', function () {
        $rule = new VerifyToken20();
        $fails = false;
        $message = '';
        
        $rule->validate('token20', str_repeat('a', 101), function ($msg) use (&$fails, &$message) {
            $fails = true;
            $message = $msg;
        });
        
        expect($fails)->toBeTrue();
        expect($message)->toContain('100');
    });

    it('provides Persian error message', function () {
        $rule = new VerifyToken20();
        $message = '';
        
        $rule->validate('token20', 'a b c d e f g h i j', function ($msg) use (&$message) {
            $message = $msg;
        });
        
        expect($message)->toBeString();
        expect($message)->toContain('فاصله');
    });
});
