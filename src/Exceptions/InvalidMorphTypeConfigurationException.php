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
 * Exception thrown when a morph type configuration is invalid.
 *
 * This occurs when a polymorphic relationship morph type is configured
 * with an invalid value or references a class that does not exist.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class InvalidMorphTypeConfigurationException extends InvalidConfigurationException
{
    /**
     * Create an exception for an invalid morph type configuration.
     *
     * @param  string $type The invalid morph type value
     * @return self   Exception instance with descriptive error message
     */
    public static function forType(string $type): self
    {
        return new self(sprintf("Invalid morph type '%s'. Ensure the class exists and is properly configured.", $type));
    }
}
