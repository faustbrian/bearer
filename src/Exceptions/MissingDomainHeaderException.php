<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Bearer\Exceptions;

/**
 * Exception thrown when origin/referer headers are missing.
 *
 * This exception occurs when domain validation is required but no origin
 * or referer header is present in the request. Domain-restricted tokens
 * require these headers to validate the request's source domain.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class MissingDomainHeaderException extends DomainRestrictionException
{
    /**
     * Create an exception when origin/referer headers are missing.
     *
     * This occurs when domain validation is required but no origin
     * or referer header is present in the request.
     *
     * @return self Exception instance with descriptive error message
     */
    public static function missingHeader(): self
    {
        return new self('No origin or referer header present for domain validation.');
    }
}
