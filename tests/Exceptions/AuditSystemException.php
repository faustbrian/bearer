<?php declare(strict_types=1);

namespace Tests\Exceptions;

use Exception;

final class AuditSystemException extends Exception
{
    public static function systemDown(): self
    {
        return new self('Audit system is down');
    }
}
