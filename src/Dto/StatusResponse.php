<?php

declare(strict_types=1);

namespace FardaDev\Kavenegar\Dto;

use FardaDev\Kavenegar\Enums\ApiErrorCodeEnum;
use FardaDev\Kavenegar\Enums\MessageStatusEnum;
use FardaDev\Kavenegar\Exceptions\KavenegarApiException;
use Illuminate\Support\Facades\Validator;

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
        $validator = Validator::make($data, [
            'messageid' => ['required', 'integer'],
            'status' => ['required', 'integer'],
            'statustext' => ['required', 'string'],
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
            status: $status,
            statustext: (string) $data['statustext']
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
