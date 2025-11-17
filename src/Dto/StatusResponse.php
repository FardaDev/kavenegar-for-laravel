<?php

declare(strict_types=1);

namespace FardaDev\Kavenegar\Dto;

readonly class StatusResponse
{
    public function __construct(
        public int $messageid,
        public int $status,
        public string $statustext
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            messageid: (int) $data['messageid'],
            status: (int) $data['status'],
            statustext: (string) $data['statustext']
        );
    }

    /**
     * Check if message was successfully delivered to recipient.
     * Status code 10 means "رسیده به گیرنده" (Delivered).
     */
    public function isDelivered(): bool
    {
        return $this->status === 10;
    }

    /**
     * Check if message failed to deliver.
     * Status codes 6, 11, 13, 14 indicate delivery failure.
     */
    public function isFailed(): bool
    {
        return in_array($this->status, [6, 11, 13, 14], true);
    }

    /**
     * Check if message is pending (in queue or sent to operator).
     * Status codes 1, 2, 4, 5 indicate pending states.
     */
    public function isPending(): bool
    {
        return in_array($this->status, [1, 2, 4, 5], true);
    }
}
