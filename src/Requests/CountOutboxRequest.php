<?php declare(strict_types=1);

namespace FardaDev\Kavenegar\Requests;

use FardaDev\Kavenegar\Enums\ApiErrorCodeEnum;
use FardaDev\Kavenegar\Enums\MessageStatusEnum;
use FardaDev\Kavenegar\Exceptions\KavenegarValidationException;
use FardaDev\Kavenegar\Validation\Rules\UnixTimestamp;
use Illuminate\Support\Facades\Validator;

readonly class CountOutboxRequest
{
    public function __construct(
        public int $startdate,
        public ?int $enddate = null,
        public ?MessageStatusEnum $status = null
    ) {
        $this->validate();
    }

    private function validate(): void
    {
        $validator = Validator::make(
            [
                'startdate' => $this->startdate,
                'enddate' => $this->enddate,
            ],
            [
                'startdate' => ['required', 'integer', new UnixTimestamp(allowPast: true, allowFuture: true)],
                'enddate' => [
                    'nullable',
                    'integer',
                    new UnixTimestamp(allowPast: true, allowFuture: true),
                    'gte:startdate',
                ],
            ],
            [
                'enddate.gte' => 'تاریخ پایان نمی‌تواند قبل از تاریخ شروع باشد',
            ]
        );

        if ($validator->fails()) {
            throw new KavenegarValidationException(
                message: implode("\n", $validator->errors()->all()),
                errorCode: ApiErrorCodeEnum::INVALID_DATE->value,
                context: ['errors' => $validator->errors()->toArray()]
            );
        }

        if ($this->enddate !== null && ($this->enddate - $this->startdate) > 86400) {
            throw new KavenegarValidationException(
                message: 'بازه زمانی نمی‌تواند بیشتر از یک روز باشد',
                errorCode: ApiErrorCodeEnum::INVALID_DATE->value,
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

        if ($this->status !== null) {
            $params['status'] = $this->status->value;
        }

        return $params;
    }
}
