<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

use RuntimeException;

/**
 * Base exception thrown when a required token ability is missing.
 *
 * Abilities define granular permissions that tokens can possess. This exception
 * occurs when code attempts to perform an action that requires specific
 * abilities that the current token does not have.
 */
abstract class AbstractMissingAbilityException extends RuntimeException implements BearerExceptionInterface {}
