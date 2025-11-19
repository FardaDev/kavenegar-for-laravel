<?php

declare(strict_types=1);

namespace FardaDev\Kavenegar\Exceptions;

use Exception;
use Illuminate\Support\MessageBag;
use Throwable;

/**
 * Exception thrown when input validation fails before making API calls.
 *
 * This is for client-side validation errors (our validation, not Kavenegar API errors).
 * Includes Laravel validation errors for detailed feedback.
 *
 * Examples:
 * - Invalid phone number format
 * - Message too long
 * - Date range exceeds limits
 * - Array length mismatch
 */
class InputValidationException extends Exception
{
    public function __construct(
        public readonly MessageBag $errors,
        ?Throwable $previous = null
    ) {
        $message = implode("\n", $errors->all());
        parent::__construct($message, 0, $previous);
    }

    /**
     * Get all validation errors.
     */
    public function getErrors(): MessageBag
    {
        return $this->errors;
    }

    /**
     * Get validation errors as array.
     *
     * @return array<string, array<int, string>>
     */
    public function getErrorsArray(): array
    {
        return $this->errors->toArray();
    }
}

