<?php

declare(strict_types=1);

namespace FardaDev\Kavenegar\Requests;

use FardaDev\Kavenegar\Exceptions\InputValidationException;
use FardaDev\Kavenegar\Validation\Rules\DateRange;
use FardaDev\Kavenegar\Validation\Rules\KavenegarSenderLine;
use FardaDev\Kavenegar\Validation\Rules\UnixTimestamp;
use Illuminate\Support\Facades\Validator;

final readonly class SelectOutboxRequest
{
    public function __construct(
        public int $startdate,
        public ?int $enddate = null,
        public ?string $sender = null
    ) {
        $this->validate();
    }

    private function validate(): void
    {
        $validator = Validator::make(
            [
                'startdate' => $this->startdate,
                'enddate' => $this->enddate,
                'sender' => $this->sender,
            ],
            [
                'startdate' => ['required', 'integer', new UnixTimestamp(allowPast: true, allowFuture: true)],
                'enddate' => [
                    'nullable',
                    'integer',
                    new UnixTimestamp(allowPast: true, allowFuture: true),
                    'gte:startdate',
                    new DateRange(maxDays: 1),
                ],
                'sender' => ['nullable', 'string', new KavenegarSenderLine],
            ],
            [
                'enddate.gte' => 'تاریخ پایان نمی‌تواند قبل از تاریخ شروع باشد',
            ]
        );

        if ($validator->fails()) {
            throw new InputValidationException($validator->errors());
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function toApiParams(): array
    {
        $params = [
            'startdate' => $this->startdate,
        ];

        if ($this->enddate !== null) {
            $params['enddate'] = $this->enddate;
        }

        if ($this->sender !== null) {
            $params['sender'] = $this->sender;
        }

        return $params;
    }
}
