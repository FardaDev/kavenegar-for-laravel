<?php declare(strict_types=1);

namespace FardaDev\Kavenegar\Requests;

use FardaDev\Kavenegar\Exceptions\InputValidationException;
use FardaDev\Kavenegar\Validation\Rules\IranianMobileNumber;
use FardaDev\Kavenegar\Validation\Rules\UnixTimestamp;
use Illuminate\Support\Facades\Validator;

readonly class StatusByReceptorRequest
{
    public function __construct(
        public string $receptor,
        public int $startdate,
        public ?int $enddate = null
    ) {
        $this->validate();
    }

    private function validate(): void
    {
        $validator = Validator::make(
            [
                'receptor' => $this->receptor,
                'startdate' => $this->startdate,
                'enddate' => $this->enddate,
            ],
            [
                'receptor' => ['required', 'string', new IranianMobileNumber()],
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
            throw new InputValidationException($validator->errors());
        }

        if ($this->enddate !== null && ($this->enddate - $this->startdate) > 86400) {
            throw new InputValidationException(
                new \Illuminate\Support\MessageBag(['enddate' => ['بازه زمانی نمی‌تواند بیشتر از یک روز باشد']])
            );
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function toApiParams(): array
    {
        $params = [
            'receptor' => $this->receptor,
            'startdate' => $this->startdate,
        ];

        if ($this->enddate !== null) {
            $params['enddate'] = $this->enddate;
        }

        return $params;
    }
}
