<?php

declare(strict_types=1);

namespace Arcanedev\LaravelImpersonator\Tests;

use Arcanedev\LaravelImpersonator\Events\ImpersonationStopped;
use Arcanedev\LaravelImpersonator\Events\ImpersonationStarted;
use Arcanedev\LaravelImpersonator\Tests\Stubs\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;

/**
 * Class     EventsTest
 *
 * @package  Arcanedev\LaravelImpersonator\Tests
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class EventsTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();
    }

    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_must_dispatches_events_when_starting_and_stopping_impersonations()
    {
        $admin = $this->getAdminUser();
        $user  = $this->getRegularUSer();

        static::assertTrue($admin->impersonate($user));
        static::assertTrue($user->stopImpersonation());

        static::assertImpersonationStartedEventDispatched($admin, $user);
        static::assertLoginEventNotDispatched();

        static::assertImpersonationStoppedEventDispatched($admin, $user);
        static::assertLogoutEventNotDispatched();
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get the admin user.
     *
     * @return \Arcanedev\LaravelImpersonator\Tests\Stubs\Models\User|mixed
     */
    protected function getAdminUser()
    {
        return User::query()->find(1);
    }

    /**
     * Get the regular user.
     *
     * @return \Arcanedev\LaravelImpersonator\Tests\Stubs\Models\User|mixed
     */
    protected function getRegularUSer()
    {
        return User::query()->find(2);
    }

    protected static function assertLoginEventNotDispatched()
    {
        Event::assertNotDispatched(Login::class);
    }

    private static function assertLogoutEventNotDispatched()
    {
        Event::assertNotDispatched(Logout::class);
    }

    protected static function assertImpersonationStartedEventDispatched($impersonater, $impersonated)
    {
        Event::assertDispatched(ImpersonationStarted::class, function ($event) use ($impersonater, $impersonated) {
            return $event->impersonater->id == $impersonater->id && $event->impersonated->id == $impersonated->id;
        });
    }

    protected static function assertImpersonationStoppedEventDispatched($impersonater, $impersonated)
    {
        Event::assertDispatched(ImpersonationStopped::class, function ($event) use ($impersonater, $impersonated) {
            return $event->impersonater->id == $impersonater->id && $event->impersonated->id == $impersonated->id;
        });
    }
}
