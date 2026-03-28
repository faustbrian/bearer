<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

use RuntimeException;

/**
 * Base exception for invalid or disallowed environment errors.
 *
 * Environment restrictions limit token usage to specific deployment
 * environments (e.g., production, staging, development). This exception occurs
 * when attempting to use a token in an environment that is either unknown or
 * not permitted by the token's configuration.
 */
abstract class AbstractInvalidEnvironmentException extends RuntimeException implements BearerExceptionInterface
{
    // Abstract base class - see concrete implementations
}
