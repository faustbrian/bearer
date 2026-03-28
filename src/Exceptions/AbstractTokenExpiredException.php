<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

use RuntimeException;

/**
 * Base exception for expired token errors.
 *
 * Tokens can have expiration timestamps to enforce time-based access control.
 * This exception occurs when a token is used after its expiration date has
 * passed, preventing unauthorized continued access.
 */
abstract class AbstractTokenExpiredException extends RuntimeException implements BearerExceptionInterface
{
    // Abstract base class - use specific subclasses
}
