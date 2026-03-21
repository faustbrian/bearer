<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Bearer\Exceptions;

/**
 * Exception thrown when the revealable field has an invalid type.
 *
 * This occurs when a token type configuration has a revealable field that
 * is not a boolean.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class InvalidRevealableTypeConfigurationException extends InvalidConfigurationException
{
    /**
     * Create an exception for invalid revealable field type.
     *
     * @return self Exception instance with descriptive error message
     */
    public static function create(): self
    {
        return new self('Token type "revealable" must be a boolean.');
    }
}
