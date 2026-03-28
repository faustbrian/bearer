<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

/**
 * Exception thrown when the abilities field has an invalid type.
 *
 * This occurs when a token type configuration has an abilities field that is
 * not an array.
 */
final class InvalidAbilitiesTypeConfigurationException extends AbstractInvalidConfigurationException
{
    /**
     * Create an exception for invalid abilities field type.
     *
     * @return self Exception instance with descriptive error message
     */
    public static function create(): self
    {
        return new self('Token type "abilities" must be an array.');
    }
}
