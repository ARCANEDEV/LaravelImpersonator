<?php

declare(strict_types=1);

namespace Arcanedev\LaravelImpersonator\Guard;

use Illuminate\Auth\SessionGuard as BaseSessionGuard;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Class     SessionGuard
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class SessionGuard extends BaseSessionGuard
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Login the user into the app without firing the Login event.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable|mixed  $user
     */
    public function silentLogin(Authenticatable $user): void
    {
        $this->updateSession($user->getAuthIdentifier());
        $this->setUser($user);
    }

    /**
     * Logout the user without updating `remember_token` and without firing the Logout event.
     */
    public function silentLogout(): void
    {
        $this->clearUserDataFromStorage();

        $this->user      = null;
        $this->loggedOut = true;
    }
}
