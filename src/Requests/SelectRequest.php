<?php declare(strict_types=1);

namespace FardaDev\Kavenegar\Requests;

use FardaDev\Kavenegar\Exceptions\InputValidationException;
use Illuminate\Support\Facades\Validator;

readonly class SelectRequest
{
    /**
     * @param  string|array<int, string>  $messageid
     */
    public function __construct(
        public string|array $messageid
    ) {
        $this->validate();
    }

    private function validate(): void
    {
        $messageids = is_array($this->messageid) ? $this->messageid : explode(',', $this->messageid);

        $validator = Validator::make(
            ['messageids' => $messageids],
            [
                'messageids' => ['required', 'array', 'max:500'],
                'messageids.*' => ['required', 'string'],
            ],
            [
                'messageids.max' => 'تعداد شناسه پیام‌ها نمی‌تواند بیشتر از 500 باشد',
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
        return [
            'messageid' => is_array($this->messageid) ? implode(',', $this->messageid) : $this->messageid,
        ];
    }
}
