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
 * Base exception for invalid or disallowed environment errors.
 *
 * Environment restrictions limit token usage to specific deployment environments
 * (e.g., production, staging, development). This exception occurs when attempting
 * to use a token in an environment that is either unknown or not permitted by
 * the token's configuration.
 *
 * @author Brian Faust <brian@cline.sh>
 */
abstract class InvalidEnvironmentException extends RuntimeException implements BearerException
{
    // Abstract base class - see concrete implementations
}
