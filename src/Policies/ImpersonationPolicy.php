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
     * Check if the current user has the `can impersonate` ability.
     *
     * @param  \Arcanedev\LaravelImpersonator\Contracts\Impersonatable  $impersonator
     *
     * @return bool
     */
    public function canImpersonatePolicy(Impersonatable $impersonator)
    {
        return $impersonator->canImpersonate();
    }

    /**
     * Check if the given user can be impersonated.
     *
     * @param  \Arcanedev\LaravelImpersonator\Contracts\Impersonatable  $impersonator
     * @param  \Arcanedev\LaravelImpersonator\Contracts\Impersonatable  $impersonated
     *
     * @return bool
     */
    public function canBeImpersonatedPolicy(Impersonatable $impersonator, Impersonatable $impersonated)
    {
        return $this->canImpersonatePolicy($impersonator) && $impersonated->canBeImpersonated();
    }
}
