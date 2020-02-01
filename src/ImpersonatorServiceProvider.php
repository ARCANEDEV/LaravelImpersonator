<?php

declare(strict_types=1);

namespace Arcanedev\LaravelImpersonator;

use Arcanedev\Support\Providers\PackageServiceProvider;
use Illuminate\Auth\SessionGuard as IlluminateSessionGuard;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\DeferrableProvider;

/**
 * Class     ImpersonatorServiceProvider
 *
 * @package  Arcanedev\LaravelImpersonator
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class ImpersonatorServiceProvider extends PackageServiceProvider implements DeferrableProvider
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * Package name.
     *
     * @var string
     */
    protected $package = 'impersonator';

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        parent::register();

        $this->registerConfig();

        $this->registerProvider(Providers\AuthorizationServiceProvider::class);

        $this->singleton(Contracts\Impersonator::class, Impersonator::class);
        $this->extendAuthDriver();
    }

    /**
     * Boot the service provider.
     */
    public function boot(): void
    {
        $this->publishConfig();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [
            Contracts\Impersonator::class,
        ];
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Extend the auth session driver.
     */
    private function extendAuthDriver(): void
    {
        /** @var  \Illuminate\Auth\AuthManager  $auth */
        $auth = $this->app['auth'];

        $auth->extend('session', function (Application $app, $name, array $config) use ($auth) {
            $provider = $auth->createUserProvider($config['provider']);

            return tap(
                new Guard\SessionGuard($name, $provider, $app['session.store']),
                function (IlluminateSessionGuard $guard) use ($app) {
                    if (method_exists($guard, 'setCookieJar'))
                        $guard->setCookieJar($app['cookie']);

                    if (method_exists($guard, 'setDispatcher'))
                        $guard->setDispatcher($app['events']);

                    if (method_exists($guard, 'setRequest'))
                        $guard->setRequest($app->refresh('request', $guard, 'setRequest'));
                }
            );
        });
    }
}
