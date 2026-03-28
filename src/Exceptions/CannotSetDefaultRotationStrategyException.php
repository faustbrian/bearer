<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

use function sprintf;

/**
 * Exception thrown when trying to set an unregistered strategy as default.
 *
 * This occurs when attempting to set a strategy as the default that has not
 * been registered in the registry.
 */
final class CannotSetDefaultRotationStrategyException extends AbstractRotationStrategyNotRegisteredException
{
    /**
     * Create an exception when trying to set an unregistered strategy as
     * default.
     *
     * This occurs when attempting to set a strategy as the default that has not
     * been registered in the registry.
     *
     * @param  string $name The name of the unregistered strategy
     * @return self   Exception instance with descriptive error message
     */
    public static function cannotSetAsDefault(string $name): self
    {
        return new self(sprintf('Cannot set unregistered rotation strategy "%s" as default.', $name));
    }
}
