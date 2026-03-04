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
 * Exception thrown when an IP address is not allowed for a token.
 *
 * This occurs when the request's source IP address is not permitted
 * by the token's configuration. This exception provides a simple error
 * message without listing allowed IPs.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class DisallowedIpException extends IpRestrictionException
{
    /**
     * Create an exception for a disallowed IP address without listing allowed IPs.
     *
     * This occurs when the request's source IP address is not permitted
     * by the token's configuration.
     *
     * @param  string $ip The IP address that attempted to use the token
     * @return self   Exception instance with descriptive error message
     */
    public static function forIp(string $ip): self
    {
        return new self(sprintf('IP address %s is not allowed for this token.', $ip));
    }
}
