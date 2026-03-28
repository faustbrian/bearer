<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

use function sprintf;

/**
 * Exception thrown when a token type has not been registered.
 *
 * This occurs when a token type exists conceptually but has not been properly
 * configured in the token types configuration array. The registry does not
 * contain an entry for the requested token type identifier.
 */
final class TokenTypeNotRegisteredException extends AbstractInvalidTokenTypeException
{
    /**
     * Create an exception for a token type that has not been registered.
     *
     * This occurs when a token type exists conceptually but has not been
     * properly configured in the token types configuration array.
     *
     * @param  string $type The unregistered token type identifier
     * @return self   Exception instance with descriptive error message
     */
    public static function notRegistered(string $type): self
    {
        return new self(sprintf("Token type '%s' is not registered in the configuration.", $type));
    }
}
