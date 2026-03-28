<?php declare(strict_types=1);

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
