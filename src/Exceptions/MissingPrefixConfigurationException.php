<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Bearer\Exceptions;

/**
 * Exception thrown when the prefix field is missing or empty.
 *
 * This occurs when a token type configuration does not include
 * a valid prefix field.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class MissingPrefixConfigurationException extends InvalidConfigurationException
{
    /**
     * Create an exception for a missing or empty prefix field.
     *
     * @return self Exception instance with descriptive error message
     */
    public static function create(): self
    {
        return new self('Token type configuration must include a non-empty "prefix" field.');
    }
}
