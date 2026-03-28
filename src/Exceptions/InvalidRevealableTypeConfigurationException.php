<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

/**
 * Exception thrown when the revealable field has an invalid type.
 *
 * This occurs when a token type configuration has a revealable field that is
 * not a boolean.
 */
final class InvalidRevealableTypeConfigurationException extends AbstractInvalidConfigurationException
{
    /**
     * Create an exception for invalid revealable field type.
     *
     * @return self Exception instance with descriptive error message
     */
    public static function create(): self
    {
        return new self('Token type "revealable" must be a boolean.');
    }
}
