<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

use function implode;
use function sprintf;

/**
 * Exception thrown when a disallowed environment is encountered.
 *
 * This occurs when a token is used in a valid environment, but that environment
 * is not included in the token's allowed environments list.
 */
final class EnvironmentNotAllowedException extends AbstractInvalidEnvironmentException
{
    /**
     * Create an exception for a disallowed environment.
     *
     * This occurs when a token is used in a valid environment, but that
     * environment is not included in the token's allowed environments list.
     *
     * @param  string        $environment The current environment identifier
     * @param  array<string> $allowed     List of allowed environment identifiers
     * @return self          Exception instance with descriptive error message
     */
    public static function notAllowed(string $environment, array $allowed): self
    {
        $allowedList = implode(', ', $allowed);

        return new self(sprintf("Environment '%s' is not allowed. Allowed environments: %s", $environment, $allowedList));
    }
}
