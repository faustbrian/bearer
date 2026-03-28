<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

use InvalidArgumentException;

/**
 * Exception thrown when a stateful domain configuration is invalid.
 *
 * The stateful domain configuration must contain string values to properly
 * match frontend request origins. Non-string values cannot be used for domain
 * pattern matching.
 */
final class InvalidStatefulDomainException extends InvalidArgumentException implements BearerExceptionInterface
{
    /**
     * Create an exception for a non-string domain value.
     *
     * @return self Exception instance with descriptive error message
     */
    public static function mustBeString(): self
    {
        return new self('Stateful domain must be a string');
    }
}
