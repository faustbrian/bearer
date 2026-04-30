<?php declare(strict_types=1);

namespace Cline\Bearer\Guards;

use Cline\Bearer\BearerManager;
use Cline\Bearer\Contracts\HasAccessTokensInterface;
use Cline\Bearer\Database\Models\AccessToken;
use Cline\Bearer\Events\TokenAuthenticated;
use Cline\Bearer\Exceptions\AbstractDomainRestrictionException;
use Cline\Bearer\Exceptions\AbstractIpRestrictionException;
use Cline\Bearer\Exceptions\DisallowedDomainException;
use Cline\Bearer\Exceptions\DisallowedIpException;
use Cline\Bearer\Exceptions\MissingDomainHeaderException;
use Cline\Bearer\Exceptions\TokenHasBeenRevokedException;
use Cline\Bearer\Exceptions\TokenHasExpiredException;
use Illuminate\Database\Connection;
use Illuminate\Http\Request;

use const PHP_URL_HOST;

use function assert;
use function config;
use function event;
use function in_array;
use function is_string;
use function now;
use function parse_url;
use function sprintf;

/**
 * Authenticates a bearer token from the incoming request.
 *
 * Shared by guards that need the same token lookup and validation semantics
 * without duplicating the revocation, expiry, provider, IP, and domain checks.
 *
 * @psalm-immutable
 */
final readonly class BearerTokenAuthenticator
{
    public function __construct(
        private BearerManager $bearer,
        private ?int $expiration = null,
        private ?string $provider = null,
    ) {}

    public function authenticate(Request $request): mixed
    {
        $token = $this->getAccessTokenFromRequest($request);

        if ($token === null) {
            return null;
        }

        $accessToken = $this->bearer->findAccessToken($token);

        if (!$this->isValidAccessToken($accessToken)) {
            return null;
        }

        assert($accessToken instanceof AccessToken);

        if (!$this->supportsTokens($accessToken->owner)) {
            return null;
        }

        try {
            $this->validateIpRestrictions($accessToken, $request);
            $this->validateDomainRestrictions($accessToken, $request);
        } catch (AbstractIpRestrictionException|AbstractDomainRestrictionException) {
            return null;
        }

        assert($accessToken->owner instanceof HasAccessTokensInterface);

        $owner = $accessToken->owner->withAccessToken($accessToken);

        event(
            new TokenAuthenticated(
                $accessToken,
                $request->ip(),
                $request->userAgent(),
            ),
        );

        $this->updateLastUsedAt($accessToken);

        return $owner;
    }

    private function getAccessTokenFromRequest(Request $request): ?string
    {
        return $request->bearerToken();
    }

    private function isValidAccessToken(?AccessToken $token): bool
    {
        if (!$token instanceof AccessToken) {
            return false;
        }

        if ($token->revoked_at !== null && !$token->revoked_at->isFuture()) {
            throw TokenHasBeenRevokedException::revoked();
        }

        if ($this->expiration !== null && $token->created_at->lt(now()->subMinutes($this->expiration))) {
            throw TokenHasExpiredException::expired();
        }

        if ($token->expires_at !== null && $token->expires_at->isPast()) {
            throw TokenHasExpiredException::expired();
        }

        return $this->hasValidProvider($token->owner);
    }

    private function validateIpRestrictions(AccessToken $token, Request $request): void
    {
        if ($token->allowed_ips === null || $token->allowed_ips === []) {
            return;
        }

        $requestIp = $request->ip();
        assert(is_string($requestIp));

        if (!in_array($requestIp, $token->allowed_ips, true)) {
            throw DisallowedIpException::forIp($requestIp);
        }
    }

    private function validateDomainRestrictions(AccessToken $token, Request $request): void
    {
        if ($token->allowed_domains === null || $token->allowed_domains === []) {
            return;
        }

        $domain = $request->headers->get('origin') ?? $request->headers->get('referer');

        if ($domain === null) {
            throw MissingDomainHeaderException::missingHeader();
        }

        $parsedDomain = parse_url($domain, PHP_URL_HOST);

        if ($parsedDomain === false || $parsedDomain === null) {
            throw MissingDomainHeaderException::missingHeader();
        }

        if (!in_array($parsedDomain, $token->allowed_domains, true)) {
            throw DisallowedDomainException::forDomain($parsedDomain);
        }
    }

    private function supportsTokens(mixed $owner = null): bool
    {
        return $owner instanceof HasAccessTokensInterface;
    }

    private function hasValidProvider(mixed $owner): bool
    {
        if ($this->provider === null) {
            return true;
        }

        $model = config(sprintf('auth.providers.%s.model', $this->provider));

        if (!is_string($model)) {
            return false;
        }

        return $owner instanceof $model;
    }

    private function updateLastUsedAt(AccessToken $accessToken): void
    {
        $connection = $accessToken->getConnection();

        if ($connection instanceof Connection) {
            $hasModifiedRecords = $connection->hasModifiedRecords();
            $accessToken->forceFill(['last_used_at' => now()])->save();
            $connection->setRecordModificationState($hasModifiedRecords);
        } else {
            $accessToken->forceFill(['last_used_at' => now()])->save();
        }
    }
}
