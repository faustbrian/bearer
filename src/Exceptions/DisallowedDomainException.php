<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

use function sprintf;

/**
 * Exception thrown when a token is used from a disallowed domain.
 *
 * This exception occurs when the request's origin domain is not permitted by
 * the token's configuration. Unlike DomainNotAllowedException, this exception
 * does not include the list of allowed domains in the error message.
 */
final class DisallowedDomainException extends AbstractDomainRestrictionException
{
    /**
     * Create an exception for a disallowed domain without listing allowed
     * domains.
     *
     * This occurs when the request's origin domain is not permitted by the
     * token's configuration.
     *
     * @param  string $domain The domain that attempted to use the token
     * @return self   Exception instance with descriptive error message
     */
    public static function forDomain(string $domain): self
    {
        return new self(sprintf('Domain %s is not allowed for this token.', $domain));
    }
}
