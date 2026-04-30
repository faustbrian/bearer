<?php declare(strict_types=1);

namespace Cline\Bearer\Guards;

use Cline\Bearer\Contracts\HasAccessTokensInterface;
use Cline\Bearer\TransientToken;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

use function assert;
use function config;
use function is_object;
use function is_string;

/**
 * Authentication guard that prefers configured stateful session guards before
 * falling back to bearer-token authentication.
 *
 * This is useful for first-party browser flows that want one guard to accept
 * both an existing session and an Authorization bearer token.
 *
 * @psalm-immutable
 */
final readonly class StatefulBearerGuard
{
    public function __construct(
        private AuthFactory $auth,
        private BearerTokenAuthenticator $authenticator,
    ) {}

    public function __invoke(Request $request): mixed
    {
        foreach (Arr::wrap(config('bearer.guard', 'web')) as $guard) {
            assert(is_string($guard));

            if ($user = $this->auth->guard($guard)->user()) {
                if ($this->supportsTokens($user)) {
                    assert($user instanceof HasAccessTokensInterface);

                    return $user->withAccessToken(
                        new TransientToken(),
                    );
                }

                return $user;
            }
        }

        return $this->authenticator->authenticate($request);
    }

    private function supportsTokens(mixed $owner = null): bool
    {
        if ($owner === null) {
            return false;
        }

        if (!is_object($owner)) {
            return false;
        }

        return $owner instanceof HasAccessTokensInterface;
    }
}
