<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

/**
 * Exception thrown when the expiration field has an invalid type.
 *
 * This occurs when a token type configuration has an expiration field that is
 * not a positive integer or null.
 */
final class InvalidExpirationTypeConfigurationException extends AbstractInvalidConfigurationException
{
    /**
     * Create an exception for invalid expiration field type.
     *
     * @return self Exception instance with descriptive error message
     */
    public static function create(): self
    {
        return new self('Token type "expiration" must be a positive integer or null.');
    }
}
