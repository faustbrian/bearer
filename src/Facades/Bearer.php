<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cline\Bearer\Facades;

use Cline\Bearer\BearerManager;
use Cline\Bearer\Conductors\TokenIssuanceConductor;
use Cline\Bearer\Contracts\AuditDriverInterface;
use Cline\Bearer\Contracts\RevocationStrategyInterface;
use Cline\Bearer\Contracts\RotationStrategyInterface;
use Cline\Bearer\Contracts\TokenGeneratorInterface;
use Cline\Bearer\Contracts\TokenHasherInterface;
use Cline\Bearer\Contracts\TokenTypeInterface;
use Cline\Bearer\Database\Models\AccessToken;
use Cline\Bearer\NewAccessToken;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;

/**
 * Laravel facade for Bearer token authentication manager.
 *
 * Provides static access to the BearerManager for managing personal access tokens,
 * token types, hashers, rotation strategies, and audit logging. This facade simplifies
 * token operations throughout your application.
 *
 * ```php
 * // Issue a new token
 * $token = Bearer::for($user)->create('Mobile App', ['posts:read']);
 *
 * // Find and validate tokens
 * $accessToken = Bearer::findAccessToken($token);
 *
 * // Revoke tokens
 * Bearer::revoke($accessToken);
 * ```
 *
 * @method static string                      accessTokenModel()
 * @method static Authenticatable             actingAs(Authenticatable $user, array<int, string> $abilities = [], ?string $type = null, string $guard = 'bearer')
 * @method static AuditDriverInterface        auditDriver(?string $name = null)
 * @method static void                        authenticateAccessTokensUsing(?\Closure $callback)
 * @method static AccessToken|null            findAccessToken(string $token)
 * @method static AccessToken|null            findByPrefix(string $prefix)
 * @method static TokenIssuanceConductor      for(Model $owner)
 * @method static void                        getAccessTokenFromRequestUsing(?\Closure $callback)
 * @method static void                        registerAuditDriver(string $name, AuditDriverInterface $driver)
 * @method static void                        registerRevocationStrategy(string $name, RevocationStrategyInterface $strategy)
 * @method static void                        registerRotationStrategy(string $name, RotationStrategyInterface $strategy)
 * @method static void                        registerTokenGenerator(string $name, TokenGeneratorInterface $generator)
 * @method static void                        registerTokenHasher(string $name, TokenHasherInterface $hasher)
 * @method static void                        registerTokenType(string $key, TokenTypeInterface $type)
 * @method static RevocationStrategyInterface revocationStrategy(?string $name = null)
 * @method static void                        revoke(AccessToken $token, ?string $strategy = null)
 * @method static NewAccessToken              rotate(AccessToken $token, ?string $strategy = null)
 * @method static RotationStrategyInterface   rotationStrategy(?string $name = null)
 * @method static TokenGeneratorInterface     tokenGenerator(?string $name = null)
 * @method static string                      tokenGroupModel()
 * @method static TokenHasherInterface        tokenHasher(?string $name = null)
 * @method static TokenTypeInterface          tokenType(string $type)
 * @method static void                        useAccessTokenGroupModel(string $model)
 * @method static void                        useAccessTokenModel(string $model)
 *
 * @author Brian Faust <brian@cline.sh>
 * @see BearerManager
 */
final class Bearer extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * Returns the service container binding for the BearerManager instance
     * that this facade proxies to.
     *
     * @return string The fully qualified class name of BearerManager
     */
    protected static function getFacadeAccessor(): string
    {
        return BearerManager::class;
    }
}
