<?php namespace Arcanedev\LaravelImpersonator;

use Arcanedev\LaravelImpersonator\Contracts\Impersonatable;
use Illuminate\Contracts\Foundation\Application;

/**
 * Class     Impersonator
 *
 * @package  Arcanedev\LaravelImpersonator
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class Impersonator implements Contracts\Impersonator
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Illuminate\Contracts\Foundation\Application */
    protected $app;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * Impersonator constructor.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Get the guard session instance.
     *
     * @return \Illuminate\Auth\AuthManager|\Arcanedev\LaravelImpersonator\Guard\SessionGuard
     */
    protected function auth()
    {
        return $this->app['auth'];
    }

    /**
     * Get the session store instance.
     *
     * @return \Illuminate\Contracts\Session\Session
     */
    protected function session()
    {
        return $this->app['session'];
    }

    /**
     * Get the config repository.
     *
     * @return \Illuminate\Contracts\Config\Repository
     */
    protected function config()
    {
        return $this->app['config'];
    }

    /**
     * Get the event dispatcher.
     *
     * @return \Illuminate\Contracts\Events\Dispatcher
     */
    protected function events()
    {
        return $this->app['events'];
    }

    /**
     * Get the session key.
     *
     * @return string
     */
    public function getSessionKey()
    {
        return $this->config()->get('impersonator.session.key', 'impersonator_id');
    }

    /**
     * Get the impersonator id.
     *
     * @return  int|null
     */
    public function getImpersonatorId()
    {
        return $this->session()->get($this->getSessionKey(), null);
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Start the impersonation.
     *
     * @param  \Arcanedev\LaravelImpersonator\Contracts\Impersonatable  $impersonator
     * @param  \Arcanedev\LaravelImpersonator\Contracts\Impersonatable  $impersonated
     *
     * @return bool
     */
    public function start(Impersonatable $impersonator, Impersonatable $impersonated)
    {
        $this->checkImpersonation($impersonator, $impersonated);

        try {
            session()->put($this->getSessionKey(), $impersonator->getAuthIdentifier());
            $this->auth()->silentLogout();
            $this->auth()->silentLogin($impersonated);

            $this->events()->dispatch(
                new Events\ImpersonationStarted($impersonator, $impersonated)
            );

            return true;
        }
        catch (\Exception $e) { return false; }
    }

    /**
     * Stop the impersonation.
     *
     * @return bool
     */
    public function stop()
    {
        try {
            $impersonated = $this->auth()->user();
            $impersonator = $this->findUserById($this->getImpersonatorId());

            $this->auth()->silentLogout();
            $this->auth()->silentLogin($impersonator);
            $this->clear();

            $this->events()->dispatch(
                new Events\ImpersonationStopped($impersonator, $impersonated)
            );

            return true;
        }
        catch (\Exception $e) { return false; }
    }

    /**
     * Clear the impersonation.
     */
    public function clear()
    {
        $this->session()->forget($this->getSessionKey());
    }

    /**
     * Find a user by the given id.
     *
     * @param  int|string  $id
     *
     * @return \Arcanedev\LaravelImpersonator\Contracts\Impersonatable
     *
     * @throws \Exception
     */
    public function findUserById($id)
    {
        return call_user_func([$this->config()->get('auth.providers.users.model'), 'findOrFail'], $id);
    }

    /* -----------------------------------------------------------------
     |  Check Functions
     | -----------------------------------------------------------------
     */

    /**
     * Check if it's impersonating.
     *
     * @return bool
     */
    public function isImpersonating()
    {
        return $this->session()->has($this->getSessionKey());
    }

    /**
     * Check if the impersonations is enabled.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->config()->get('impersonator.enabled', false);
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Check the impersonation.
     *
     * @param  \Arcanedev\LaravelImpersonator\Contracts\Impersonatable  $impersonator
     * @param  \Arcanedev\LaravelImpersonator\Contracts\Impersonatable  $impersonated
     *
     * @throws Exceptions\ImpersonationException
     */
    private function checkImpersonation(Impersonatable $impersonator, Impersonatable $impersonated)
    {
        if ( ! $this->isEnabled())
            throw new Exceptions\ImpersonationException("The impersonation is disabled.");

        if ($impersonator->getAuthIdentifier() == $impersonated->getAuthIdentifier())
            throw new Exceptions\ImpersonationException('The impersonator & impersonated with must be different.');

        if ( ! $impersonator->canImpersonate())
            throw new Exceptions\ImpersonationException(
                "The impersonator with `{$impersonator->getAuthIdentifierName()}`=[{$impersonator->getAuthIdentifier()}] doesn't have the ability to impersonate."
            );

        if ( ! $impersonated->canBeImpersonated())
            throw new Exceptions\ImpersonationException(
                "The impersonated with `{$impersonated->getAuthIdentifierName()}`=[{$impersonated->getAuthIdentifier()}] cannot be impersonated."
            );
    }
}
