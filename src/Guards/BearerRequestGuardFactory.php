<?php declare(strict_types=1);

namespace Cline\Bearer\Guards;

use Cline\Bearer\BearerManager;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

/**
 * Builds stateless bearer request guards for Laravel auth drivers.
 *
 * Keeping RequestGuard construction in a dedicated factory avoids coupling
 * auth-driver registration to service-provider callback binding behavior.
 *
 * @psalm-immutable
 */
final readonly class BearerRequestGuardFactory
{
    public function __construct(
        private BearerManager $bearerManager,
    ) {}

    /**
     * @param array<string, mixed> $config
     */
    public function make(AuthManager $auth, array $config, Request $request): RefreshingRequestGuard
    {
        /** @var null|int $expiration */
        $expiration = Config::get('bearer.expiration');

        /** @var null|string $provider */
        $provider = $config['provider'] ?? null;

        return new RefreshingRequestGuard(
            new BearerGuard(
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
