<?php namespace Arcanedev\LaravelImpersonator\Traits;

use Arcanedev\LaravelImpersonator\Contracts\Impersonatable;
use Arcanedev\LaravelImpersonator\Contracts\Impersonator;

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
     * @param   \Arcanedev\LaravelImpersonator\Contracts\Impersonatable  $impersonated
     *
     * @return  bool
     */
    public function impersonate(Impersonatable $impersonated)
    {
        /** @var  \Arcanedev\LaravelImpersonator\Contracts\Impersonatable  $this */
        return $this->impersonatorManager()->start($this, $impersonated);
    }

    /**
     * Leave the current impersonation.
     *
     * @param   void
     *
     * @return  bool
     */
    public function stopImpersonation()
    {
        return $this->isImpersonated()
            ? $this->impersonatorManager()->stop()
            : false;
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
    public function canImpersonate()
    {
        return true;
    }

    /**
     * Check if the current model can be impersonated.
     *
     * @return  bool
     */
    public function canBeImpersonated()
    {
        return true;
    }

    /**
     * Check if impersonation is ongoing.
     *
     * @return  bool
     */
    public function isImpersonated()
    {
        return $this->impersonatorManager()->isImpersonating();
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get the impersonator manager.
     *
     * @return \Arcanedev\LaravelImpersonator\Contracts\Impersonator
     */
    protected function impersonatorManager()
    {
        return app(Impersonator::class);
    }
}
