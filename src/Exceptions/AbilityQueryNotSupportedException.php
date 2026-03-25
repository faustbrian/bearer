<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Bearer\Exceptions;

use LogicException;

use function sprintf;

final class AbilityQueryNotSupportedException extends LogicException implements BearerExceptionInterface
{
    public static function forProvider(string $name): self
    {
        return new self(sprintf('Ability provider "%s" does not support query constraints.', $name));
    }
}
