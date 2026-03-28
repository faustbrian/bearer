<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

use RuntimeException;

/**
 * Base exception for domain restriction violations.
 *
 * Domain restrictions limit token usage to specific domains or subdomains,
 * providing an additional security layer for web-based API access. This
 * exception occurs when a request originates from a domain that is not included
 * in the token's allowed domains list.
 */
abstract class AbstractDomainRestrictionException extends RuntimeException implements BearerExceptionInterface
{
    // Abstract base class - no factory methods
}
