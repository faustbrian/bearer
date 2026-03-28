<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

/**
 * Exception thrown when a token has been revoked.
 *
 * This occurs when a token has been revoked but the exact revocation time is
 * not needed in the error message.
 */
final class TokenHasBeenRevokedException extends AbstractTokenRevokedException
{
    /**
     * Create an exception for a revoked token without timestamp details.
     *
     * This occurs when a token has been revoked but the exact revocation time
     * is not needed in the error message.
     *
     * @return self Exception instance with descriptive error message
     */
    public static function revoked(): self
    {
        return new self('This token has been revoked.');
    }
}
