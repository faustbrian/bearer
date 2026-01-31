<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Bearer\Exceptions;

use function gettype;
use function sprintf;

/**
 * Exception thrown when a non-string value is assigned to a UUID primary key.
 *
 * UUID primary keys require string values in the canonical 8-4-4-4-12
 * hexadecimal format (e.g., "550e8400-e29b-41d4-a716-446655440000").
 * Non-string types cannot be properly validated or stored as UUIDs.
 *
 * @author Brian Faust <brian@cline.sh>
 */
final class NonStringUuidException extends InvalidPrimaryKeyValueException
{
    /**
     * Create exception for non-string value assigned to UUID primary key.
     *
     * UUID primary keys require string values in the canonical 8-4-4-4-12
     * hexadecimal format (e.g., "550e8400-e29b-41d4-a716-446655440000").
     * Non-string types cannot be properly validated or stored as UUIDs.
     *
     * @param  mixed $value The invalid value that was provided
     * @return self  Exception instance with descriptive error message
     */
    public static function nonStringUuid(mixed $value): self
    {
        return new self(
            sprintf(
                'Cannot assign non-string value to UUID primary key. Got: %s',
                gettype($value),
            ),
        );
    }
}
