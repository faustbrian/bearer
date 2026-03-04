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
 * Base exception thrown when a requested token cannot be found.
 *
 * This exception occurs when attempting to retrieve a token by its identifier
 * (ID or prefix) but no matching token exists in the system. This can happen
 * when tokens are deleted, not yet created, or when using incorrect identifiers.
 *
 * @author Brian Faust <brian@cline.sh>
 */
abstract class TokenNotFoundException extends RuntimeException implements BearerException
{
    // Base exception - use specific subclasses
}
