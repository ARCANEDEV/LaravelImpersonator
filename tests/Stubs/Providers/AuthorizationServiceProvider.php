<?php

declare(strict_types=1);

namespace Arcanedev\LaravelImpersonator\Tests\Stubs\Providers;

use Arcanedev\LaravelImpersonator\Policies\ImpersonationPolicy;
use Arcanedev\LaravelPolicies\Contracts\PolicyManager;
use Arcanedev\Support\Providers\AuthorizationServiceProvider as ServiceProvider;

/**
 * Class     AuthorizationServiceProvider
 *
 * @package  Arcanedev\LaravelImpersonator\Tests\Stubs\Providers
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class AuthorizationServiceProvider extends ServiceProvider
{
    /* -----------------------------------------------------------------
     |  Getters
     | -----------------------------------------------------------------
     */

    /**
     * Get policy's classes.
     *
     * @return iterable
     */
    public function policyClasses(): iterable
    {
        return [
            ImpersonationPolicy::class,
        ];
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Register any application authentication / authorization services.
     */
    public function boot(): void
    {
        parent::registerPolicies();

        $manager = $this->app->make(PolicyManager::class);

        foreach ($this->policyClasses() as $class) {
            $manager->registerClass($class);
        }
    }
}
