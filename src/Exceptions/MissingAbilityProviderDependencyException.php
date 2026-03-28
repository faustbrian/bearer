<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

use RuntimeException;

use function sprintf;

final class MissingAbilityProviderDependencyException extends RuntimeException implements BearerExceptionInterface
{
    public static function forPackage(string $provider, string $package): self
    {
        return new self(sprintf('Ability provider "%s" requires package "%s" to be installed.', $provider, $package));
    }
}
