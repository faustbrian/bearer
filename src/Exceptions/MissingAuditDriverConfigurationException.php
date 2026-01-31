<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Bearer\Exceptions;

use function sprintf;

/**
 * Exception thrown when an audit driver is not configured.
 *
 * This occurs when an audit driver is referenced but does not have a
 * corresponding entry in the audit drivers configuration array.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class MissingAuditDriverConfigurationException extends InvalidConfigurationException
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
