<?php

declare(strict_types=1);

namespace Arcanedev\LaravelImpersonator;

use Arcanedev\LaravelImpersonator\Contracts\Impersonator as ImpersonatorContract;
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

        $this->singleton(ImpersonatorContract::class, Impersonator::class);
        $this->extendAuthDriver();
    }

    /**
     * Boot the service provider.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishConfig();
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [
            ImpersonatorContract::class,
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
            $store    = $app['session']->driver(
                $app['config']['impersonator.session.store']
            );

            return tap(
                new Guard\SessionGuard($name, $provider, $store),
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
