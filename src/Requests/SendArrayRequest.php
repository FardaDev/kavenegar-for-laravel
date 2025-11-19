<?php

declare(strict_types=1);

namespace FardaDev\Kavenegar\Requests;

use FardaDev\Kavenegar\Enums\ApiErrorCodeEnum;
use FardaDev\Kavenegar\Enums\MessageTypeEnum;
use FardaDev\Kavenegar\Exceptions\KavenegarValidationException;
use FardaDev\Kavenegar\Validation\Rules\IranianMobileNumber;
use FardaDev\Kavenegar\Validation\Rules\KavenegarSenderLine;
use FardaDev\Kavenegar\Validation\Rules\KavenegarTag;
use FardaDev\Kavenegar\Validation\Rules\UnixTimestamp;
use Illuminate\Support\Facades\Validator;

final readonly class SendArrayRequest
{
    /**
     * @param  array<int, string>  $senders
     * @param  array<int, string>  $receptors
     * @param  array<int, string>  $messages
     * @param  array<int, MessageTypeEnum>|null  $types
     * @param  array<int, int>|null  $localids
     */
    public function __construct(
        public array $senders,
        public array $receptors,
        public array $messages,
        public ?int $date = null,
        public ?array $types = null,
        public ?array $localids = null,
        public ?int $hide = null,
        public ?string $tag = null,
        public ?string $policy = null,
    ) {
        $this->validate();
    }

    private function validate(): void
    {
        // Use Laravel Validator for cleaner validation
        $validator = Validator::make([
            'senders' => $this->senders,
            'receptors' => $this->receptors,
            'messages' => $this->messages,
            'date' => $this->date,
            'hide' => $this->hide,
            'tag' => $this->tag,
        ], [
            'senders' => ['required', 'array', 'min:1', 'max:200'],
            'senders.*' => ['required', 'string', new KavenegarSenderLine],
            'receptors' => ['required', 'array', 'min:1', 'max:200'],
            'receptors.*' => ['required', 'string', new IranianMobileNumber],
            'messages' => ['required', 'array', 'min:1', 'max:200'],
            'messages.*' => ['required', 'string', 'max:900'],
            'date' => ['nullable', 'integer', new UnixTimestamp(allowPast: false)],
            'hide' => ['nullable', 'integer', 'in:0,1'],
            'tag' => ['nullable', 'string', new KavenegarTag],
        ]);

        if ($validator->fails()) {
            // Map validation errors to appropriate error codes
            $errors = $validator->errors();
            $firstError = $errors->first();

            $errorCode = match (true) {
                $errors->has('senders') || $errors->has('senders.*') => ApiErrorCodeEnum::INVALID_SENDER->value,
                $errors->has('receptors') || $errors->has('receptors.*') => ApiErrorCodeEnum::INVALID_RECEPTOR->value,
                $errors->has('messages') || $errors->has('messages.*') => ApiErrorCodeEnum::INVALID_MESSAGE->value,
                $errors->has('date') => ApiErrorCodeEnum::INVALID_DATE->value,
                $errors->has('tag') => ApiErrorCodeEnum::INVALID_TAG->value,
                default => ApiErrorCodeEnum::INCOMPLETE_PARAMS->value,
            };

            throw new KavenegarValidationException(
                message: $firstError,
                errorCode: $errorCode,
                context: ['errors' => $errors->toArray()]
            );
        }

        // Validate array lengths match
        $this->validateArrayLengths();

        // Validate optional arrays if provided
        if ($this->types !== null) {
            $this->validateTypesArray();
        }

        if ($this->localids !== null) {
            $this->validateLocalidsArray();
        }
    }

    private function validateArrayLengths(): void
    {
        $lengths = [
            count($this->senders),
            count($this->receptors),
            count($this->messages),
        ];

        if (count(array_unique($lengths)) > 1) {
            throw new KavenegarValidationException(
                message: 'تعداد عناصر آرایه‌ها باید برابر باشد',
                errorCode: ApiErrorCodeEnum::ARRAY_LENGTH_MISMATCH->value,
                context: ['lengths' => $lengths]
            );
        }
    }

    private function validateTypesArray(): void
    {
        if (count($this->types) !== count($this->senders)) {
            throw new KavenegarValidationException(
                message: 'تعداد عناصر آرایه types باید با سایر آرایه‌ها برابر باشد',
                errorCode: ApiErrorCodeEnum::ARRAY_LENGTH_MISMATCH->value
            );
        }
    }

    private function validateLocalidsArray(): void
    {
        if (count($this->localids) !== count($this->senders)) {
            throw new KavenegarValidationException(
                message: 'تعداد عناصر آرایه localids باید با سایر آرایه‌ها برابر باشد',
                errorCode: ApiErrorCodeEnum::ARRAY_LENGTH_MISMATCH->value
            );
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function toApiParams(): array
    {
        $params = [
            'sender' => $this->senders,
            'receptor' => $this->receptors,
            'message' => $this->messages,
        ];

        if ($this->date !== null) {
            $params['date'] = $this->date;
        }

        if ($this->types !== null) {
            $params['type'] = array_map(fn (MessageTypeEnum $type) => $type->value, $this->types);
        }

        if ($this->localids !== null) {
            $params['localmessageids'] = $this->localids;
        }

        if ($this->hide !== null) {
            $params['hide'] = $this->hide;
        }

        if ($this->tag !== null) {
            $params['tag'] = $this->tag;
        }

        if ($this->policy !== null) {
            $params['policy'] = $this->policy;
        }

        return $params;
    }
}
