<?php

declare(strict_types=1);

namespace Arcanedev\LaravelImpersonator\Tests;

use Arcanedev\LaravelImpersonator\Contracts\Impersonator as ImpersonatorContract;
use Arcanedev\LaravelImpersonator\Http\Middleware\ImpersonationNotAllowed;
use Arcanedev\LaravelImpersonator\ImpersonatorServiceProvider;
use Arcanedev\LaravelImpersonator\Tests\Stubs\Controllers\ImpersonatorController;
use Arcanedev\LaravelImpersonator\Tests\Stubs\Models\User;
use Arcanedev\LaravelImpersonator\Tests\Stubs\Providers\AuthorizationServiceProvider;
use Arcanedev\LaravelPolicies\PoliciesServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

/**
 * Class     TestCase
 *
 * @package  Arcanedev\LaravelImpersonator\Tests
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
abstract class TestCase extends BaseTestCase
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate', ['--database' => 'testing']);

        $this->loadMigrationsFrom([
            '--database' => 'testing',
            '--path'     => realpath(__DIR__.'/fixtures/database/migrations'),
        ]);
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app): array
    {
        return [
            PoliciesServiceProvider::class,
            ImpersonatorServiceProvider::class,
            AuthorizationServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     */
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('app.debug', true);
        $app['config']->set('auth.providers.users.model', Stubs\Models\User::class);

        $this->setUpRoutes($app['router']);
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get the impersonator instance.
     *
     * @return \Arcanedev\LaravelImpersonator\Contracts\Impersonator
     */
    protected function impersonator(): ImpersonatorContract
    {
        return $this->app[ImpersonatorContract::class];
    }

    /**
     * Disable the impersonations.
     */
    protected function disableImpersonations(): void
    {
        $this->app['config']->set('impersonator.enabled', false);
    }

    /**
     * Login the user with the given id.
     *
     * @param  int  $id
     *
     * @return \Arcanedev\LaravelImpersonator\Tests\Stubs\Models\User
     */
    protected function loginWithId($id)
    {
        return $this->app['auth']->loginUsingId($id);
    }

    /**
     * Get the authenticated user.
     *
     * @return \Arcanedev\LaravelImpersonator\Tests\Stubs\Models\User|null
     */
    protected function getAuthenticatedUser()
    {
        return $this->app['auth']->user();
    }

    /**
     * Check if the user is logged in.
     */
    protected function assertIsLoggedIn(): void
    {
        static::assertTrue($this->app['auth']->check());
    }

    /**
     * Get a user by the given id.
     *
     * @param  int  $id
     *
     * @return \Arcanedev\LaravelImpersonator\Tests\Stubs\Models\User|mixed
     */
    protected function getUserById(int $id)
    {
        return User::query()->findOrFail($id);
    }

    /**
     * Setup the routes.
     *
     * @param  \Illuminate\Routing\Router  $router
     */
    private function setUpRoutes($router): void
    {
        $router->aliasMiddleware('no-impersonations', ImpersonationNotAllowed::class);

        $router->name('auth::impersonator.')->middleware('web')->group(function () use ($router) {
            $router->get('start/{id}', [ImpersonatorController::class, 'start'])
                   ->name('start'); // auth::impersonator.start

            $router->get('stop', [ImpersonatorController::class, 'stop'])
                   ->name('stop'); // auth::impersonator.stop;
        });

        $router->middleware(['web', 'auth', 'no-impersonations'])->group(function () use ($router) {
            $router->get('dashboard', function() {
                return 'Dashboard page';
            })->name('admin::dashboard');
        });
    }
}
