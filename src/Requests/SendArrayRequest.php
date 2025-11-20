<?php

declare(strict_types=1);

namespace FardaDev\Kavenegar\Requests;

use FardaDev\Kavenegar\Enums\MessageTypeEnum;
use FardaDev\Kavenegar\Exceptions\InputValidationException;
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
            'senders.*' => ['required', 'string', new KavenegarSenderLine()],
            'receptors' => ['required', 'array', 'min:1', 'max:200'],
            'receptors.*' => ['required', 'string', new IranianMobileNumber()],
            'messages' => ['required', 'array', 'min:1', 'max:200'],
            'messages.*' => ['required', 'string', 'max:900'],
            'date' => ['nullable', 'integer', new UnixTimestamp(allowPast: false)],
            'hide' => ['nullable', 'integer', 'in:0,1'],
            'tag' => ['nullable', 'string', new KavenegarTag()],
        ]);

        if ($validator->fails()) {
            throw new InputValidationException($validator->errors());
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
            throw new InputValidationException(
                new \Illuminate\Support\MessageBag(['arrays' => ['تعداد عناصر آرایه‌ها باید برابر باشد']])
            );
        }
    }

    private function validateTypesArray(): void
    {
        if (count($this->types) !== count($this->senders)) {
            throw new InputValidationException(
                new \Illuminate\Support\MessageBag(['types' => ['تعداد عناصر آرایه types باید با سایر آرایه‌ها برابر باشد']])
            );
        }
    }

    private function validateLocalidsArray(): void
    {
        if (count($this->localids) !== count($this->senders)) {
            throw new InputValidationException(
                new \Illuminate\Support\MessageBag(['localids' => ['تعداد عناصر آرایه localids باید با سایر آرایه‌ها برابر باشد']])
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
