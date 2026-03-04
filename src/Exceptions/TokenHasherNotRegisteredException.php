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
 * Base exception for token hasher registration errors.
 *
 * Token hashers are responsible for securely hashing and verifying token values.
 * This exception occurs when attempting to use a hasher that hasn't been registered
 * with the BearerManager, or when no default hasher is configured.
 *
 * @author Brian Faust <brian@cline.sh>
 */
abstract class TokenHasherNotRegisteredException extends InvalidArgumentException implements BearerException
{
    // Abstract base class - use concrete implementations
}
