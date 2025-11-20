<?php

declare(strict_types=1);

namespace FardaDev\Kavenegar\Requests;

use FardaDev\Kavenegar\Enums\MessageTypeEnum;
use FardaDev\Kavenegar\Exceptions\InputValidationException;
use FardaDev\Kavenegar\Validation\Rules\KavenegarSenderLine;
use FardaDev\Kavenegar\Validation\Rules\KavenegarTag;
use FardaDev\Kavenegar\Validation\Rules\UnixTimestamp;
use Illuminate\Support\Facades\Validator;

final readonly class SendMessageRequest
{
    /**
     * @param  string|array<int, string>  $receptor
     * @param  array<int, int>|null  $localid
     */
    public function __construct(
        public string|array $receptor,
        public string $message,
        public ?string $sender = null,
        public ?int $date = null,
        public ?MessageTypeEnum $type = null,
        public ?array $localid = null,
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
            'receptor' => $this->receptor,
            'message' => $this->message,
            'sender' => $this->sender,
            'date' => $this->date,
            'hide' => $this->hide,
            'tag' => $this->tag,
        ], [
            'receptor' => ['required'],
            'message' => ['required', 'string', 'max:900'],
            'sender' => ['nullable', 'string', new KavenegarSenderLine()],
            'date' => ['nullable', 'integer', new UnixTimestamp(allowPast: false)],
            'hide' => ['nullable', 'integer', 'in:0,1'],
            'tag' => ['nullable', 'string', new KavenegarTag()],
        ]);

        if ($validator->fails()) {
            throw new InputValidationException($validator->errors());
        }

        // Custom validation for receptors (count and format)
        $this->validateReceptors();
    }

    private function validateReceptors(): void
    {
        $receptors = is_array($this->receptor) ? $this->receptor : explode(',', $this->receptor);

        if (count($receptors) > 200) {
            throw new InputValidationException(
                new \Illuminate\Support\MessageBag(['receptor' => ['تعداد گیرندگان نباید بیشتر از 200 باشد']])
            );
        }

        foreach ($receptors as $receptor) {
            $receptor = trim($receptor);
            if (! preg_match('/^09\d{9}$/', $receptor)) {
                throw new InputValidationException(
                    new \Illuminate\Support\MessageBag(['receptor' => ["شماره موبایل {$receptor} نامعتبر است"]])
                );
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function toApiParams(): array
    {
        $params = [
            'receptor' => is_array($this->receptor) ? implode(',', $this->receptor) : $this->receptor,
            'message' => $this->message,
        ];

        if ($this->sender !== null) {
            $params['sender'] = $this->sender;
        }

        if ($this->date !== null) {
            $params['date'] = $this->date;
        }

        if ($this->type !== null) {
            $params['type'] = $this->type->value;
        }

        if ($this->localid !== null) {
            $params['localid'] = implode(',', $this->localid);
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
