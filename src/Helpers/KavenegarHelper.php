<?php declare(strict_types=1);

namespace FardaDev\Kavenegar\Helpers;

use FardaDev\Kavenegar\Client\KavenegarClient;
use FardaDev\Kavenegar\Dto\MessageResponse;

class KavenegarHelper
{
    public function __construct(private readonly KavenegarClient $client) {}

    /**
     * Send login verification code using configured template.
     *
     * @param string $receptor Recipient phone number
     * @param string $code Verification code
     * @return MessageResponse|bool Returns MessageResponse or true if skipped in development
     */
    public function sendLoginCode(string $receptor, string $code): MessageResponse|bool
    {
        if ($this->shouldSkipInDevelopment($receptor)) {
            return true;
        }

        $template = config('kavenegar.templates.login');

        return $this->client->verifyLookup(
            receptor: $receptor,
            template: $template,
            token: $this->normalizeToken($code)
        );
    }

    /**
     * Send email + password verification code using configured template.
     *
     * @param string $receptor Recipient phone number
     * @param string $code Verification code
     * @param string $email Email address
     * @return MessageResponse|bool Returns MessageResponse or true if skipped in development
     */
    public function sendEmailPasswordCode(
        string $receptor,
        string $code,
        string $email
    ): MessageResponse|bool {
        if ($this->shouldSkipInDevelopment($receptor)) {
            return true;
        }

        $template = config('kavenegar.templates.email_password');

        return $this->client->verifyLookup(
            receptor: $receptor,
            template: $template,
            token: $this->normalizeToken($code),
            token2: $email
        );
    }

    /**
     * Send 2FA code with email using configured template.
     *
     * @param string $receptor Recipient phone number
     * @param string $code Verification code
     * @param string $email Email address
     * @return MessageResponse|bool Returns MessageResponse or true if skipped in development
     */
    public function sendTwoFactorCode(
        string $receptor,
        string $code,
        string $email
    ): MessageResponse|bool {
        if ($this->shouldSkipInDevelopment($receptor)) {
            return true;
        }

        $template = config('kavenegar.templates.two_factor');

        return $this->client->verifyLookup(
            receptor: $receptor,
            template: $template,
            token: $this->normalizeToken($code),
            token2: $email
        );
    }

    /**
     * Check if SMS sending should be skipped in development environment.
     *
     * @param string $receptor Recipient phone number
     * @return bool True if should skip, false otherwise
     */
    public function shouldSkipInDevelopment(string $receptor): bool
    {
        if (!config('kavenegar.skip_in_development')) {
            return false;
        }

        $environment = app()->environment();

        if (in_array($environment, ['local', 'dev', 'development'])) {
            return true;
        }

        if ($environment === 'testing' && $this->isTestPhoneNumber($receptor)) {
            return true;
        }

        return false;
    }

    /**
     * Normalize token by replacing whitespace with hyphens.
     *
     * @param string|null $token Token to normalize
     * @return string|null Normalized token
     */
    private function normalizeToken(?string $token): ?string
    {
        if ($token === null) {
            return null;
        }

        return preg_replace('/\s+/', '-', $token);
    }

    /**
     * Check if phone number is in test numbers list.
     *
     * @param string $number Phone number to check
     * @return bool True if test number, false otherwise
     */
    private function isTestPhoneNumber(string $number): bool
    {
        $testNumbers = config('kavenegar.test_phone_numbers', []);
        return in_array($number, $testNumbers, true);
    }
}
