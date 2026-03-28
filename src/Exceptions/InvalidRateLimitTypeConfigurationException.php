<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

/**
 * Exception thrown when the rate_limit field has an invalid type.
 *
 * This occurs when a token type configuration has a rate_limit field that is
 * not a positive integer or null.
 */
final class InvalidRateLimitTypeConfigurationException extends AbstractInvalidConfigurationException
{
    /**
     * Create an exception for invalid rate limit field type.
     *
     * @return self Exception instance with descriptive error message
     */
    public static function create(): self
    {
        return new self('Token type "rate_limit" must be a positive integer or null.');
    }
}
