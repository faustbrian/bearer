<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

use function sprintf;

/**
 * Exception thrown when an audit driver is not found by name.
 *
 * This occurs when code references an audit driver by name that has not been
 * registered in the registry.
 */
final class AuditDriverNotFoundException extends AbstractAuditDriverNotRegisteredException
{
    /**
     * Create an exception for an unregistered audit driver.
     *
     * This occurs when code references an audit driver by name that has not
     * been registered in the registry.
     *
     * @param  string $name The name of the unregistered driver
     * @return self   Exception instance with descriptive error message
     */
    public static function forName(string $name): self
    {
        return new self(sprintf('Audit driver "%s" is not registered.', $name));
    }
}
