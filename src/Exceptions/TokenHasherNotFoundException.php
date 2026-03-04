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
 * Exception thrown when a named token hasher is not registered.
 *
 * This occurs when attempting to retrieve or use a token hasher by name
 * that hasn't been registered via registerTokenHasher() on the BearerManager.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class TokenHasherNotFoundException extends TokenHasherNotRegisteredException
{
    /**
     * Create an exception for an unregistered hasher.
     *
     * This occurs when attempting to retrieve or use a token hasher by name
     * that hasn't been registered via registerTokenHasher() on the BearerManager.
     *
     * @param  string $name The hasher name that was not found
     * @return self   Exception instance with descriptive error message
     */
    public static function forHasher(string $name): self
    {
        return new self(sprintf("Token hasher '%s' is not registered.", $name));
    }
}
