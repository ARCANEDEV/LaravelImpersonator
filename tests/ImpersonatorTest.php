<?php namespace Arcanedev\LaravelImpersonator\Tests;

use Arcanedev\LaravelImpersonator\Tests\Stubs\Models\User;

/**
 * Class     ImpersonatorTest
 *
 * @package  Arcanedev\LaravelImpersonator\Tests
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class ImpersonatorTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanedev\LaravelImpersonator\Contracts\Impersonator */
    protected $impersonator;

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    protected function setUp()
    {
        parent::setUp();

        $this->impersonator = $this->app->make(\Arcanedev\LaravelImpersonator\Contracts\Impersonator::class);
    }

    protected function tearDown()
    {
        unset($this->impersonator);

        parent::tearDown();
    }

    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_can_be_instantiated()
    {
        $expectations = [
            \Arcanedev\LaravelImpersonator\Contracts\Impersonator::class,
            \Arcanedev\LaravelImpersonator\Impersonator::class,
        ];

        foreach ($expectations as $expected) {
            $this->assertInstanceOf($expected, $this->impersonator);
        }
    }

    /** @test */
    public function it_can_find_a_user()
    {
        $admin = $this->impersonator->findUserById(1);
        $user  = $this->impersonator->findUserById(2);

        $this->assertInstanceOf(User::class, $admin);
        $this->assertInstanceOf(User::class, $user);

        $this->assertSame('Admin 1',  $admin->name);
        $this->assertSame('User 1', $user->name);
    }

    /** @test */
    public function it_can_check_is_impersonating()
    {
        $this->assertFalse($this->impersonator->isImpersonating());

        $this->app['session']->put($this->impersonator->getSessionKey(), 1);

        $this->assertTrue($this->impersonator->isImpersonating());
        $this->assertSame(1, $this->impersonator->getImpersonatorId());
    }

    /** @test */
    public function it_can_clear_impersonating()
    {
        $this->app['session']->put($this->impersonator->getSessionKey(), 1);

        $this->assertTrue($this->app['session']->has($this->impersonator->getSessionKey()));

        $this->impersonator->clear();

        $this->assertFalse($this->app['session']->has($this->impersonator->getSessionKey()));
    }

    /** @test */
    public function it_can_start_impersonation()
    {
        $this->loginWithId(1);

        $this->assertIsLoggedIn();

        $this->impersonator->start(
            $this->getAuthenticatedUser(),
            $this->impersonator->findUserById(2)
        );

        $this->assertSame(2, $this->getAuthenticatedUser()->getKey());
        $this->assertSame(1, $this->impersonator->getImpersonatorId());
        $this->assertTrue($this->impersonator->isImpersonating());
    }

    /** @test */
    public function it_can_stop_impersonation()
    {
        $this->loginWithId(1);

        $this->impersonator->start(
            $this->getAuthenticatedUser(),
            $this->impersonator->findUserById(2)
        );

        $this->assertIsLoggedIn();
        $this->assertTrue($this->impersonator->isImpersonating());
        $this->assertSame(2, $this->getAuthenticatedUser()->getKey());

        $this->assertTrue($this->impersonator->stop());

        $this->assertIsLoggedIn();
        $this->assertFalse($this->impersonator->isImpersonating());
        $this->assertSame(1, $this->getAuthenticatedUser()->getKey());
    }

    /** @test */
    public function it_must_preserve_the_remember_token_when_starting_and_stopping_impersonation()
    {
        /** @var  \Arcanedev\LaravelImpersonator\Tests\Stubs\Models\User  $admin */
        $admin = $this->impersonator->findUserById(1);
        $admin->remember_token = 'impersonator_token';
        $admin->save();

        /** @var  \Arcanedev\LaravelImpersonator\Tests\Stubs\Models\User  $user */
        $user = $this->impersonator->findUserById(2);
        $user->remember_token = 'impersonated_token';
        $user->save();

        $admin->impersonate($user);
        $user->stopImpersonation();

        $user->fresh();
        $admin->fresh();

        $this->assertEquals('impersonator_token', $admin->remember_token);
        $this->assertEquals('impersonated_token', $user->remember_token);
    }

    /**
     * @test
     *
     * @expectedException         \Arcanedev\LaravelImpersonator\Exceptions\ImpersonationException
     * @expectedExceptionMessage  The impersonator with `id`=[2] doesn't have the ability to impersonate.
     */
    public function it_must_throw_exception_if_impersonator_cannot_impersonate()
    {
        $this->loginWithId(2);

        $this->impersonator->start(
            $this->getAuthenticatedUser(),
            $this->impersonator->findUserById(3)
        );
    }

    /**
     * @test
     *
     * @expectedException         \Arcanedev\LaravelImpersonator\Exceptions\ImpersonationException
     * @expectedExceptionMessage  The impersonator & impersonated with must be different.
     */
    public function it_must_throw_exception_if_impersonator_and_impersonated_are_same()
    {
        $this->loginWithId(1);

        $this->impersonator->start(
            $this->getAuthenticatedUser(),
            $this->impersonator->findUserById(1)
        );
    }

    /**
     * @test
     *
     * @expectedException         \Arcanedev\LaravelImpersonator\Exceptions\ImpersonationException
     * @expectedExceptionMessage  The impersonated with `id`=[4] cannot be impersonated.
     */
    public function it_must_throw_exception_if_impersonated_cannot_be_impersonated() // WHAT ??
    {
        $this->loginWithId(1);

        $this->impersonator->start(
            $this->getAuthenticatedUser(),
            $this->impersonator->findUserById(4)
        );
    }

    /**
     * @test
     *
     * @expectedException         \Arcanedev\LaravelImpersonator\Exceptions\ImpersonationException
     * @expectedExceptionMessage  The impersonation is disabled.
     */
    public function it_must_throw_exception_if_impersonator_is_disabled()
    {
        $this->app['config']->set('impersonator.enabled', false);

        $this->loginWithId(1);

        $this->impersonator->start(
            $this->getAuthenticatedUser(),
            $this->impersonator->findUserById(2)
        );
    }
}
