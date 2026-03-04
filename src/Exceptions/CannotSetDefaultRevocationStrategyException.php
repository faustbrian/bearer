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
 * Exception thrown when attempting to set an unregistered strategy as default.
 *
 * This occurs when trying to set a revocation strategy as the default that
 * has not been registered in the revocation strategy registry.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class CannotSetDefaultRevocationStrategyException extends RevocationStrategyNotRegisteredException
{
    /**
     * Create an exception when trying to set an unregistered strategy as default.
     *
     * This occurs when attempting to set a strategy as the default that
     * has not been registered in the registry.
     *
     * @param  string $name The name of the unregistered strategy
     * @return self   Exception instance with descriptive error message
     */
    public static function cannotSetAsDefault(string $name): self
    {
        return new self(sprintf('Cannot set unregistered revocation strategy "%s" as default.', $name));
    }
}
