<?php namespace Arcanedev\LaravelImpersonator\Tests;

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
            '--realpath' => realpath(__DIR__.'/fixtures/database/migrations'),
        ]);
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            \Arcanedev\LaravelImpersonator\ImpersonatorServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
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
     * @return \Arcanedev\LaravelImpersonator\Contracts\Impersonator::class
     */
    protected function impersonator()
    {
        return $this->app[\Arcanedev\LaravelImpersonator\Contracts\Impersonator::class];
    }

    /**
     * Disable the impersonations.
     */
    protected function disableImpersonations()
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
    protected function assertIsLoggedIn()
    {
        static::assertTrue($this->app['auth']->check());
    }

    /**
     * @param  \Illuminate\Routing\Router  $router
     */
    private function setUpRoutes($router)
    {
        $router->aliasMiddleware(
            'no-impersonations',
            \Arcanedev\LaravelImpersonator\Http\Middleware\ImpersonationNotAllowed::class
        );

        $router->group([
            'namespace'  => 'Arcanedev\LaravelImpersonator\Tests\Stubs\Controllers',
            'as'         => 'auth::impersonator.',
            'middleware' => 'web',
        ], function () use ($router) {
            $router->get('start/{id}', [
                'uses' => 'ImpersonatorController@start',
                'as'   => 'start', // auth::impersonator.start
            ]);

            $router->get('stop', [
                'uses' => 'ImpersonatorController@stop',
                'as'   => 'stop', // auth::impersonator.stop
            ]);
        });

        $router->group(['middleware' => ['web', 'auth', 'no-impersonations']], function () use ($router) {
            $router->get('dashboard', [
                'as'   => 'admin::dashboard',
                'uses' => function() {
                    return 'Dashboard page';
                }
            ]);
        });
    }
}
