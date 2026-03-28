<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

use RuntimeException;

/**
 * Base exception thrown when a token lacks an associated owner model.
 */
abstract class AbstractMissingTokenableException extends RuntimeException implements BearerExceptionInterface
{
    // Base exception class - concrete implementations provide specific contexts
}
