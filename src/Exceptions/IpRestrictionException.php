<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Bearer\Exceptions;

use RuntimeException;

/**
 * Base exception for IP restriction violations.
 *
 * IP restrictions limit token usage to specific IP addresses or CIDR ranges,
 * providing network-level access control for access tokens. This exception occurs
 * when a request originates from an IP address that is not included in the
 * token's allowed IPs list.
 *
 * @author Brian Faust <brian@cline.sh>
 */
abstract class IpRestrictionException extends RuntimeException implements BearerException
{
    // Base exception class - implementations provide specific factory methods
}
