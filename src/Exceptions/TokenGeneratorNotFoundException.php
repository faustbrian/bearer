<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

use function sprintf;

/**
 * Exception thrown when an unregistered token generator is requested by name.
 *
 * This exception occurs when attempting to retrieve a token generator that has
 * not been registered in the token generator registry.
 */
final class TokenGeneratorNotFoundException extends AbstractTokenGeneratorNotRegisteredException
{
    /**
     * Create an exception for an unregistered token generator.
     *
     * This occurs when code references a token generator by name that has not
     * been registered in the registry.
     *
     * @param  string $name The name of the unregistered generator
     * @return self   Exception instance with descriptive error message
     */
    public static function forName(string $name): self
    {
        return new self(sprintf('Token generator "%s" is not registered.', $name));
    }
}
