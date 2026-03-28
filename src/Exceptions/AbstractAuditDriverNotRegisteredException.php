<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

use InvalidArgumentException;

/**
 * Base exception for audit driver registration issues.
 *
 * Audit drivers are responsible for logging token-related events. This
 * exception occurs when attempting to use a driver that has not been registered
 * in the audit driver registry.
 */
abstract class AbstractAuditDriverNotRegisteredException extends InvalidArgumentException implements BearerExceptionInterface
{
    // Abstract base class - use concrete implementations
}
