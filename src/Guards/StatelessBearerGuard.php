<?php declare(strict_types=1);

namespace Cline\Bearer\Guards;

use Illuminate\Http\Request;

/**
 * Authentication guard for bearer-token-only transport surfaces.
 *
 * Unlike {@see BearerGuard}, this guard never falls back to configured stateful
 * session guards. It resolves authentication strictly from the request bearer
 * token, which makes it suitable for public API routes that must not silently
 * succeed via an existing web session.
 *
 * @psalm-immutable
 */
final readonly class StatelessBearerGuard
{
    public function __construct(
        private BearerTokenAuthenticator $authenticator,
    ) {}

    public function __invoke(Request $request): mixed
    {
        return $this->authenticator->authenticate($request);
    }
}
