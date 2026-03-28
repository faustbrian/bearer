<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

/**
 * Exception thrown when the server_side_only field has an invalid type.
 *
 * This occurs when a token type configuration has a server_side_only field that
 * is not a boolean.
 */
final class InvalidServerSideOnlyTypeConfigurationException extends AbstractInvalidConfigurationException
{
    /**
     * Create an exception for invalid server_side_only field type.
     *
     * @return self Exception instance with descriptive error message
     */
    public static function create(): self
    {
        return new self('Token type "server_side_only" must be a boolean.');
    }
}
