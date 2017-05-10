<?php namespace Arcanedev\LaravelImpersonator\Tests;

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

    protected function setUp()
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

        $this->assertTrue($admin->impersonate($user));
        $this->assertTrue($user->stopImpersonation());

        $this->assertImpersonationStartedEventDispatched($admin, $user);
        $this->assertLoginEventNotDispatched();

        $this->assertImpersonationStoppedEventDispatched($admin, $user);
        $this->assertLogoutEventNotDispatched();
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get the admin user.
     *
     * @return \Arcanedev\LaravelImpersonator\Tests\Stubs\Models\User
     */
    protected function getAdminUser()
    {
        return User::find(1);
    }

    /**
     * Get the regular user.
     *
     * @return \Arcanedev\LaravelImpersonator\Tests\Stubs\Models\User
     */
    protected function getRegularUSer()
    {
        return User::find(2);
    }

    protected function assertLoginEventNotDispatched()
    {
        Event::assertNotDispatched(Login::class);
    }

    private function assertLogoutEventNotDispatched()
    {
        Event::assertNotDispatched(Logout::class);
    }

    protected function assertImpersonationStartedEventDispatched($impersonater, $impersonated)
    {
        Event::assertDispatched(ImpersonationStarted::class, function ($event) use ($impersonater, $impersonated) {
            return $event->impersonater->id == $impersonater->id && $event->impersonated->id == $impersonated->id;
        });
    }

    protected function assertImpersonationStoppedEventDispatched($impersonater, $impersonated)
    {
        Event::assertDispatched(ImpersonationStopped::class, function ($event) use ($impersonater, $impersonated) {
            return $event->impersonater->id == $impersonater->id && $event->impersonated->id == $impersonated->id;
        });
    }
}
