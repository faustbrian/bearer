<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

use function sprintf;

final class AbilityProviderNotFoundException extends AbstractAbilityProviderNotRegisteredException
{
    public static function forName(string $name): self
    {
        return new self(sprintf('Ability provider "%s" is not registered.', $name));
    }
}
