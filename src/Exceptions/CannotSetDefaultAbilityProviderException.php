<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

use function sprintf;

final class CannotSetDefaultAbilityProviderException extends AbstractAbilityProviderNotRegisteredException
{
    public static function cannotSetAsDefault(string $name): self
    {
        return new self(sprintf('Cannot set unregistered ability provider "%s" as default.', $name));
    }
}
