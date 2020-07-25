<?php

declare(strict_types=1);

namespace Arcanedev\LaravelImpersonator;

use Arcanedev\LaravelImpersonator\Contracts\Impersonatable;
use Arcanedev\LaravelImpersonator\Contracts\Impersonator as ImpersonatorContract;
use Arcanedev\LaravelImpersonator\Exceptions\ImpersonationException;
use Exception;
use Illuminate\Contracts\Foundation\Application;

/**
 * Class     Impersonator
 *
 * @package  Arcanedev\LaravelImpersonator
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class Impersonator implements ImpersonatorContract
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
    public function getSessionKey(): string
    {
        return $this->config()->get('impersonator.session.key', 'impersonator_id');
    }

    /**
     * Get the session guard.
     *
     * @return string
     */
    public function getSessionGuard(): string
    {
        return $this->config()->get('impersonator.session.guard', 'impersonator_guard');
    }

    /**
     * Get the impersonator id.
     *
     * @return int|null
     */
    public function getImpersonatorId(): ?int
    {
        return $this->session()->get($this->getSessionKey(), null);
    }

    /**
     * Get the impersonator guard.
     *
     * @return string|null
     */
    public function getImpersonatorGuard(): ?string
    {
        return $this->session()->get($this->getSessionGuard(), null)
            ?: $this->auth()->getDefaultDriver();
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Start the impersonation.
     *
     * @param  \Arcanedev\LaravelImpersonator\Contracts\Impersonatable|mixed  $impersonator
     * @param  \Arcanedev\LaravelImpersonator\Contracts\Impersonatable|mixed  $impersonated
     * @param  string|null                                                    $guard
     *
     * @return bool
     */
    public function start(Impersonatable $impersonator, Impersonatable $impersonated, $guard = null): bool
    {
        $this->checkImpersonation($impersonator, $impersonated);

        try {
            $this->rememberImpersonater($impersonator);

            $auth = $this->auth();
            $auth->guard()->silentLogout();
            $auth->guard($guard)->silentLogin($impersonated);

            $this->events()->dispatch(
                new Events\ImpersonationStarted($impersonator, $impersonated)
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
    public function stop(): bool
    {
        try {
            $auth = $this->auth();

            $impersonated = $auth->user();
            $impersonator = $this->getImpersonatorFromSession();

            $auth->silentLogout();
            $auth->guard($this->getImpersonatorGuard())->silentLogin($impersonator);
            $this->clear();

            $this->events()->dispatch(
                new Events\ImpersonationStopped($impersonator, $impersonated)
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
    public function clear(): void
    {
        $this->session()->forget([
            $this->getSessionKey(),
            $this->getSessionGuard(),
        ]);
    }

    /**
     * Get the impersonator from session.
     *
     * @return \Arcanedev\LaravelImpersonator\Contracts\Impersonatable|mixed|null
     *
     * @throws \Exception
     */
    protected function getImpersonatorFromSession()
    {
        $user = $this->auth()
            ->guard($this->getImpersonatorGuard())
            ->getProvider()
            ->retrieveById($this->getImpersonatorId());

        abort_if(is_null($user), 404, 'User not found');

        return $user;
    }

    /* -----------------------------------------------------------------
     |  Check Methods
     | -----------------------------------------------------------------
     */

    /**
     * Check if it's impersonating.
     *
     * @return bool
     */
    public function isImpersonating(): bool
    {
        return $this->session()->has([
            $this->getSessionKey(),
            $this->getSessionGuard(),
        ]);
    }

    /**
     * Check if the impersonations is enabled.
     *
     * @return bool
     */
    public function isEnabled(): bool
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
     * @param  \Arcanedev\LaravelImpersonator\Contracts\Impersonatable|mixed  $impersonator
     * @param  \Arcanedev\LaravelImpersonator\Contracts\Impersonatable|mixed  $impersonated
     */
    private function checkImpersonation(Impersonatable $impersonator, Impersonatable $impersonated): void
    {
        $this->mustBeEnabled();
        $this->mustBeDifferentImpersonatable($impersonator, $impersonated);
        $this->checkImpersonater($impersonator);
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
     * Check the impersonator and the impersonated are different.
     *
     * @param  \Arcanedev\LaravelImpersonator\Contracts\Impersonatable|mixed  $impersonator
     * @param  \Arcanedev\LaravelImpersonator\Contracts\Impersonatable|mixed  $impersonated
     *
     * @throws \Arcanedev\LaravelImpersonator\Exceptions\ImpersonationException
     */
    private function mustBeDifferentImpersonatable(Impersonatable $impersonator, Impersonatable $impersonated): void
    {
        if ($impersonator->isSamePerson($impersonated)) {
            throw Exceptions\ImpersonationException::selfImpersonation();
        }
    }

    /**
     * Check the impersonator.
     *
     * @param  \Arcanedev\LaravelImpersonator\Contracts\Impersonatable|mixed  $impersonator
     *
     * @throws \Arcanedev\LaravelImpersonator\Exceptions\ImpersonationException
     */
    private function checkImpersonater(Impersonatable $impersonator): void
    {
        if ( ! $impersonator->canImpersonate())
            throw ImpersonationException::cannotImpersonate($impersonator);
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

    /**
     * Remember the impersonator.
     *
     * @param  \Arcanedev\LaravelImpersonator\Contracts\Impersonatable  $impersonator
     */
    private function rememberImpersonater(Impersonatable $impersonator)
    {
        $this->session()->put([
            $this->getSessionKey()   => $impersonator->getAuthIdentifier(),
            $this->getSessionGuard() => $this->auth()->getDefaultDriver(),
        ]);
    }
}
