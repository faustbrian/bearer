<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

/**
 * Exception thrown when the name field is missing or empty.
 *
 * This occurs when a token type configuration does not include a valid name
 * field.
 */
final class MissingNameConfigurationException extends AbstractInvalidConfigurationException
{
    /**
     * Create an exception for a missing or empty name field.
     *
     * @return self Exception instance with descriptive error message
     */
    public static function create(): self
    {
        return new self('Token type configuration must include a non-empty "name" field.');
    }
}
