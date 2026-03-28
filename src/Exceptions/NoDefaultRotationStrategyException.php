<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

/**
 * Exception thrown when no default rotation strategy is registered.
 *
 * This occurs when requesting the default strategy but none has been set or
 * registered.
 */
final class NoDefaultRotationStrategyException extends AbstractRotationStrategyNotRegisteredException
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
        return new self('No default rotation strategy is registered.');
    }
}
