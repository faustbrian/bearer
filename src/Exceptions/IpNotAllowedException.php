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
 * Exception thrown when an IP address is not in the allowed list.
 *
 * This occurs when the request's source IP address does not match any of
 * the IP addresses or CIDR ranges explicitly permitted by the token's
 * configuration. The exception includes the list of allowed IPs for debugging.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class IpNotAllowedException extends IpRestrictionException
{
    /**
     * Create an exception for a disallowed IP address.
     *
     * This occurs when the request's source IP address does not match any of
     * the IP addresses or CIDR ranges explicitly permitted by the token's
     * configuration.
     *
     * @param  string        $ip         The IP address that attempted to use the token
     * @param  array<string> $allowedIps List of permitted IP addresses/ranges for this token
     * @return self          Exception instance with descriptive error message
     */
    public static function notAllowed(string $ip, array $allowedIps): self
    {
        $allowedList = implode(', ', $allowedIps);

        return new self(sprintf("IP address '%s' is not allowed. Allowed IPs: %s", $ip, $allowedList));
    }
}
