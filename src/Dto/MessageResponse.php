<?php

declare(strict_types=1);

namespace FardaDev\Kavenegar\Dto;

use FardaDev\Kavenegar\Enums\ApiErrorCodeEnum;
use FardaDev\Kavenegar\Enums\MessageStatusEnum;
use FardaDev\Kavenegar\Exceptions\KavenegarApiException;
use Illuminate\Support\Facades\Validator;

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
        $validator = Validator::make($data, [
            'messageid' => ['required', 'integer'],
            'message' => ['required', 'string'],
            'status' => ['required', 'integer'],
            'statustext' => ['required', 'string'],
            'sender' => ['required', 'string'],
            'receptor' => ['required', 'string'],
            'date' => ['required', 'integer'],
            'cost' => ['required', 'integer'],
        ]);

        if ($validator->fails()) {
            throw new KavenegarApiException(
                message: 'Invalid API response structure: '.$validator->errors()->first(),
                errorCode: ApiErrorCodeEnum::OPERATION_FAILED->value,
                context: ['errors' => $validator->errors()->toArray(), 'data' => $data]
            );
        }

        try {
            $status = MessageStatusEnum::from((int) $data['status']);
        } catch (\ValueError $e) {
            $validStatuses = array_map(fn ($case) => $case->value, MessageStatusEnum::cases());

            throw new KavenegarApiException(
                message: "Unknown message status code received from API: {$data['status']}",
                errorCode: ApiErrorCodeEnum::OPERATION_FAILED->value,
                context: ['received_status' => $data['status'], 'valid_statuses' => $validStatuses]
            );
        }

        return new self(
            messageid: (int) $data['messageid'],
            message: (string) $data['message'],
            status: $status,
            statustext: (string) $data['statustext'],
            sender: (string) $data['sender'],
            receptor: (string) $data['receptor'],
            date: (int) $data['date'],
            cost: (int) $data['cost']
        );
    }

    public function isDelivered(): bool
    {
        return $this->status->isDelivered();
    }

    public function isFailed(): bool
    {
        return $this->status->isFailed();
    }

    public function isPending(): bool
    {
        return $this->status->isPending();
    }
}
