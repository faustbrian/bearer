<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

use InvalidArgumentException;

/**
 * Base exception for rotation strategy registration issues.
 *
 * Rotation strategies define how tokens are renewed. This exception occurs when
 * attempting to use a strategy that has not been registered in the rotation
 * strategy registry.
 */
abstract class AbstractRotationStrategyNotRegisteredException extends InvalidArgumentException implements BearerExceptionInterface
{
    // Base exception class - see concrete implementations
}
