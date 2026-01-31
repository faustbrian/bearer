<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Bearer\Exceptions;

use DateTimeInterface;

/**
 * Exception thrown when a token was revoked at a specific time.
 *
 * This occurs when a token's revocation timestamp exists and has passed,
 * indicating that the token has been explicitly invalidated by an administrator
 * or automated revocation strategy.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class TokenRevokedAtException extends TokenRevokedException
{
    /**
     * Create an exception for a token that was revoked at a specific time.
     *
     * This occurs when a token's revocation timestamp exists and has passed,
     * indicating that the token has been explicitly invalidated by an administrator
     * or automated revocation strategy.
     *
     * @param  DateTimeInterface $revokedAt The timestamp when the token was revoked
     * @return self              Exception instance with descriptive error message
     */
    public static function at(DateTimeInterface $revokedAt): self
    {
        return new self('Token was revoked at '.$revokedAt->format('Y-m-d H:i:s'));
    }
}
