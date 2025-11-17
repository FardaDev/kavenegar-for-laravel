<?php

declare(strict_types=1);

namespace FardaDev\Kavenegar\Exceptions;

use Exception;
use Throwable;

abstract class KavenegarException extends Exception
{
    /**
     * @param  array<string, mixed>|null  $context
     */
    public function __construct(
        string $message,
        public readonly int $errorCode,
        public readonly ?array $context = null,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $errorCode, $previous);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getContext(): ?array
    {
        return $this->context;
    }
}
