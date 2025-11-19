<?php

declare(strict_types=1);

namespace FardaDev\Kavenegar\Requests;

use FardaDev\Kavenegar\Enums\ApiErrorCodeEnum;
use FardaDev\Kavenegar\Exceptions\KavenegarValidationException;
use Illuminate\Support\Facades\Validator;

final readonly class LatestOutboxRequest
{
    public function __construct(
        public ?int $pagesize = null,
        public ?string $sender = null,
    ) {
        $this->validate();
    }

    private function validate(): void
    {
        $validator = Validator::make([
            'pagesize' => $this->pagesize,
            'sender' => $this->sender,
        ], [
            'pagesize' => ['nullable', 'integer', 'max:500'],
            'sender' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            throw new KavenegarValidationException(
                message: $validator->errors()->first(),
                errorCode: ApiErrorCodeEnum::TOO_MANY_RECORDS->value,
                context: ['errors' => $validator->errors()->toArray()]
            );
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function toApiParams(): array
    {
        $params = [];

        if ($this->pagesize !== null) {
            $params['pagesize'] = min($this->pagesize, 500);
        }

        if ($this->sender !== null) {
            $params['sender'] = $this->sender;
        }

        return $params;
    }
}
