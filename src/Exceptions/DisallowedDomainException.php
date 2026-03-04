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
 * Exception thrown when a token is used from a disallowed domain.
 *
 * This exception occurs when the request's origin domain is not permitted
 * by the token's configuration. Unlike DomainNotAllowedException, this
 * exception does not include the list of allowed domains in the error message.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class DisallowedDomainException extends DomainRestrictionException
{
    /**
     * Create an exception for a disallowed domain without listing allowed domains.
     *
     * This occurs when the request's origin domain is not permitted
     * by the token's configuration.
     *
     * @param  string $domain The domain that attempted to use the token
     * @return self   Exception instance with descriptive error message
     */
    public static function forDomain(string $domain): self
    {
        return new self(sprintf('Domain %s is not allowed for this token.', $domain));
    }
}
