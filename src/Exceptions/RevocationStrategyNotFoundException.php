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
 * Exception thrown when a revocation strategy is not found by name.
 *
 * This occurs when code references a revocation strategy by name that
 * has not been registered in the revocation strategy registry.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class RevocationStrategyNotFoundException extends RevocationStrategyNotRegisteredException
{
    /**
     * Create an exception for an unregistered revocation strategy.
     *
     * This occurs when code references a revocation strategy by name that
     * has not been registered in the registry.
     *
     * @param  string $name The name of the unregistered strategy
     * @return self   Exception instance with descriptive error message
     */
    public static function forName(string $name): self
    {
        return new self(sprintf('Revocation strategy "%s" is not registered.', $name));
    }
}
