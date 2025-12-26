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
 * Exception thrown when a token is used from a domain not in the allowed list.
 *
 * This exception occurs when the request's origin domain does not match any of
 * the domains explicitly permitted by the token's configuration. The exception
 * message includes both the attempted domain and the full list of allowed domains.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class DomainNotAllowedException extends DomainRestrictionException
{
    /**
     * Create an exception for a disallowed domain.
     *
     * This occurs when the request's origin domain does not match any of
     * the domains explicitly permitted by the token's configuration.
     *
     * @param  string        $domain         The domain that attempted to use the token
     * @param  array<string> $allowedDomains List of permitted domains for this token
     * @return self          Exception instance with descriptive error message
     */
    public static function notAllowed(string $domain, array $allowedDomains): self
    {
        $allowedList = implode(', ', $allowedDomains);

        return new self(sprintf("Domain '%s' is not allowed. Allowed domains: %s", $domain, $allowedList));
    }
}
