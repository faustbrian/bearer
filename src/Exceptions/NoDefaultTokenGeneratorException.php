<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Bearer\Exceptions;

/**
 * Exception thrown when no default token generator is registered.
 *
 * This exception occurs when requesting the default token generator but none
 * has been set or registered in the token generator registry.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class NoDefaultTokenGeneratorException extends TokenGeneratorNotRegisteredException
{
    /**
     * Create an exception when no default generator is registered.
     *
     * This occurs when requesting the default generator but none has
     * been set or registered.
     *
     * @return self Exception instance with descriptive error message
     */
    public static function noDefault(): self
    {
        return new self('No default token generator is registered.');
    }
}
