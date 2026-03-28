<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

use function sprintf;

/**
 * Exception thrown when a morph type configuration is invalid.
 *
 * This occurs when a polymorphic relationship morph type is configured with an
 * invalid value or references a class that does not exist.
 */
final class InvalidMorphTypeConfigurationException extends AbstractInvalidConfigurationException
{
    /**
     * Create an exception for an invalid morph type configuration.
     *
     * @param  string $type The invalid morph type value
     * @return self   Exception instance with descriptive error message
     */
    public static function forType(string $type): self
    {
        return new self(sprintf("Invalid morph type '%s'. Ensure the class exists and is properly configured.", $type));
    }
}
