<?php

declare(strict_types=1);

use FardaDev\Kavenegar\Validation\Rules\KavenegarSenderLine;

describe('KavenegarSenderLine', function () {
    it('passes validation for plain numeric sender', function () {
        $rule = new KavenegarSenderLine();
        $fails = false;
        
        $rule->validate('sender', '10004346', function () use (&$fails) {
            $fails = true;
        });
        
        expect($fails)->toBeFalse();
    });

    it('passes validation for sender with + prefix', function () {
        $rule = new KavenegarSenderLine();
        $fails = false;
        
        $rule->validate('sender', '+9810004346', function () use (&$fails) {
            $fails = true;
        });
        
        expect($fails)->toBeFalse();
    });

    it('passes validation for sender with 00 prefix', function () {
        $rule = new KavenegarSenderLine();
        $fails = false;
        
        $rule->validate('sender', '009810004346', function () use (&$fails) {
            $fails = true;
        });
        
        expect($fails)->toBeFalse();
    });

    it('passes validation for various sender formats and lengths', function () {
        $rule = new KavenegarSenderLine();
        $validSenders = [
            '3000',           // 4 digits (short code)
            '30002',          // 5 digits
            '10004346',       // 8 digits
            '30002626',       // 8 digits
            '3000202030',     // 10 digits
            '30001528961415', // 14 digits
            '010004346',      // 9 digits with single leading zero
            '+9810004346',    // with + prefix
            '009810004346',   // with 00 prefix
        ];
        
        foreach ($validSenders as $sender) {
            $fails = false;
            
            $rule->validate('sender', $sender, function () use (&$fails) {
                $fails = true;
            });
            
            expect($fails)->toBeFalse("Failed for sender: {$sender}");
        }
    });

    it('fails validation for empty sender', function () {
        $rule = new KavenegarSenderLine();
        $fails = false;
        
        $rule->validate('sender', '', function () use (&$fails) {
            $fails = true;
        });
        
        expect($fails)->toBeTrue();
    });

    it('fails validation for non-numeric sender without valid prefix', function () {
        $rule = new KavenegarSenderLine();
        $fails = false;
        
        $rule->validate('sender', 'put_number_here', function () use (&$fails) {
            $fails = true;
        });
        
        expect($fails)->toBeTrue();
    });

    it('fails validation for sender with letters', function () {
        $rule = new KavenegarSenderLine();
        $fails = false;
        
        $rule->validate('sender', '1000abc', function () use (&$fails) {
            $fails = true;
        });
        
        expect($fails)->toBeTrue();
    });

    it('fails validation for too short sender', function () {
        $rule = new KavenegarSenderLine();
        $fails = false;
        
        $rule->validate('sender', '123', function () use (&$fails) {
            $fails = true;
        });
        
        expect($fails)->toBeTrue();
    });

    it('fails validation for too long sender', function () {
        $rule = new KavenegarSenderLine();
        $fails = false;
        
        $rule->validate('sender', '123456789012345678', function () use (&$fails) {
            $fails = true;
        });
        
        expect($fails)->toBeTrue();
    });

    it('provides Persian error message', function () {
        $rule = new KavenegarSenderLine();
        $message = '';
        
        $rule->validate('sender', 'invalid', function ($msg) use (&$message) {
            $message = $msg;
        });
        
        expect($message)->toBeString();
        expect($message)->toContain('فرستنده');
    });
});
