<?php

declare(strict_types=1);

namespace FardaDev\Kavenegar\Dto;

use FardaDev\Kavenegar\Enums\MessageStatusEnum;

readonly class StatusResponse
{
    public function __construct(
        public int $messageid,
        public MessageStatusEnum $status,
        public string $statustext
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            messageid: (int) $data['messageid'],
            status: MessageStatusEnum::from((int) $data['status']),
            statustext: (string) $data['statustext']
        );
    }

    /**
     * Check if message was successfully delivered to recipient.
     */
    public function isDelivered(): bool
    {
        return $this->status->isDelivered();
    }

    /**
     * Check if message failed to deliver.
     */
    public function isFailed(): bool
    {
        return $this->status->isFailed();
    }

    /**
     * Check if message is pending (in queue or sent to operator).
     */
    public function isPending(): bool
    {
        return $this->status->isPending();
    }
}
