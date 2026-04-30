<?php declare(strict_types=1);

use Cline\Bearer\Database\Models\AccessToken;
use Cline\Bearer\TransientToken;
use Illuminate\Contracts\Auth\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Tests\Fixtures\User;
use Tests\Fixtures\UserWithoutTokens;

function createRawTokenForStatefulGuard(Authenticatable $user, array $attributes): AccessToken
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

describe('StatefulBearerGuard', function (): void {
    beforeEach(function (): void {
        Config::set('auth.providers.users.model', User::class);
        Config::set('app.key', 'base64:'.base64_encode(random_bytes(32)));
        Config::set('auth.guards.test-stateful-bearer', [
            'driver' => 'stateful-bearer',
            'provider' => 'users',
        ]);

        resolve(Factory::class)->forgetGuards();

        Route::middleware(['auth:test-stateful-bearer'])->get('/test-stateful-auth', fn () => response()->json([
            'user_id' => Auth::id(),
            'token_type' => Auth::user()?->currentAccessToken() ? Auth::user()->currentAccessToken()::class : null,
        ]));
    });

    test('returns user from stateful guard when authenticated via session', function (): void {
        $user = createUser(['email' => uniqid().'@example.com']);

        $response = $this->actingAs($user, 'web')->get('/test-stateful-auth');

        $response->assertOk();

        expect($response->json('user_id'))->toBe($user->id);
        expect($response->json('token_type'))->toBe(TransientToken::class);
    });

    test('checks multiple stateful guards in order', function (): void {
        Config::set('bearer.guard', ['web', 'admin']);

        $user = createUser(['email' => uniqid().'@example.com']);

        $response = $this->actingAs($user, 'web')->get('/test-stateful-auth');

        $response->assertOk();

        expect($response->json('user_id'))->toBe($user->id);
        expect($response->json('token_type'))->toBe(TransientToken::class);
    });

    test('returns user without token wrapper when session user lacks access token support', function (): void {
        $user = UserWithoutTokens::query()->create([
            'name' => 'No Token User',
            'email' => uniqid().'@example.com',
            'password' => bcrypt('password'),
        ]);

        Route::middleware(['auth:test-stateful-bearer'])->get('/test-stateful-no-tokens', fn () => response()->json([
            'user_id' => Auth::id(),
            'user_class' => Auth::user() ? Auth::user()::class : null,
        ]));

        $response = $this->actingAs($user, 'web')->get('/test-stateful-no-tokens');

        $response->assertOk();

        expect($response->json('user_id'))->toBe($user->id);
        expect($response->json('user_class'))->toBe(UserWithoutTokens::class);
    });

    test('single guard string in config works correctly', function (): void {
        Config::set('bearer.guard', 'web');

        $user = createUser(['email' => uniqid().'@example.com']);

        $response = $this->actingAs($user, 'web')->get('/test-stateful-auth');

        $response->assertOk();

        expect($response->json('user_id'))->toBe($user->id);
    });

    test('prefers stateful guard over bearer token when both are present', function (): void {
        $sessionUser = createUser(['email' => 'session-'.uniqid().'@example.com']);
        $tokenUser = createUser(['email' => 'token-'.uniqid().'@example.com']);

        $plainToken = 'both-present-token-'.uniqid();
        createRawTokenForStatefulGuard($tokenUser, ['token' => hash('sha256', $plainToken)]);

        $response = $this->actingAs($sessionUser, 'web')
            ->withHeader('Authorization', 'Bearer '.$plainToken)
            ->get('/test-stateful-auth');

        $response->assertOk();

        expect($response->json('user_id'))->toBe($sessionUser->id);
        expect($response->json('token_type'))->toBe(TransientToken::class);
    });
});
