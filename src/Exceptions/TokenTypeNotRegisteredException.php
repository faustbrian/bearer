<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Bearer\Exceptions;

use function sprintf;

/**
 * Exception thrown when a token type has not been registered.
 *
 * This occurs when a token type exists conceptually but has not been
 * properly configured in the token types configuration array. The registry
 * does not contain an entry for the requested token type identifier.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class TokenTypeNotRegisteredException extends InvalidTokenTypeException
{
    /**
     * Create an exception for a token type that has not been registered.
     *
     * This occurs when a token type exists conceptually but has not been
     * properly configured in the token types configuration array.
     *
     * @param  string $type The unregistered token type identifier
     * @return self   Exception instance with descriptive error message
     */
    public static function notRegistered(string $type): self
    {
        return new self(sprintf("Token type '%s' is not registered in the configuration.", $type));
    }
}
