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
 * Exception thrown when an audit driver is not found by name.
 *
 * This occurs when code references an audit driver by name that
 * has not been registered in the registry.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class AuditDriverNotFoundException extends AuditDriverNotRegisteredException
{
    /**
     * Create an exception for an unregistered audit driver.
     *
     * This occurs when code references an audit driver by name that
     * has not been registered in the registry.
     *
     * @param  string $name The name of the unregistered driver
     * @return self   Exception instance with descriptive error message
     */
    public static function forName(string $name): self
    {
        return new self(sprintf('Audit driver "%s" is not registered.', $name));
    }
}
