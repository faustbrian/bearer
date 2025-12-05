<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Bearer\Exceptions;

/**
 * Exception thrown when no default rotation strategy is registered.
 *
 * This occurs when requesting the default strategy but none has
 * been set or registered.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class NoDefaultRotationStrategyException extends RotationStrategyNotRegisteredException
{
    /**
     * Create an exception when no default strategy is registered.
     *
     * This occurs when requesting the default strategy but none has
     * been set or registered.
     *
     * @return self Exception instance with descriptive error message
     */
    public static function noDefault(): self
    {
        return new self('No default rotation strategy is registered.');
    }
}
