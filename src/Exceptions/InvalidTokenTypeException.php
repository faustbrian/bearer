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
 * Base exception for all token type related errors.
 *
 * Token types define the behavior and characteristics of tokens in the system.
 * This abstract base exception is extended by specific token type exceptions
 * for different error scenarios (unknown types, unregistered types, disallowed types).
 *
 * @author Brian Faust <brian@cline.sh>
 */
abstract class InvalidTokenTypeException extends RuntimeException implements BearerException
{
    // Abstract base class - no methods required
}
