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
 * Base exception for token generator registration errors.
 *
 * Token generators are responsible for creating unique token strings. This
 * base exception is extended by specific exceptions for different types of
 * token generator registration failures.
 *
 * @author Brian Faust <brian@cline.sh>
 */
abstract class TokenGeneratorNotRegisteredException extends InvalidArgumentException implements BearerException
{
    // Base exception class - specific exceptions extend this
}
