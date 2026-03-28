<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

/**
 * Exception thrown when the environments field has an invalid type.
 *
 * This occurs when a token type configuration has an environments field that is
 * not an array.
 */
final class InvalidEnvironmentsTypeConfigurationException extends AbstractInvalidConfigurationException
{
    /**
     * Create an exception for invalid environments field type.
     *
     * @return self Exception instance with descriptive error message
     */
    public static function create(): self
    {
        return new self('Token type "environments" must be an array.');
    }
}
