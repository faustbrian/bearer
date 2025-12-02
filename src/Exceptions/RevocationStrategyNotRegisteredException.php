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
 * Base exception for revocation strategy registration errors.
 *
 * Revocation strategies define how tokens are invalidated. This exception
 * serves as a base class for all exceptions related to revocation strategy
 * registration and retrieval from the registry.
 *
 * @author Brian Faust <brian@cline.sh>
 */
abstract class RevocationStrategyNotRegisteredException extends InvalidArgumentException implements BearerException
{
    // Abstract base class - see concrete implementations
}
