<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

use DateTimeInterface;

/**
 * Exception thrown when a token expired at a specific time.
 *
 * This occurs when the current timestamp exceeds the token's expiration
 * timestamp, indicating that the token's validity period has ended.
 */
final class TokenExpiredAtException extends AbstractTokenExpiredException
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
