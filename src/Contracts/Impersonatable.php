<?php

declare(strict_types=1);

namespace Arcanedev\LaravelImpersonator\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Interface  Impersonatable
 *
 * @author    ARCANEDEV <arcanedev.maroc@gmail.com>
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
     * @param  \Arcanedev\LaravelImpersonator\Contracts\Impersonatable|mixed  $impersonated
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

    /**
     * Check if the two persons are the same.
     *
     * @param  \Arcanedev\LaravelImpersonator\Contracts\Impersonatable|mixed  $impersonated
     *
     * @return bool
     */
    public function isSamePerson($impersonated);
}
