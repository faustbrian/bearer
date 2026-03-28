<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

/**
 * Exception thrown when no default token hasher is registered.
 *
 * This occurs when attempting to use the default hasher but no default has been
 * configured in the bearer configuration.
 */
final class NoDefaultTokenHasherException extends AbstractTokenHasherNotRegisteredException
{
    /**
     * Create an exception for when no default hasher is set.
     *
     * This occurs when attempting to use the default hasher but no default has
     * been configured in the bearer configuration.
     *
     * @return self Exception instance with descriptive error message
     */
    public static function noDefault(): self
    {
        return new self('No default token hasher has been set.');
    }
}
