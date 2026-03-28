<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

/**
 * Exception thrown when no default revocation strategy is registered.
 *
 * This occurs when requesting the default revocation strategy but none has been
 * set or registered in the revocation strategy registry.
 */
final class NoDefaultRevocationStrategyException extends AbstractRevocationStrategyNotRegisteredException
{
    /**
     * Create an exception when no default strategy is registered.
     *
     * This occurs when requesting the default strategy but none has been set or
     * registered.
     *
     * @return self Exception instance with descriptive error message
     */
    public static function noDefault(): self
    {
        return new self('No default revocation strategy is registered.');
    }
}
