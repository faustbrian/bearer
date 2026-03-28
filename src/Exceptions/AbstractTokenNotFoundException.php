<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

use RuntimeException;

/**
 * Base exception thrown when a requested token cannot be found.
 *
 * This exception occurs when attempting to retrieve a token by its identifier
 * (ID or prefix) but no matching token exists in the system. This can happen
 * when tokens are deleted, not yet created, or when using incorrect
 * identifiers.
 */
abstract class AbstractTokenNotFoundException extends RuntimeException implements BearerExceptionInterface
{
    // Base exception - use specific subclasses
}
