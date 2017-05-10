<?php namespace Arcanedev\LaravelImpersonator\Policies;

use Arcanedev\LaravelImpersonator\Contracts\Impersonatable;
use Arcanedev\Support\Bases\Policy;

/**
 * Class     ImpersonationPolicy
 *
 * @package  Arcanedev\LaravelImpersonator\Policies
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class ImpersonationPolicy extends Policy
{
    /* -----------------------------------------------------------------
     |  Constants
     | -----------------------------------------------------------------
     */

    const CAN_IMPERSONATE     = 'auth.impersonator.can-impersonate';
    const CAN_BE_IMPERSONATED = 'auth.impersonator.can-be-impersonated';

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Check if the current user can impersonate.
     *
     * @param  \Arcanedev\LaravelImpersonator\Contracts\Impersonatable  $impersonater
     *
     * @return bool
     */
    public function canImpersonatePolicy(Impersonatable $impersonater)
    {
        return $this->isEnabled() && $impersonater->canImpersonate();
    }

    /**
     * Check if the given user can be impersonated.
     *
     * @param  \Arcanedev\LaravelImpersonator\Contracts\Impersonatable  $impersonater
     * @param  \Arcanedev\LaravelImpersonator\Contracts\Impersonatable  $impersonated
     *
     * @return bool
     */
    public function canBeImpersonatedPolicy(Impersonatable $impersonater, Impersonatable $impersonated)
    {
        return $this->canImpersonatePolicy($impersonater) && $impersonated->canBeImpersonated();
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Check if the impersonation is enabled.
     *
     * @return bool
     */
    private function isEnabled()
    {
        return config('impersonator.enabled', false);
    }
}
