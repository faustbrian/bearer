<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Bearer\Exceptions;

/**
 * Exception thrown when no default token hasher is registered.
 *
 * This occurs when attempting to use the default hasher but no default
 * has been configured in the bearer configuration.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class NoDefaultTokenHasherException extends TokenHasherNotRegisteredException
{
    /**
     * Create an exception for when no default hasher is set.
     *
     * This occurs when attempting to use the default hasher but no default
     * has been configured in the bearer configuration.
     *
     * @return self Exception instance with descriptive error message
     */
    public static function noDefault(): self
    {
        return new self('No default token hasher has been set.');
    }
}
