<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Bearer\Exceptions;

use RuntimeException;

/**
 * Abstract base exception for revoked token scenarios.
 *
 * Token revocation allows administrators to invalidate tokens before their
 * expiration date, typically in response to security concerns or access changes.
 * This exception occurs when a token that has been explicitly revoked is used.
 *
 * @author Brian Faust <brian@cline.sh>
 */
abstract class TokenRevokedException extends RuntimeException implements BearerException
{
    // Abstract base class - see concrete implementations
}
