<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Bearer\Exceptions;

use function implode;

/**
 * Exception thrown when any of several required token abilities is missing.
 *
 * This occurs when an action requires at least one of several abilities,
 * but the token does not have any of them in its abilities list.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class AnyAbilityMissingException extends MissingAbilityException
{
    /**
     * Create an exception for missing any of several abilities.
     *
     * This occurs when an action requires at least one of several abilities,
     * but the token does not have any of them in its abilities list.
     *
     * @param  array<string> $abilities The list of abilities, any of which would satisfy the requirement
     * @return self          Exception instance with descriptive error message
     */
    public static function missingAny(array $abilities): self
    {
        $abilitiesList = implode(', ', $abilities);

        return new self('Token is missing any of the required abilities: '.$abilitiesList);
    }
}
