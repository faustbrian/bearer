<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Bearer\Exceptions;

/**
 * Exception thrown when the server_side_only field has an invalid type.
 *
 * This occurs when a token type configuration has a server_side_only
 * field that is not a boolean.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class InvalidServerSideOnlyTypeConfigurationException extends InvalidConfigurationException
{
    /**
     * Create an exception for invalid server_side_only field type.
     *
     * @return self Exception instance with descriptive error message
     */
    public static function create(): self
    {
        return new self('Token type "server_side_only" must be a boolean.');
    }
}
