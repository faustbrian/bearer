<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

use RuntimeException;

/**
 * Base exception for IP restriction violations.
 *
 * IP restrictions limit token usage to specific IP addresses or CIDR ranges,
 * providing network-level access control for access tokens. This exception
 * occurs when a request originates from an IP address that is not included in
 * the token's allowed IPs list.
 */
abstract class AbstractIpRestrictionException extends RuntimeException implements BearerExceptionInterface
{
    // Base exception class - implementations provide specific factory methods
}
