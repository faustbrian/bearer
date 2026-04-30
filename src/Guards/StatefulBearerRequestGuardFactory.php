<?php declare(strict_types=1);

namespace Cline\Bearer\Guards;

use Cline\Bearer\BearerManager;
use Illuminate\Auth\AuthManager;
use Illuminate\Auth\RequestGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

/**
 * Builds stateful bearer request guards for Laravel auth drivers.
 *
 * This factory keeps session-aware bearer guard assembly outside the service
 * provider so auth-driver callbacks remain thin and binding-safe.
 *
 * @psalm-immutable
 */
final readonly class StatefulBearerRequestGuardFactory
{
    public function __construct(
        private BearerManager $bearerManager,
    ) {}

    /**
     * @param array<string, mixed> $config
     */
    public function make(AuthManager $auth, array $config, Request $request): RequestGuard
    {
        /** @var null|int $expiration */
        $expiration = Config::get('bearer.expiration');

        /** @var null|string $provider */
        $provider = $config['provider'] ?? null;

        return new RequestGuard(
            new StatefulBearerGuard(
                $auth,
                new BearerTokenAuthenticator(
                    $this->bearerManager,
                    $expiration,
                    $provider,
                ),
            ),
            $request,
            $auth->createUserProvider($provider),
        );
    }
}
