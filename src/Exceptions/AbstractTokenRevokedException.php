<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

use RuntimeException;

/**
 * Abstract base exception for revoked token scenarios.
 *
 * Token revocation allows administrators to invalidate tokens before their
 * expiration date, typically in response to security concerns or access
 * changes. This exception occurs when a token that has been explicitly revoked
 * is used.
 */
abstract class AbstractTokenRevokedException extends RuntimeException implements BearerExceptionInterface
{
    // Abstract base class - see concrete implementations
}
