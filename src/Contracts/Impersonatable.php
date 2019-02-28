<?php namespace Arcanedev\LaravelImpersonator\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Interface     Impersonatable
 *
 * @package  Arcanedev\LaravelImpersonator\Contracts
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
interface Impersonatable extends Authenticatable
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Impersonate the given user.
     *
     * @param  \Arcanedev\LaravelImpersonator\Contracts\Impersonatable  $impersonated
     *
     * @return bool
     */
    public function impersonate(Impersonatable $impersonated);

    /**
     * Leave the current impersonation.
     *
     * @return bool
     */
    public function stopImpersonation();

    /* -----------------------------------------------------------------
     |  Check Methods
     | -----------------------------------------------------------------
     */

    /**
     * Check if the current modal can impersonate other models.
     *
     * @return bool
     */
    public function canImpersonate();

    /**
     * Check if the current model can be impersonated.
     *
     * @return bool
     */
    public function canBeImpersonated();

    /**
     * Check if impersonation is ongoing.
     *
     * @return bool
     */
    public function isImpersonated();
}
