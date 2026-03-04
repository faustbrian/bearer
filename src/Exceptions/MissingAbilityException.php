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
 * Base exception thrown when a required token ability is missing.
 *
 * Abilities define granular permissions that tokens can possess. This exception
 * occurs when code attempts to perform an action that requires specific abilities
 * that the current token does not have.
 *
 * @author Brian Faust <brian@cline.sh>
 */
abstract class MissingAbilityException extends RuntimeException implements BearerException {}
