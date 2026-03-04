<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Bearer\Exceptions;

/**
 * Exception thrown when the abilities field has an invalid type.
 *
 * This occurs when a token type configuration has an abilities
 * field that is not an array.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class InvalidAbilitiesTypeConfigurationException extends InvalidConfigurationException
{
    /**
     * Create an exception for invalid abilities field type.
     *
     * @return self Exception instance with descriptive error message
     */
    public static function create(): self
    {
        return new self('Token type "abilities" must be an array.');
    }
}
