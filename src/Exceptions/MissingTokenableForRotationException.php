<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

/**
 * Exception thrown when a token lacks an associated owner model during
 * rotation.
 *
 * During token rotation, the token must have a valid owner relationship that
 * implements the HasAccessTokensInterface contract. This exception occurs when
 * the owner is null or does not implement the required interface.
 */
final class MissingTokenableForRotationException extends AbstractMissingTokenableException
{
    /**
     * Create an exception for a token without a valid owner model during
     * rotation.
     *
     * This occurs when attempting to rotate a token that has no associated
     * owner model, which is required to perform the rotation operation.
     *
     * @return self Exception instance with descriptive error message
     */
    public static function forRotation(): self
    {
        return new self('Token has no associated owner model');
    }
}
