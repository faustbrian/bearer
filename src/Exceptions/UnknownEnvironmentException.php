<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Bearer\Exceptions;

/**
 * Exception thrown when an unknown environment is encountered.
 *
 * This occurs when a token references an environment that is not
 * recognized by the system's environment configuration.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class UnknownEnvironmentException extends InvalidEnvironmentException
{
    /**
     * Create an exception for an unknown environment.
     *
     * This occurs when a token references an environment that is not
     * recognized by the system's environment configuration.
     *
     * @param  string $environment The unknown environment identifier
     * @return self   Exception instance with descriptive error message
     */
    public static function unknown(string $environment): self
    {
        return new self('Unknown environment: '.$environment);
    }
}
