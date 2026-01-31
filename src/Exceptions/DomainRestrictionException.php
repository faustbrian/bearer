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
 * Base exception for domain restriction violations.
 *
 * Domain restrictions limit token usage to specific domains or subdomains,
 * providing an additional security layer for web-based API access. This
 * exception occurs when a request originates from a domain that is not
 * included in the token's allowed domains list.
 *
 * @author Brian Faust <brian@cline.sh>
 */
abstract class DomainRestrictionException extends RuntimeException implements BearerException
{
    // Abstract base class - no factory methods
}
