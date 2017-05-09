<?php namespace Arcanedev\LaravelImpersonator\Guard;

use Illuminate\Auth\SessionGuard as BaseSessionGuard;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Class     SessionGuard
 *
 * @package  Arcanedev\LaravelImpersonator\Guard
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
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     */
    public function silentLogin(Authenticatable $user)
    {
        $this->updateSession($user->getAuthIdentifier());
        $this->setUser($user);
    }

    /**
     * Logout the user without updating `remember_token` and without firing the Logout event.
     */
    public function silentLogout()
    {
        $this->clearUserDataFromStorage();

        $this->user      = null;
        $this->loggedOut = true;
    }
}
