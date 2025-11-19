<?php

declare(strict_types=1);

namespace FardaDev\Kavenegar\Dto;

use FardaDev\Kavenegar\Enums\ApiErrorCodeEnum;
use FardaDev\Kavenegar\Exceptions\KavenegarApiException;
use Illuminate\Support\Facades\Validator;

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
        $validator = Validator::make($data, [
            'remaincredit' => ['required', 'integer', 'min:0'],
            'expiredate' => ['required', 'integer'],
            'type' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            throw new KavenegarApiException(
                message: 'Invalid API response structure: '.$validator->errors()->first(),
                errorCode: ApiErrorCodeEnum::OPERATION_FAILED->value,
                context: ['errors' => $validator->errors()->toArray(), 'data' => $data]
            );
        }

        return new self(
            remaincredit: (int) $data['remaincredit'],
            expiredate: (int) $data['expiredate'],
            type: (string) $data['type']
        );
    }

    public function hasCredit(): bool
    {
        return $this->remaincredit > 0;
    }

    public function isExpired(): bool
    {
        return $this->expiredate < time();
    }

    public function getCreditAmount(): int
    {
        return $this->remaincredit;
    }

    public function getExpiryDate(): \DateTime
    {
        $date = new \DateTime;
        $date->setTimestamp($this->expiredate);

        return $date;
    }
}
