<?php

declare(strict_types=1);

namespace FardaDev\Kavenegar\Dto;

use FardaDev\Kavenegar\Enums\MessageStatusEnum;

readonly class MessageResponse
{
    public function __construct(
        public int $messageid,
        public string $message,
        public MessageStatusEnum $status,
        public string $statustext,
        public string $sender,
        public string $receptor,
        public int $date,
        public int $cost
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            messageid: (int) $data['messageid'],
            message: (string) $data['message'],
            status: MessageStatusEnum::from((int) $data['status']),
            statustext: (string) $data['statustext'],
            sender: (string) $data['sender'],
            receptor: (string) $data['receptor'],
            date: (int) $data['date'],
            cost: (int) $data['cost']
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
