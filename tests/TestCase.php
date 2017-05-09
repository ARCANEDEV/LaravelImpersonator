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
    protected function setUp()
    {
        parent::setUp();

        $this->artisan('migrate', ['--database' => 'testbench']);

        $this->loadMigrationsFrom([
            '--database' => 'testbench',
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
            \Orchestra\Database\ConsoleServiceProvider::class,
            \Arcanedev\LaravelImpersonator\ImpersonatorServiceProvider::class,
        ];
    }

    /**
     * Get package aliases.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            //
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application   $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $app['config']->set('auth.providers.users.model', Stubs\Models\User::class);
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
        $this->assertTrue($this->app['auth']->check());
    }
}
