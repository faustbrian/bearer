<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

final class NoDefaultAbilityProviderException extends AbstractAbilityProviderNotRegisteredException
{
    public static function noDefault(): self
    {
        return new self('No default ability provider has been set.');
    }
}
