<?php

declare(strict_types=1);

namespace FardaDev\Kavenegar\Exceptions;

/**
 * Exception thrown for network and HTTP connection errors.
 *
 * This includes errors such as:
 * - Connection timeout
 * - DNS resolution failure
 * - Connection refused
 * - SSL/TLS errors
 */
class KavenegarHttpException extends KavenegarException
{
    //
}
