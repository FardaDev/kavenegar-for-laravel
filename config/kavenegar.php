<?php declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Kavenegar API Key
    |--------------------------------------------------------------------------
    |
    | Your Kavenegar API key from https://panel.kavenegar.com/client/setting/account
    | This is required for all API operations.
    |
    */
    'api_key' => env('KAVENEGAR_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Default Sender
    |--------------------------------------------------------------------------
    |
    | Default sender line number. This will be used when no sender is specified
    | in the send methods. Can be overridden per message.
    |
    */
    'sender' => env('KAVENEGAR_SENDER', null),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | HTTP request timeout in seconds. Adjust based on your network conditions.
    |
    */
    'timeout' => env('KAVENEGAR_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Skip in Development
    |--------------------------------------------------------------------------
    |
    | When enabled, SMS sending will be skipped in local/dev environments.
    | Useful for testing without consuming credits.
    |
    */
    'skip_in_development' => env('KAVENEGAR_SKIP_IN_DEV', true),

    /*
    |--------------------------------------------------------------------------
    | Test Phone Numbers
    |--------------------------------------------------------------------------
    |
    | Phone numbers that should be treated as test numbers in testing environment.
    | SMS to these numbers will be skipped when skip_in_development is enabled.
    |
    */
    'test_phone_numbers' => [
        '09112223344',
    ],

    /*
    |--------------------------------------------------------------------------
    | Verification Templates
    |--------------------------------------------------------------------------
    |
    | Template names for common verification scenarios. These templates must be
    | created in your Kavenegar panel before use.
    |
    */
    'templates' => [
        'login' => env('KAVENEGAR_TEMPLATE_LOGIN', 'login-verify'),
        'email_password' => env('KAVENEGAR_TEMPLATE_EMAIL_PASS', 'email-pass'),
        'two_factor' => env('KAVENEGAR_TEMPLATE_2FA', 'email-2fa'),
    ],
];
