<?php namespace Arcanedev\LaravelImpersonator\Tests;

use Arcanedev\LaravelImpersonator\Tests\Stubs\Models\User;
use Arcanedev\LaravelImpersonator\Contracts\Impersonator as ImpersonatorContract;

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

    protected function setUp(): void
    {
        parent::setUp();

        $this->impersonator = $this->app->make(ImpersonatorContract::class);
    }

    protected function tearDown(): void
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
            ImpersonatorContract::class,
            \Arcanedev\LaravelImpersonator\Impersonator::class,
        ];

        foreach ($expectations as $expected) {
            static::assertInstanceOf($expected, $this->impersonator);
        }
    }

    /** @test */
    public function it_can_be_instantiated_with_helper()
    {
        $impersonator = impersonator();

        $expectations = [
            ImpersonatorContract::class,
            \Arcanedev\LaravelImpersonator\Impersonator::class,
        ];

        foreach ($expectations as $expected) {
            static::assertInstanceOf($expected, $impersonator);
        }
    }

    /** @test */
    public function it_can_find_a_user()
    {
        /** @var \Arcanedev\LaravelImpersonator\Tests\Stubs\Models\User  $admin */
        $admin = $this->impersonator->findUserById(1);
        /** @var \Arcanedev\LaravelImpersonator\Tests\Stubs\Models\User  $user */
        $user  = $this->impersonator->findUserById(2);

        static::assertInstanceOf(User::class, $admin);
        static::assertInstanceOf(User::class, $user);

        static::assertSame('Admin 1',  $admin->name);
        static::assertSame('User 1', $user->name);
    }

    /** @test */
    public function it_can_check_is_impersonating()
    {
        static::assertFalse($this->impersonator->isImpersonating());

        $this->app['session']->put($this->impersonator->getSessionKey(), 1);

        static::assertTrue($this->impersonator->isImpersonating());
        static::assertSame(1, $this->impersonator->getImpersonatorId());
    }

    /** @test */
    public function it_can_clear_impersonating()
    {
        $this->app['session']->put($this->impersonator->getSessionKey(), 1);

        static::assertTrue($this->app['session']->has($this->impersonator->getSessionKey()));

        $this->impersonator->clear();

        static::assertFalse($this->app['session']->has($this->impersonator->getSessionKey()));
    }

    /** @test */
    public function it_can_start_impersonation()
    {
        $this->loginWithId(1);

        static::assertIsLoggedIn();

        $this->impersonator->start(
            $this->getAuthenticatedUser(),
            $this->impersonator->findUserById(2)
        );

        static::assertSame(2, $this->getAuthenticatedUser()->getKey());
        static::assertSame(1, $this->impersonator->getImpersonatorId());
        static::assertTrue($this->impersonator->isImpersonating());
    }

    /** @test */
    public function it_can_stop_impersonation()
    {
        $this->loginWithId(1);

        $this->impersonator->start(
            $this->getAuthenticatedUser(),
            $this->impersonator->findUserById(2)
        );

        static::assertIsLoggedIn();
        static::assertTrue($this->impersonator->isImpersonating());
        static::assertSame(2, $this->getAuthenticatedUser()->getKey());

        static::assertTrue($this->impersonator->stop());

        static::assertIsLoggedIn();
        static::assertFalse($this->impersonator->isImpersonating());
        static::assertSame(1, $this->getAuthenticatedUser()->getKey());
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

        static::assertEquals('impersonator_token', $admin->remember_token);
        static::assertEquals('impersonated_token', $user->remember_token);
    }

    /** @test */
    public function it_must_throw_exception_if_impersonater_cannot_impersonate()
    {
        $this->expectException(\Arcanedev\LaravelImpersonator\Exceptions\ImpersonationException::class);
        $this->expectExceptionMessage('The impersonater with `id`=[2] doesn\'t have the ability to impersonate.');

        $this->loginWithId(2);

        $this->impersonator->start(
            $this->getAuthenticatedUser(),
            $this->impersonator->findUserById(3)
        );
    }

    /** @test */
    public function it_must_throw_exception_if_impersonater_and_impersonated_are_same()
    {
        $this->expectException(\Arcanedev\LaravelImpersonator\Exceptions\ImpersonationException::class);
        $this->expectExceptionMessage('The impersonater & impersonated with must be different.');

        $this->loginWithId(1);

        $this->impersonator->start(
            $this->getAuthenticatedUser(),
            $this->impersonator->findUserById(1)
        );
    }

    /** @test */
    public function it_must_throw_exception_if_impersonated_cannot_be_impersonated() // WHAT ??
    {
        $this->expectException(\Arcanedev\LaravelImpersonator\Exceptions\ImpersonationException::class);
        $this->expectExceptionMessage('The impersonated with `id`=[4] cannot be impersonated.');

        $this->loginWithId(1);

        $this->impersonator->start(
            $this->getAuthenticatedUser(),
            $this->impersonator->findUserById(4)
        );
    }

    /** @test */
    public function it_must_throw_exception_if_impersonater_is_disabled()
    {
        $this->expectException(\Arcanedev\LaravelImpersonator\Exceptions\ImpersonationException::class);
        $this->expectExceptionMessage('The impersonation is disabled.');

        $this->disableImpersonations();

        $this->loginWithId(1);

        $this->impersonator->start(
            $this->getAuthenticatedUser(),
            $this->impersonator->findUserById(2)
        );
    }

    /** @test */
    public function it_must_return_false_if_not_in_impersonating_mode()
    {
        static::assertFalse($this->impersonator->stop());
    }
}
