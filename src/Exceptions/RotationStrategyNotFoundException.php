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
 * Exception thrown when an unregistered rotation strategy is requested.
 *
 * This occurs when code references a rotation strategy by name that
 * has not been registered in the registry.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class RotationStrategyNotFoundException extends RotationStrategyNotRegisteredException
{
    /**
     * Create an exception for an unregistered rotation strategy.
     *
     * This occurs when code references a rotation strategy by name that
     * has not been registered in the registry.
     *
     * @param  string $name The name of the unregistered strategy
     * @return self   Exception instance with descriptive error message
     */
    public static function forName(string $name): self
    {
        return new self(sprintf('Rotation strategy "%s" is not registered.', $name));
    }
}
