<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Bearer\Exceptions;

/**
 * Exception thrown when a token has been revoked.
 *
 * This occurs when a token has been revoked but the exact revocation
 * time is not needed in the error message.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class TokenHasBeenRevokedException extends TokenRevokedException
{
    /**
     * Create an exception for a revoked token without timestamp details.
     *
     * This occurs when a token has been revoked but the exact revocation
     * time is not needed in the error message.
     *
     * @return self Exception instance with descriptive error message
     */
    public static function revoked(): self
    {
        return new self('This token has been revoked.');
    }
}
