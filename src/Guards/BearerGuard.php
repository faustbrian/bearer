<?php declare(strict_types=1);

namespace Cline\Bearer\Guards;

use Illuminate\Http\Request;

/**
 * Authentication guard for Bearer token-based authentication.
 *
 * Authenticates strictly from the request bearer token. This is the package's
 * canonical API guard for transport surfaces that must not silently succeed via
 * an existing web session.
 *
 * @psalm-immutable
 */
final readonly class BearerGuard
{
    public function __construct(
        private BearerTokenAuthenticator $authenticator,
    ) {}

    public function __invoke(Request $request): mixed
    {
        return $this->authenticator->authenticate($request);
    }
}
