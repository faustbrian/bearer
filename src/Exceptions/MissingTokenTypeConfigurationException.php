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
 * Exception thrown when a token type is not configured.
 *
 * This occurs when a token type is referenced but does not have a
 * corresponding entry in the token types configuration array.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class MissingTokenTypeConfigurationException extends InvalidConfigurationException
{
    /**
     * Create an exception for a missing token type configuration.
     *
     * @param  string $type The missing token type identifier
     * @return self   Exception instance with descriptive error message
     */
    public static function forType(string $type): self
    {
        return new self(sprintf("Token type '%s' is not configured. Add it to the 'token_types' configuration.", $type));
    }
}
