<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

/**
 * Exception thrown when an unknown token type is encountered.
 *
 * This occurs when code references a token type that does not exist in the
 * system's token type definitions. The token type identifier is not recognized
 * by the system.
 */
final class UnknownTokenTypeException extends AbstractInvalidTokenTypeException
{
    /**
     * Create an exception for an unknown token type.
     *
     * This occurs when code references a token type that does not exist in the
     * system's token type definitions.
     *
     * @param  string $type The unknown token type identifier
     * @return self   Exception instance with descriptive error message
     */
    public static function unknown(string $type): self
    {
        return new self('Unknown token type: '.$type);
    }
}
