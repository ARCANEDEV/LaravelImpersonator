<?php

declare(strict_types=1);

namespace Arcanedev\LaravelImpersonator\Tests;

use Arcanedev\LaravelImpersonator\Events\{ImpersonationStarted, ImpersonationStopped};
use Illuminate\Auth\Events\{Login, Logout};
use Illuminate\Support\Facades\Event;

/**
 * Class     EventsTest
 *
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
    public function it_must_dispatches_events_when_starting_and_stopping_impersonations(): void
    {
        $admin = $this->getAdminUser();
        $user  = $this->getRegularUser();

        static::assertTrue($admin->impersonate($user));
        static::assertTrue($user->stopImpersonation());

        static::assertImpersonationStartedEventDispatched($admin, $user);
        static::assertLoginEventNotDispatched();

        static::assertImpersonationStoppedEventDispatched($admin, $user);
        static::assertLogoutEventNotDispatched();
    }

    /* -----------------------------------------------------------------
     |  Custom Assertions
     | -----------------------------------------------------------------
     */

    protected static function assertLoginEventNotDispatched(): void
    {
        Event::assertNotDispatched(Login::class);
    }

    private static function assertLogoutEventNotDispatched(): void
    {
        Event::assertNotDispatched(Logout::class);
    }

    protected static function assertImpersonationStartedEventDispatched($impersonater, $impersonated): void
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
        return $this->getUserById(1);
    }

    /**
     * Get the regular user.
     *
     * @return \Arcanedev\LaravelImpersonator\Tests\Stubs\Models\User|mixed
     */
    protected function getRegularUser()
    {
        return $this->getUserById(2);
    }
}
