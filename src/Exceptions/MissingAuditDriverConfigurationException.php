<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

use function sprintf;

/**
 * Exception thrown when an audit driver is not configured.
 *
 * This occurs when an audit driver is referenced but does not have a
 * corresponding entry in the audit drivers configuration array.
 */
final class MissingAuditDriverConfigurationException extends AbstractInvalidConfigurationException
{
    /**
     * Create an exception for a missing audit driver configuration.
     *
     * @param  string $driver The missing audit driver identifier
     * @return self   Exception instance with descriptive error message
     */
    public static function forDriver(string $driver): self
    {
        return new self(sprintf("Audit driver '%s' is not configured. Add it to the 'audit_drivers' configuration.", $driver));
    }
}
