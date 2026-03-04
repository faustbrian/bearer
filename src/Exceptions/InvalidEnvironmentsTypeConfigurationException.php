<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Bearer\Exceptions;

/**
 * Exception thrown when the environments field has an invalid type.
 *
 * This occurs when a token type configuration has an environments
 * field that is not an array.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class InvalidEnvironmentsTypeConfigurationException extends InvalidConfigurationException
{
    /**
     * Create an exception for invalid environments field type.
     *
     * @return self Exception instance with descriptive error message
     */
    public static function create(): self
    {
        return new self('Token type "environments" must be an array.');
    }
}
