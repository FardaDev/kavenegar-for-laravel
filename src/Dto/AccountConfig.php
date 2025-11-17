<?php

declare(strict_types=1);

namespace FardaDev\Kavenegar\Dto;

readonly class AccountConfig
{
    public function __construct(
        public string $apilogs,
        public string $dailyreport,
        public string $debugmode,
        public string $defaultsender,
        public int $mincreditalarm,
        public string $resendfailed
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            apilogs: (string) $data['apilogs'],
            dailyreport: (string) $data['dailyreport'],
            debugmode: (string) $data['debugmode'],
            defaultsender: (string) $data['defaultsender'],
            mincreditalarm: (int) $data['mincreditalarm'],
            resendfailed: (string) $data['resendfailed']
        );
    }

    /**
     * Check if API logs are enabled.
     */
    public function hasApiLogsEnabled(): bool
    {
        return $this->apilogs === 'enabled' || $this->apilogs === '1';
    }

    /**
     * Check if daily report is enabled.
     */
    public function hasDailyReportEnabled(): bool
    {
        return $this->dailyreport === 'enabled' || $this->dailyreport === '1';
    }

    /**
     * Check if debug mode is enabled.
     */
    public function hasDebugModeEnabled(): bool
    {
        return $this->debugmode === 'enabled' || $this->debugmode === '1';
    }

    /**
     * Check if resend failed messages is enabled.
     */
    public function hasResendFailedEnabled(): bool
    {
        return $this->resendfailed === 'enabled' || $this->resendfailed === '1';
    }
}
