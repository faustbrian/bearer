<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Bearer\Exceptions;

use function implode;
use function sprintf;

/**
 * Exception thrown when a token type is not allowed for a request.
 *
 * This occurs when middleware validates that the current token's type
 * is not among the allowed types for an endpoint. Endpoints can restrict
 * access to specific token types (e.g., secret keys only, no publishable keys).
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class TokenTypeNotAllowedException extends InvalidTokenTypeException
{
    /**
     * Create an exception when a token type is not allowed for a request.
     *
     * This occurs when middleware validates that the current token's type
     * is not among the allowed types for an endpoint.
     *
     * @param  string        $currentType  The actual token type from the request
     * @param  array<string> $allowedTypes List of allowed token types
     * @return self          Exception instance with descriptive error message
     */
    public static function notAllowedForRequest(string $currentType, array $allowedTypes): self
    {
        $allowedList = implode(', ', $allowedTypes);

        return new self(sprintf("Token type '%s' is not allowed. Allowed types: %s", $currentType, $allowedList));
    }
}
