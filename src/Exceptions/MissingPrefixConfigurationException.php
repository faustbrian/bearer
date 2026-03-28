<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

/**
 * Exception thrown when the prefix field is missing or empty.
 *
 * This occurs when a token type configuration does not include a valid prefix
 * field.
 */
final class MissingPrefixConfigurationException extends AbstractInvalidConfigurationException
{
    /**
     * Create an exception for a missing or empty prefix field.
     *
     * @return self Exception instance with descriptive error message
     */
    public static function create(): self
    {
        return new self('Token type configuration must include a non-empty "prefix" field.');
    }
}
