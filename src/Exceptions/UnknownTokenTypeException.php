<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Bearer\Exceptions;

/**
 * Exception thrown when an unknown token type is encountered.
 *
 * This occurs when code references a token type that does not exist
 * in the system's token type definitions. The token type identifier
 * is not recognized by the system.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class UnknownTokenTypeException extends InvalidTokenTypeException
{
    /**
     * Create an exception for an unknown token type.
     *
     * This occurs when code references a token type that does not exist
     * in the system's token type definitions.
     *
     * @param  string $type The unknown token type identifier
     * @return self   Exception instance with descriptive error message
     */
    public static function unknown(string $type): self
    {
        return new self('Unknown token type: '.$type);
    }
}
