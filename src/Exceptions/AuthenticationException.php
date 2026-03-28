<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

use RuntimeException;

/**
 * Exception thrown when authentication fails or is not present.
 *
 * This exception occurs when a protected endpoint is accessed without valid
 * authentication credentials or when the authentication token is missing.
 */
final class AuthenticationException extends RuntimeException implements BearerExceptionInterface
{
    /**
     * Create an exception for unauthenticated access.
     *
     * This occurs when a request is made without valid authentication
     * credentials or when no access token is present.
     *
     * @return self Exception instance with descriptive error message
     */
    public static function unauthenticated(): self
    {
        return new self('Unauthenticated.');
    }
}
