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
 * Base exception for expired token errors.
 *
 * Tokens can have expiration timestamps to enforce time-based access control.
 * This exception occurs when a token is used after its expiration date has
 * passed, preventing unauthorized continued access.
 *
 * @author Brian Faust <brian@cline.sh>
 */
abstract class TokenExpiredException extends RuntimeException implements BearerException
{
    // Abstract base class - use specific subclasses
}
