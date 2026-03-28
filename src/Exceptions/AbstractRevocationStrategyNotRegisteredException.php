<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

use InvalidArgumentException;

/**
 * Base exception for revocation strategy registration errors.
 *
 * Revocation strategies define how tokens are invalidated. This exception
 * serves as a base class for all exceptions related to revocation strategy
 * registration and retrieval from the registry.
 */
abstract class AbstractRevocationStrategyNotRegisteredException extends InvalidArgumentException implements BearerExceptionInterface
{
    // Abstract base class - see concrete implementations
}
