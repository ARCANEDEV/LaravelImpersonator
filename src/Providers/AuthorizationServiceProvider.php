<?php

declare(strict_types=1);

namespace Arcanedev\LaravelImpersonator\Providers;

use Arcanedev\LaravelImpersonator\Policies\ImpersonationPolicy;
use Arcanedev\Support\Providers\AuthorizationServiceProvider as ServiceProvider;

/**
 * Class     AuthorizationServiceProvider
 *
 * @package  Arcanedev\LaravelImpersonator\Providers
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class AuthorizationServiceProvider extends ServiceProvider
{
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

        $this->defineMany(ImpersonationPolicy::class, ImpersonationPolicy::policies());
    }
}
