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
 * Base exception thrown when a token lacks an associated owner model.
 *
 * @author Brian Faust <brian@cline.sh>
 */
abstract class MissingTokenableException extends RuntimeException implements BearerException
{
    // Base exception class - concrete implementations provide specific contexts
}
