<?php

declare(strict_types=1);

use FardaDev\Kavenegar\Validation\Rules\KavenegarTag;

describe('KavenegarTag', function () {
    it('passes validation for valid tags', function () {
        $rule = new KavenegarTag();
        $validTags = [
            'test',
            'test-tag',
            'test_tag',
            'test-tag_123',
            'TAG123',
            'a',
            str_repeat('a', 200), // max length
        ];
        
        foreach ($validTags as $tag) {
            $fails = false;
            
            $rule->validate('tag', $tag, function () use (&$fails) {
                $fails = true;
            });
            
            expect($fails)->toBeFalse("Failed for tag: {$tag}");
        }
    });

    it('fails validation for tag exceeding 200 characters', function () {
        $rule = new KavenegarTag();
        $fails = false;
        $message = '';
        
        $rule->validate('tag', str_repeat('a', 201), function ($msg) use (&$fails, &$message) {
            $fails = true;
            $message = $msg;
        });
        
        expect($fails)->toBeTrue();
        expect($message)->toContain('200');
    });

    it('fails validation for tag with spaces', function () {
        $rule = new KavenegarTag();
        $fails = false;
        
        $rule->validate('tag', 'test tag', function () use (&$fails) {
            $fails = true;
        });
        
        expect($fails)->toBeTrue();
    });

    it('fails validation for tag with special characters', function () {
        $rule = new KavenegarTag();
        $invalidTags = [
            'test@tag',
            'test&tag',
            'test*tag',
            'test.tag',
            'test!tag',
            'test#tag',
        ];
        
        foreach ($invalidTags as $tag) {
            $fails = false;
            
            $rule->validate('tag', $tag, function () use (&$fails) {
                $fails = true;
            });
            
            expect($fails)->toBeTrue("Should fail for tag: {$tag}");
        }
    });

    it('fails validation for empty tag', function () {
        $rule = new KavenegarTag();
        $fails = false;
        
        $rule->validate('tag', '', function () use (&$fails) {
            $fails = true;
        });
        
        expect($fails)->toBeTrue();
    });

    it('provides Persian error message', function () {
        $rule = new KavenegarTag();
        $message = '';
        
        $rule->validate('tag', 'invalid tag!', function ($msg) use (&$message) {
            $message = $msg;
        });
        
        expect($message)->toBeString();
        expect($message)->toContain('تگ');
    });
});
