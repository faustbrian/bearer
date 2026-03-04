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
 * Exception thrown when a token expired at a specific time.
 *
 * This occurs when the current timestamp exceeds the token's expiration
 * timestamp, indicating that the token's validity period has ended.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class TokenExpiredAtException extends TokenExpiredException
{
    /**
     * Create an exception for a token that expired at a specific time.
     *
     * This occurs when the current timestamp exceeds the token's expiration
     * timestamp, indicating that the token's validity period has ended.
     *
     * @param  DateTimeInterface $expiredAt The timestamp when the token expired
     * @return self              Exception instance with descriptive error message
     */
    public static function at(DateTimeInterface $expiredAt): self
    {
        return new self('Token expired at '.$expiredAt->format('Y-m-d H:i:s'));
    }
}
