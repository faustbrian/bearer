<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

/**
 * Exception thrown when no default audit driver is registered.
 *
 * This occurs when requesting the default driver but none has been set or
 * registered.
 */
final class NoDefaultAuditDriverException extends AbstractAuditDriverNotRegisteredException
{
    /**
     * Create an exception when no default driver is registered.
     *
     * This occurs when requesting the default driver but none has been set or
     * registered.
     *
     * @return self Exception instance with descriptive error message
     */
    public static function noDefault(): self
    {
        return new self('No default audit driver is registered.');
    }
}
