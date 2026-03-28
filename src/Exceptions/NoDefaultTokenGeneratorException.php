<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

/**
 * Exception thrown when no default token generator is registered.
 *
 * This exception occurs when requesting the default token generator but none
 * has been set or registered in the token generator registry.
 */
final class NoDefaultTokenGeneratorException extends AbstractTokenGeneratorNotRegisteredException
{
    /**
     * Create an exception when no default generator is registered.
     *
     * This occurs when requesting the default generator but none has been set
     * or registered.
     *
     * @return self Exception instance with descriptive error message
     */
    public static function noDefault(): self
    {
        return new self('No default token generator is registered.');
    }
}
