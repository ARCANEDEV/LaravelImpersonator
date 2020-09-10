<?php

declare(strict_types=1);

namespace Arcanedev\LaravelImpersonator\Contracts;

/**
 * Interface  Impersonator
 *
 * @author    ARCANEDEV <arcanedev.maroc@gmail.com>
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
    public function getSessionKey(): string;

    /**
     * Get the session guard.
     *
     * @return string
     */
    public function getSessionGuard(): string;

    /**
     * Get the impersonator id.
     *
     * @return  int|null
     */
    public function getImpersonatorId(): ?int;

    /**
     * Get the impersonator guard.
     *
     * @return string|null
     */
    public function getImpersonatorGuard(): ?string;

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Start the impersonation.
     *
     * @param  \Arcanedev\LaravelImpersonator\Contracts\Impersonatable|mixed  $impersonater
     * @param  \Arcanedev\LaravelImpersonator\Contracts\Impersonatable|mixed  $impersonated
     * @param  string|null                                                    $guard
     *
     * @return bool
     */
    public function start(Impersonatable $impersonater, Impersonatable $impersonated, $guard = null): bool;

    /**
     * Stop the impersonation.
     *
     * @return bool
     */
    public function stop(): bool;

    /**
     * Clear the impersonation.
     */
    public function clear(): void;

    /* -----------------------------------------------------------------
     |  Check Methods
     | -----------------------------------------------------------------
     */

    /**
     * Check if the impersonations is enabled.
     *
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * Check if the impersonator is impersonating.
     *
     * @return bool
     */
    public function isImpersonating(): bool;
}
