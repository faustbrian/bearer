<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

use InvalidArgumentException;

abstract class AbstractAbilityProviderNotRegisteredException extends InvalidArgumentException implements BearerExceptionInterface
{
    // Abstract base class - use concrete implementations.
}
