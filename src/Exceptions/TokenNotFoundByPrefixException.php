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
 * Exception thrown when a token cannot be found by its prefix.
 *
 * This occurs when searching for a token using its prefix (the public
 * identifier shown in plain text) but no token with that prefix exists.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class TokenNotFoundByPrefixException extends TokenNotFoundException
{
    /**
     * Create an exception for a token not found by its prefix.
     *
     * This occurs when searching for a token using its prefix (the public
     * identifier shown in plain text) but no token with that prefix exists.
     *
     * @param  string $prefix The token prefix that was not found
     * @return self   Exception instance with descriptive error message
     */
    public static function forPrefix(string $prefix): self
    {
        return new self(sprintf("Token with prefix '%s' not found.", $prefix));
    }
}
