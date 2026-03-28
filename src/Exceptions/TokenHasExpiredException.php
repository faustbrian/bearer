<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

/**
 * Exception thrown when a token has expired.
 *
 * This occurs when a token has expired but the exact expiration time is not
 * needed in the error message.
 */
final class TokenHasExpiredException extends AbstractTokenExpiredException
{
    /**
     * Create an exception for an expired token without timestamp details.
     *
     * This occurs when a token has expired but the exact expiration time is not
     * needed in the error message.
     *
     * @return self Exception instance with descriptive error message
     */
    public static function expired(): self
    {
        return new self('This token has expired.');
    }
}
