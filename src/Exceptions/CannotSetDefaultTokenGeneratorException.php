<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Bearer\Exceptions;

use function sprintf;

/**
 * Exception thrown when trying to set an unregistered generator as default.
 *
 * This exception occurs when attempting to set a token generator as the
 * default that has not been registered in the token generator registry.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class CannotSetDefaultTokenGeneratorException extends TokenGeneratorNotRegisteredException
{
    /**
     * Create an exception when trying to set an unregistered generator as default.
     *
     * This occurs when attempting to set a generator as the default that
     * has not been registered in the registry.
     *
     * @param  string $name The name of the unregistered generator
     * @return self   Exception instance with descriptive error message
     */
    public static function cannotSetAsDefault(string $name): self
    {
        return new self(sprintf('Cannot set unregistered generator "%s" as default.', $name));
    }
}
