<?php declare(strict_types=1);

namespace Tests\Exceptions;

use RuntimeException;

final class DatabaseConnectionException extends RuntimeException
{
    public static function failed(): self
    {
        return new self('Database connection failed');
    }
}
