<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Bearer\Exceptions;

/**
 * Exception thrown when a single required token ability is missing.
 *
 * This occurs when an action requires a specific ability and the
 * token does not have that ability in its abilities list.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class SingleAbilityMissingException extends MissingAbilityException
{
    /**
     * Create an exception for a single missing ability.
     *
     * This occurs when an action requires a specific ability and the
     * token does not have that ability in its abilities list.
     *
     * @param  string $ability The required ability that is missing
     * @return self   Exception instance with descriptive error message
     */
    public static function missing(string $ability): self
    {
        return new self('Token is missing required ability: '.$ability);
    }
}
