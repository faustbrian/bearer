<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Bearer\Exceptions;

/**
 * Exception thrown when a parent token lacks an associated owner model.
 *
 * During token derivation, the parent token must have a valid owner relationship
 * that implements the HasAccessTokens contract. This exception occurs when the
 * parent token's owner is null or does not implement the required interface.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class MissingTokenableForParentException extends MissingTokenableException
{
    /**
     * Create an exception for a parent token without an owner model.
     *
     * This occurs when attempting to derive a new token from a parent token
     * that has no associated owner model, which is required to perform the
     * derivation operation.
     *
     * @return self Exception instance with descriptive error message
     */
    public static function forParentToken(): self
    {
        return new self('Parent token has no owner model');
    }
}
