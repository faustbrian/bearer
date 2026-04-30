<?php declare(strict_types=1);

use Cline\Bearer\BearerManager;
use Cline\Bearer\Database\Models\AccessToken;
use Cline\Bearer\Events\TokenAuthenticated;
use Cline\Bearer\Exceptions\TokenHasBeenRevokedException;
use Cline\Bearer\Exceptions\TokenHasExpiredException;
use Cline\Bearer\Guards\BearerTokenAuthenticator;
use Cline\Bearer\Guards\StatelessBearerGuard;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Event;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Tests\Fixtures\User;

function createRawTokenForStatelessGuard(Authenticatable $user, array $attributes): AccessToken
{
    $defaults = [
        'type' => 'secret_key',
        'environment' => 'testing',
        'name' => 'Test Token',
        'prefix' => 'sk_test',
        'owner_type' => User::class,
        'owner_id' => $user->id,
    ];

    return Model::unguarded(fn () => AccessToken::query()->create(array_merge($defaults, $attributes)));
}

function makeStatelessBearerGuard(): StatelessBearerGuard
{
    return new StatelessBearerGuard(
        new BearerTokenAuthenticator(
            resolve(BearerManager::class),
            Config::get('bearer.expiration'),
            Config::get('auth.guards.bearer.provider'),
        ),
    );
}

describe('StatelessBearerGuard', function (): void {
    beforeEach(function (): void {
        Config::set('auth.providers.users.model', User::class);
        Config::set('auth.guards.bearer.provider', 'users');
        Config::set('app.key', 'base64:'.base64_encode(random_bytes(32)));
    });

    afterEach(function (): void {
        Date::setTestNow();
    });

    test('rejects session-only authentication', function (): void {
        $sessionUser = createUser(['email' => 'session-'.uniqid().'@example.com']);

        Auth::guard('web')->setUser($sessionUser);
        Auth::shouldUse('web');

        $guard = makeStatelessBearerGuard();
        $request = Request::create('/test-auth', SymfonyRequest::METHOD_GET);

        expect($guard($request))->toBeNull();
    });

    test('authenticates a valid bearer token and attaches the access token', function (): void {
        Event::fake();

        $user = createUser(['email' => uniqid().'@example.com']);
        $plainToken = 'stateless-token-'.uniqid();
        $token = createRawTokenForStatelessGuard($user, [
            'token' => hash('sha256', $plainToken),
            'abilities' => ['read', 'write'],
        ]);

        $guard = makeStatelessBearerGuard();
        $request = Request::create('/test-auth', SymfonyRequest::METHOD_GET, server: [
            'HTTP_AUTHORIZATION' => 'Bearer '.$plainToken,
        ]);

        $authenticatedUser = $guard($request);

        expect($authenticatedUser)->not->toBeNull()
            ->and($authenticatedUser->getKey())->toBe($user->id)
            ->and($authenticatedUser->currentAccessToken())->toBeInstanceOf(AccessToken::class);

        Event::assertDispatched(TokenAuthenticated::class);

        $token->refresh();
        expect($token->last_used_at)->not->toBeNull();
    });

    test('prefers the bearer token when both session and bearer credentials are present', function (): void {
        $sessionUser = createUser(['email' => 'session-'.uniqid().'@example.com']);
        $tokenUser = createUser(['email' => 'token-'.uniqid().'@example.com']);

        $plainToken = 'both-present-token-'.uniqid();
        createRawTokenForStatelessGuard($tokenUser, [
            'token' => hash('sha256', $plainToken),
        ]);

        Auth::guard('web')->setUser($sessionUser);
        Auth::shouldUse('web');

        $guard = makeStatelessBearerGuard();
        $request = Request::create('/test-auth', SymfonyRequest::METHOD_GET, server: [
            'HTTP_AUTHORIZATION' => 'Bearer '.$plainToken,
        ]);

        $authenticatedUser = $guard($request);

        expect($authenticatedUser)->not->toBeNull()
            ->and($authenticatedUser->getKey())->toBe($tokenUser->id)
            ->and($authenticatedUser->currentAccessToken())->toBeInstanceOf(AccessToken::class);
    });

    test('throws for revoked bearer tokens', function (): void {
        $user = createUser(['email' => uniqid().'@example.com']);
        $plainToken = 'revoked-token-'.uniqid();
        createRawTokenForStatelessGuard($user, [
            'token' => hash('sha256', $plainToken),
            'revoked_at' => now()->subHour(),
        ]);

        $guard = makeStatelessBearerGuard();
        $request = Request::create('/test-auth', SymfonyRequest::METHOD_GET, server: [
            'HTTP_AUTHORIZATION' => 'Bearer '.$plainToken,
        ]);

        expect(fn (): mixed => $guard($request))->toThrow(TokenHasBeenRevokedException::class);
    });

    test('throws for expired bearer tokens', function (): void {
        Config::set('bearer.expiration', 60);

        $user = createUser(['email' => uniqid().'@example.com']);
        $plainToken = 'expired-token-'.uniqid();
        createRawTokenForStatelessGuard($user, [
            'token' => hash('sha256', $plainToken),
            'created_at' => now()->subMinutes(120),
        ]);

        $guard = makeStatelessBearerGuard();
        $request = Request::create('/test-auth', SymfonyRequest::METHOD_GET, server: [
            'HTTP_AUTHORIZATION' => 'Bearer '.$plainToken,
        ]);

        expect(fn (): mixed => $guard($request))->toThrow(TokenHasExpiredException::class);
    });

    test('returns null when IP restrictions reject the request', function (): void {
        $user = createUser(['email' => uniqid().'@example.com']);
        $plainToken = 'ip-restricted-token-'.uniqid();
        createRawTokenForStatelessGuard($user, [
            'token' => hash('sha256', $plainToken),
            'allowed_ips' => ['192.168.1.1'],
        ]);

        $guard = makeStatelessBearerGuard();
        $request = Request::create('/test-auth', SymfonyRequest::METHOD_GET, server: [
            'HTTP_AUTHORIZATION' => 'Bearer '.$plainToken,
            'REMOTE_ADDR' => '8.8.8.8',
        ]);

        expect($guard($request))->toBeNull();
    });

    test('returns null when domain restrictions reject the request', function (): void {
        $user = createUser(['email' => uniqid().'@example.com']);
        $plainToken = 'domain-restricted-token-'.uniqid();
        createRawTokenForStatelessGuard($user, [
            'token' => hash('sha256', $plainToken),
            'allowed_domains' => ['example.com'],
        ]);

        $guard = makeStatelessBearerGuard();
        $request = Request::create('/test-auth', SymfonyRequest::METHOD_GET, server: [
            'HTTP_AUTHORIZATION' => 'Bearer '.$plainToken,
            'HTTP_ORIGIN' => 'https://evil.com',
        ]);

        expect($guard($request))->toBeNull();
    });
});
