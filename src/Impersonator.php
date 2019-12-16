<?php namespace Arcanedev\LaravelImpersonator;

use Arcanedev\LaravelImpersonator\Contracts\Impersonatable;
use Arcanedev\LaravelImpersonator\Exceptions\ImpersonationException;
use Exception;
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
     * @param  \Arcanedev\LaravelImpersonator\Contracts\Impersonatable|mixed  $impersonater
     * @param  \Arcanedev\LaravelImpersonator\Contracts\Impersonatable|mixed  $impersonated
     *
     * @return bool
     */
    public function start(Impersonatable $impersonater, Impersonatable $impersonated)
    {
        $this->checkImpersonation($impersonater, $impersonated);

        try {
            session()->put($this->getSessionKey(), $impersonater->getAuthIdentifier());
            $this->auth()->silentLogout();
            $this->auth()->silentLogin($impersonated);

            $this->events()->dispatch(
                new Events\ImpersonationStarted($impersonater, $impersonated)
            );

            return true;
        }
        catch (Exception $e) {
            return false;
        }
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
            $impersonater = $this->findUserById($this->getImpersonatorId());

            $this->auth()->silentLogout();
            $this->auth()->silentLogin($impersonater);
            $this->clear();

            $this->events()->dispatch(
                new Events\ImpersonationStopped($impersonater, $impersonated)
            );

            return true;
        }
        catch (Exception $e) {
            return false;
        }
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
     * @param  \Arcanedev\LaravelImpersonator\Contracts\Impersonatable|mixed  $impersonater
     * @param  \Arcanedev\LaravelImpersonator\Contracts\Impersonatable|mixed  $impersonated
     */
    private function checkImpersonation(Impersonatable $impersonater, Impersonatable $impersonated): void
    {
        $this->mustBeEnabled();
        $this->mustBeDifferentImpersonatable($impersonater, $impersonated);
        $this->checkImpersonater($impersonater);
        $this->checkImpersonated($impersonated);
    }

    /**
     * Check if the impersonation is enabled.
     *
     * @throws \Arcanedev\LaravelImpersonator\Exceptions\ImpersonationException
     */
    private function mustBeEnabled(): void
    {
        if ( ! $this->isEnabled())
            throw new ImpersonationException(
                'The impersonation is disabled.'
            );
    }

    /**
     * Check the impersonater and the impersonated are different.
     *
     * @param  \Arcanedev\LaravelImpersonator\Contracts\Impersonatable|mixed  $impersonater
     * @param  \Arcanedev\LaravelImpersonator\Contracts\Impersonatable|mixed  $impersonated
     *
     * @throws \Arcanedev\LaravelImpersonator\Exceptions\ImpersonationException
     */
    private function mustBeDifferentImpersonatable(Impersonatable $impersonater, Impersonatable $impersonated): void
    {
        if ($impersonater->isSamePerson($impersonated)) {
            throw Exceptions\ImpersonationException::impersonaterAndImpersonatedAreSame();
        }
    }

    /**
     * Check the impersonater.
     *
     * @param  \Arcanedev\LaravelImpersonator\Contracts\Impersonatable|mixed  $impersonater
     *
     * @throws \Arcanedev\LaravelImpersonator\Exceptions\ImpersonationException
     */
    private function checkImpersonater(Impersonatable $impersonater): void
    {
        if ( ! $impersonater->canImpersonate())
            throw ImpersonationException::cannotImpersonate($impersonater);
    }

    /**
     * Check the impersonated.
     *
     * @param  \Arcanedev\LaravelImpersonator\Contracts\Impersonatable|mixed  $impersonated
     *
     * @throws \Arcanedev\LaravelImpersonator\Exceptions\ImpersonationException
     */
    private function checkImpersonated(Impersonatable $impersonated): void
    {

        if ( ! $impersonated->canBeImpersonated())
            throw ImpersonationException::cannotBeImpersonated($impersonated);
    }
}
