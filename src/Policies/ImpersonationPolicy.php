<?php

declare(strict_types=1);

namespace Arcanedev\LaravelImpersonator\Policies;

use Arcanedev\LaravelImpersonator\Contracts\Impersonatable;
use Arcanedev\LaravelPolicies\Policy;
use Illuminate\Foundation\Auth\User;

/**
 * Class     ImpersonationPolicy
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class ImpersonationPolicy extends Policy
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get the ability's prefix.
     *
     * @return string
     */
    protected static function prefix(): string
    {
        return 'auth::impersonator.';
    }

    /**
     * Get the abilities.
     *
     * @return iterable
     */
    public function abilities(): iterable
    {
        return [

            // auth::impersonator.can-impersonate
            static::makeAbility('can-impersonate')->setMetas([
                'name'        => 'Ability to impersonate',
                'description' => 'Ability to impersonate other users',
            ]),

            // auth::impersonator.can-be-impersonated
            static::makeAbility('can-be-impersonated')->setMetas([
                'name'        => 'Ability to be impersonated',
                'description' => 'Ability to be impersonated by other users',
            ]),

        ];
    }

    /* -----------------------------------------------------------------
     |  Policies
     | -----------------------------------------------------------------
     */

    /**
     * Check if the current user can impersonate.
     *
     * @param  \Arcanedev\LaravelImpersonator\Contracts\Impersonatable|\Illuminate\Foundation\Auth\User  $user
     *
     * @return bool
     */
    public function canImpersonate(User $user): bool
    {
        if ( ! $this->isEnabled()) {
            return false;
        }

        return $user->canImpersonate();
    }

    /**
     * Check if the given user can be impersonated.
     *
     * @param  \Arcanedev\LaravelImpersonator\Contracts\Impersonatable|\Illuminate\Foundation\Auth\User  $user
     * @param  \Arcanedev\LaravelImpersonator\Contracts\Impersonatable                                   $impersonated
     *
     * @return bool
     */
    public function canBeImpersonated(User $user, Impersonatable $impersonated): bool
    {
        if ( ! $this->canImpersonate($user)) {
            return false;
        }

        return $impersonated->canBeImpersonated();
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
