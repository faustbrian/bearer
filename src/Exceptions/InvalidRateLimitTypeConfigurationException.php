<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Bearer\Exceptions;

/**
 * Exception thrown when the rate_limit field has an invalid type.
 *
 * This occurs when a token type configuration has a rate_limit
 * field that is not a positive integer or null.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class InvalidRateLimitTypeConfigurationException extends InvalidConfigurationException
{
    /**
     * Create an exception for invalid rate limit field type.
     *
     * @return self Exception instance with descriptive error message
     */
    public static function create(): self
    {
        return new self('Token type "rate_limit" must be a positive integer or null.');
    }
}
