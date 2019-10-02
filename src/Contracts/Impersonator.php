<?php namespace Arcanedev\LaravelImpersonator\Contracts;

/**
 * Interface     Impersonator
 *
 * @package  Arcanedev\LaravelImpersonator\Contracts
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
interface Impersonator
{
    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Get the session key.
     *
     * @return string
     */
    public function getSessionKey();

    /**
     * Get the impersonator id.
     *
     * @return  int|null
     */
    public function getImpersonatorId();

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Start the impersonation.
     *
     * @param  \Arcanedev\LaravelImpersonator\Contracts\Impersonatable|mixed  $impersonater
     * @param  \Arcanedev\LaravelImpersonator\Contracts\Impersonatable|mixed  $impersonated
     *
     * @return bool
     */
    public function start(Impersonatable $impersonater, Impersonatable $impersonated);

    /**
     * Stop the impersonation.
     *
     * @return bool
     */
    public function stop();

    /**
     * Clear the impersonation.
     */
    public function clear();

    /**
     * Find a user by the given id.
     *
     * @param  int|string  $id
     *
     * @return \Arcanedev\LaravelImpersonator\Contracts\Impersonatable
     *
     * @throws \Exception
     */
    public function findUserById($id);

    /* -----------------------------------------------------------------
     |  Check Methods
     | -----------------------------------------------------------------
     */

    /**
     * Check if the impersonations is enabled.
     *
     * @return bool
     */
    public function isEnabled();

    /**
     * Check if the impersonator is impersonating.
     *
     * @return bool
     */
    public function isImpersonating();
}
