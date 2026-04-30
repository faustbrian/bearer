<?php declare(strict_types=1);

use Cline\Bearer\TransientToken;
use Illuminate\Auth\RequestGuard;
use Illuminate\Contracts\Auth\Factory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

test('auth manager resolves the bearer driver through the package factory', function (): void {
    Config::set('auth.guards.integration-bearer', [
        'driver' => 'bearer',
        'provider' => 'users',
    ]);

    /** @var Factory $auth */
    $auth = resolve(Factory::class);
    $auth->forgetGuards();

    $guard = $auth->guard('integration-bearer');

    expect($guard)->toBeInstanceOf(RequestGuard::class);
});

test('auth manager resolves the stateful bearer driver through the package factory', function (): void {
    Config::set('auth.guards.integration-stateful-bearer', [
        'driver' => 'stateful-bearer',
        'provider' => 'users',
    ]);

    /** @var Factory $auth */
    $auth = resolve(Factory::class);
    $auth->forgetGuards();

    $guard = $auth->guard('integration-stateful-bearer');

    expect($guard)->toBeInstanceOf(RequestGuard::class);
});

test('resolved stateful bearer guard still honors session authentication', function (): void {
    Config::set('auth.guards.integration-stateful-bearer', [
        'driver' => 'stateful-bearer',
        'provider' => 'users',
    ]);

    /** @var Factory $auth */
    $auth = resolve(Factory::class);
    $auth->forgetGuards();

    Route::middleware(['auth:integration-stateful-bearer'])->get('/integration-stateful-auth', fn () => response()->json([
        'user_id' => Auth::id(),
        'token_type' => Auth::user()?->currentAccessToken() ? Auth::user()->currentAccessToken()::class : null,
    ]));

    $user = createUser([
        'email' => 'stateful-'.uniqid().'@example.com',
    ]);

    $response = $this->actingAs($user, 'web')->get('/integration-stateful-auth');

    $response->assertOk();
    expect($response->json('user_id'))->toBe($user->id);
    expect($response->json('token_type'))->toBe(TransientToken::class);
});
