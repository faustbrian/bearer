<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

/**
 * Exception thrown when origin/referer headers are missing.
 *
 * This exception occurs when domain validation is required but no origin or
 * referer header is present in the request. Domain-restricted tokens require
 * these headers to validate the request's source domain.
 */
final class MissingDomainHeaderException extends AbstractDomainRestrictionException
{
    /**
     * Create an exception when origin/referer headers are missing.
     *
     * This occurs when domain validation is required but no origin or referer
     * header is present in the request.
     *
     * @return self Exception instance with descriptive error message
     */
    public static function missingHeader(): self
    {
        return new self('No origin or referer header present for domain validation.');
    }
}
