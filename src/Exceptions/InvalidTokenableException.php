<?php declare(strict_types=1);

namespace Cline\Bearer\Exceptions;

use RuntimeException;

final class InvalidTokenableException extends RuntimeException implements BearerExceptionInterface
{
    public static function mustImplementHasAccessTokens(): self
    {
        return new self('Tokenable model must implement HasAccessTokensInterface');
    }
}
