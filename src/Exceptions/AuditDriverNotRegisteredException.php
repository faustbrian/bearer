<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Bearer\Exceptions;

use InvalidArgumentException;

/**
 * Base exception for audit driver registration issues.
 *
 * Audit drivers are responsible for logging token-related events. This
 * exception occurs when attempting to use a driver that has not been
 * registered in the audit driver registry.
 *
 * @author Brian Faust <brian@cline.sh>
 */
abstract class AuditDriverNotRegisteredException extends InvalidArgumentException implements BearerException
{
    // Abstract base class - use concrete implementations
}
