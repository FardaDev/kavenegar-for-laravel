<?php

declare(strict_types=1);

namespace FardaDev\Kavenegar\Requests;

use FardaDev\Kavenegar\Enums\MessageTypeEnum;
use FardaDev\Kavenegar\Exceptions\InputValidationException;
use FardaDev\Kavenegar\Validation\Rules\IranianMobileNumber;
use FardaDev\Kavenegar\Validation\Rules\VerifyToken;
use FardaDev\Kavenegar\Validation\Rules\VerifyToken10;
use FardaDev\Kavenegar\Validation\Rules\VerifyToken20;
use Illuminate\Support\Facades\Validator;

final readonly class VerifyLookupRequest
{
    public function __construct(
        public string $receptor,
        public string $template,
        public string $token,
        public ?string $token2 = null,
        public ?string $token3 = null,
        public ?string $token10 = null,
        public ?string $token20 = null,
        public ?MessageTypeEnum $type = null,
    ) {
        $this->validate();
    }

    private function validate(): void
    {
        // Use Laravel Validator for cleaner validation
        $validator = Validator::make([
            'receptor' => $this->receptor,
            'template' => $this->template,
            'token' => $this->token,
            'token2' => $this->token2,
            'token3' => $this->token3,
            'token10' => $this->token10,
            'token20' => $this->token20,
        ], [
            'receptor' => ['required', 'string', new IranianMobileNumber],
            'template' => ['required', 'string', 'max:100'],
            'token' => ['required', 'string', new VerifyToken],
            'token2' => ['nullable', 'string', new VerifyToken],
            'token3' => ['nullable', 'string', new VerifyToken],
            'token10' => ['nullable', 'string', new VerifyToken10],
            'token20' => ['nullable', 'string', new VerifyToken20],
        ]);

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
            'receptor' => $this->receptor,
            'template' => $this->template,
            'token' => $this->token,
        ];

        if ($this->token2 !== null) {
            $params['token2'] = $this->token2;
        }

        if ($this->token3 !== null) {
            $params['token3'] = $this->token3;
        }

        if ($this->token10 !== null) {
            $params['token10'] = $this->token10;
        }

        if ($this->token20 !== null) {
            $params['token20'] = $this->token20;
        }

        if ($this->type !== null) {
            $params['type'] = $this->type->value;
        }

        return $params;
    }
}
