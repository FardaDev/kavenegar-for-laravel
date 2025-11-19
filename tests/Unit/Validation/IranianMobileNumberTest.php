<?php

declare(strict_types=1);

use FardaDev\Kavenegar\Validation\Rules\IranianMobileNumber;

describe('IranianMobileNumber', function () {
    it('passes validation for valid Iranian mobile numbers', function () {
        $rule = new IranianMobileNumber;
        $fails = false;

        $rule->validate('receptor', '09123456789', function () use (&$fails) {
            $fails = true;
        });

        expect($fails)->toBeFalse();
    });

    it('passes validation for all valid Iranian mobile prefixes', function () {
        $rule = new IranianMobileNumber;
        $validPrefixes = ['0910', '0911', '0912', '0913', '0914', '0915', '0916', '0917', '0918', '0919', '0990', '0991', '0992'];

        foreach ($validPrefixes as $prefix) {
            $fails = false;
            $number = $prefix.'1234567';

            $rule->validate('receptor', $number, function () use (&$fails) {
                $fails = true;
            });

            expect($fails)->toBeFalse("Failed for prefix: {$prefix}");
        }
    });

    it('fails validation for numbers not starting with 09', function () {
        $rule = new IranianMobileNumber;
        $fails = false;
        $message = '';

        $rule->validate('receptor', '9123456789', function ($msg) use (&$fails, &$message) {
            $fails = true;
            $message = $msg;
        });

        expect($fails)->toBeTrue();
        expect($message)->toContain('09');
    });

    it('fails validation for numbers with wrong length', function () {
        $rule = new IranianMobileNumber;

        // Too short
        $fails = false;
        $rule->validate('receptor', '091234567', function () use (&$fails) {
            $fails = true;
        });
        expect($fails)->toBeTrue();

        // Too long
        $fails = false;
        $rule->validate('receptor', '091234567890', function () use (&$fails) {
            $fails = true;
        });
        expect($fails)->toBeTrue();
    });

    it('fails validation for non-numeric characters', function () {
        $rule = new IranianMobileNumber;
        $fails = false;

        $rule->validate('receptor', '0912345678a', function () use (&$fails) {
            $fails = true;
        });

        expect($fails)->toBeTrue();
    });

    it('provides Persian error message', function () {
        $rule = new IranianMobileNumber;
        $message = '';

        $rule->validate('receptor', 'invalid', function ($msg) use (&$message) {
            $message = $msg;
        });

        expect($message)->toBeString();
        expect($message)->toContain('09');
    });
});
