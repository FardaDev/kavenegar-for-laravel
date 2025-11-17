<?php

declare(strict_types=1);

namespace FardaDev\Kavenegar\Exceptions;

/**
 * Exception thrown when Kavenegar API returns a non-200 status code.
 *
 * This includes errors such as:
 * - 401: Invalid API key
 * - 411: Invalid receptor number
 * - 412: Invalid sender number
 * - 413: Message too long or empty
 * - 414: Too many records
 * - 418: Insufficient credit
 */
class KavenegarApiException extends KavenegarException
{
    //
}
