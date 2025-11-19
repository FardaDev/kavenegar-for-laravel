<?php declare(strict_types=1);

namespace FardaDev\Kavenegar\Requests;

use FardaDev\Kavenegar\Enums\ApiErrorCodeEnum;
use FardaDev\Kavenegar\Exceptions\KavenegarValidationException;
use FardaDev\Kavenegar\Validation\Rules\KavenegarSenderLine;
use FardaDev\Kavenegar\Validation\Rules\UnixTimestamp;
use Illuminate\Support\Facades\Validator;

readonly class SelectOutboxRequest
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
                ],
                'sender' => ['nullable', 'string', new KavenegarSenderLine()],
            ],
            [
                'enddate.gte' => 'تاریخ پایان نمی‌تواند قبل از تاریخ شروع باشد',
            ]
        );

        if ($validator->fails()) {
            throw new KavenegarValidationException(
                message: implode("\n", $validator->errors()->all()),
                errorCode: ApiErrorCodeEnum::INVALID_INPUT->value,
                context: ['errors' => $validator->errors()->toArray()]
            );
        }

        if ($this->enddate !== null && ($this->enddate - $this->startdate) > 86400) {
            throw new KavenegarValidationException(
                message: 'بازه زمانی نمی‌تواند بیشتر از یک روز باشد',
                errorCode: ApiErrorCodeEnum::INVALID_INPUT->value,
                context: ['startdate' => $this->startdate, 'enddate' => $this->enddate, 'max_range' => '1 day']
            );
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
