<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

use RuntimeException;

/**
 * Base exception for all token type related errors.
 *
 * Token types define the behavior and characteristics of tokens in the system.
 * This abstract base exception is extended by specific token type exceptions
 * for different error scenarios (unknown types, unregistered types, disallowed
 * types).
 */
abstract class AbstractInvalidTokenTypeException extends RuntimeException implements BearerExceptionInterface
{
    // Abstract base class - no methods required
}
