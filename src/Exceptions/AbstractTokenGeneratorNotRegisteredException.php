<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

use InvalidArgumentException;

/**
 * Base exception for token generator registration errors.
 *
 * Token generators are responsible for creating unique token strings. This base
 * exception is extended by specific exceptions for different types of token
 * generator registration failures.
 */
abstract class AbstractTokenGeneratorNotRegisteredException extends InvalidArgumentException implements BearerExceptionInterface
{
    // Base exception class - specific exceptions extend this
}
