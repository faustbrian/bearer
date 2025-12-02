<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Bearer\Exceptions;

use InvalidArgumentException;

/**
 * Base exception for rotation strategy registration issues.
 *
 * Rotation strategies define how tokens are renewed. This exception
 * occurs when attempting to use a strategy that has not been registered
 * in the rotation strategy registry.
 *
 * @author Brian Faust <brian@cline.sh>
 */
abstract class RotationStrategyNotRegisteredException extends InvalidArgumentException implements BearerException
{
    // Base exception class - see concrete implementations
}
