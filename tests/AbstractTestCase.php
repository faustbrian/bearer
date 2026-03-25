<?php declare(strict_types=1);

/**
 * Copyright (C) Brian Faust
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests;

use Cline\Ancestry\AncestryServiceProvider;
use Cline\Bearer\BearerServiceProvider;
use Cline\VariableKeys\VariableKeysServiceProvider;
use Cline\Warden\WardenServiceProvider;
use Fixtures\User;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\TestCase as Orchestra;

/**
 * Base test case for Bearer package tests.
 *
 * Provides test infrastructure including:
 * - Orchestra Testbench setup for package testing
 * - RefreshDatabase trait for clean database state per test
 * - Automatic loading of package migrations
 * - SQLite in-memory database configuration
 * - Bearer service provider registration
 * - Package configuration defaults
 *
 * @author Brian Faust <brian@cline.sh>
 * @internal
 */
abstract class AbstractTestCase extends Orchestra
{
    use RefreshDatabase;

    /**
     * Set up the test environment.
     *
     * Loads migrations from both the package and test fixtures.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadMigrationsFrom(__DIR__.'/Fixtures/migrations');
        $this->loadMigrationsFrom(__DIR__.'/../vendor/cline/ancestry/database/migrations');
        $this->loadMigrationsFrom(__DIR__.'/../vendor/cline/warden/migrations');
    }

    /**
     * Get package providers.
     *
     * @param  Application              $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            VariableKeysServiceProvider::class,
            BearerServiceProvider::class,
            AncestryServiceProvider::class,
            WardenServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * Configures the test environment with:
     * - SQLite in-memory database for speed and isolation
     * - Bearer default configuration for testing
     *
     * @param Application $app
     */
    protected function defineEnvironment($app): void
    {
        $app->make(Repository::class)->set('database.default', 'testing');
        $app->make(Repository::class)->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Load bearer configuration
        $config = require __DIR__.'/../config/bearer.php';
        $app->make(Repository::class)->set('bearer', $config);
        $app->make(Repository::class)->set('auth.defaults.guard', 'web');
        $app->make(Repository::class)->set('auth.guards.web', [
            'driver' => 'session',
            'provider' => 'users',
        ]);
        $app->make(Repository::class)->set('auth.providers.users', [
            'driver' => 'eloquent',
            'model' => User::class,
        ]);

        $wardenConfig = require __DIR__.'/../vendor/cline/warden/config/warden.php';
        $wardenConfig['user_model'] = User::class;
        $app->make(Repository::class)->set('warden', $wardenConfig);
    }

    /**
     * Get the base path for the package.
     */
    protected function getBasePath(): string
    {
        return __DIR__.'/../';
    }
}
