<?php declare(strict_types=1);

namespace FardaDev\Kavenegar\Exceptions;

/**
 * Exception thrown for invalid input parameters before making API calls.
 *
 * This includes errors such as:
 * - Array length mismatch in sendArray
 * - Invalid phone number format
 * - Invalid tag format
 * - Missing required parameters
 */
class KavenegarValidationException extends KavenegarException
{
    //
}
