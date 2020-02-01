<?php

declare(strict_types=1);

namespace Arcanedev\LaravelImpersonator\Traits;

use Arcanedev\LaravelImpersonator\Contracts\Impersonatable;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait     HasImpersonation
 *
 * @package  Arcanedev\LaravelImpersonator\Traits
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
trait CanImpersonate
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Impersonate the given user.
     *
     * @param   \Arcanedev\LaravelImpersonator\Contracts\Impersonatable|mixed  $impersonated
     *
     * @return  bool
     */
    public function impersonate(Impersonatable $impersonated)
    {
        /** @var  \Arcanedev\LaravelImpersonator\Contracts\Impersonatable  $this */
        return impersonator()->start($this, $impersonated);
    }

    /**
     * Stop the impersonation.
     *
     * @return  bool
     */
    public function stopImpersonation()
    {
        return $this->isImpersonated() ? impersonator()->stop() : false;
    }

    /* -----------------------------------------------------------------
     |  Check Methods
     | -----------------------------------------------------------------
     */

    /**
     * Check if the current modal can impersonate other models.
     *
     * @return  bool
     */
    abstract public function canImpersonate();

    /**
     * Check if the current model can be impersonated.
     *
     * @return  bool
     */
    abstract public function canBeImpersonated();

    /**
     * Check if impersonation is ongoing.
     *
     * @return  bool
     */
    public function isImpersonated()
    {
        return impersonator()->isImpersonating();
    }

    /**
     * Check if the two persons are the same.
     *
     * @param  \Arcanedev\LaravelImpersonator\Contracts\Impersonatable|mixed  $impersonated
     *
     * @return bool
     */
    public function isSamePerson($impersonated)
    {
        if ($this instanceof Model && $impersonated instanceof Model) {
            return $this->is($impersonated);
        }

        return $this->getAuthIdentifier() == $impersonated->getAuthIdentifier();
    }
}
