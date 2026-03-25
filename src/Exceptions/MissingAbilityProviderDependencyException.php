<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
