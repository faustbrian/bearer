<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

/**
 * Exception thrown when an unknown environment is encountered.
 *
 * This occurs when a token references an environment that is not recognized by
 * the system's environment configuration.
 */
final class UnknownEnvironmentException extends AbstractInvalidEnvironmentException
{
    /**
     * Create an exception for an unknown environment.
     *
     * This occurs when a token references an environment that is not recognized
     * by the system's environment configuration.
     *
     * @param  string $environment The unknown environment identifier
     * @return self   Exception instance with descriptive error message
     */
    public static function unknown(string $environment): self
    {
        return new self('Unknown environment: '.$environment);
    }
}
