<?php

declare(strict_types=1);

namespace FardaDev\Kavenegar\Dto;

readonly class AccountInfo
{
    public function __construct(
        public int $remaincredit,
        public int $expiredate,
        public string $type
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            remaincredit: (int) $data['remaincredit'],
            expiredate: (int) $data['expiredate'],
            type: (string) $data['type']
        );
    }

    /**
     * Check if account has remaining credit.
     */
    public function hasCredit(): bool
    {
        return $this->remaincredit > 0;
    }

    /**
     * Check if account is expired.
     * Compares expiredate (UnixTime) with current time.
     */
    public function isExpired(): bool
    {
        return $this->expiredate < time();
    }

    /**
     * Get remaining credit in Rials.
     */
    public function getCreditAmount(): int
    {
        return $this->remaincredit;
    }

    /**
     * Get expiry date as DateTime object.
     */
    public function getExpiryDate(): \DateTime
    {
        $date = new \DateTime;
        $date->setTimestamp($this->expiredate);

        return $date;
    }
}
