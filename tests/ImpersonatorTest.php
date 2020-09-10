<?php

declare(strict_types=1);

namespace Arcanedev\LaravelImpersonator\Tests;

use Arcanedev\LaravelImpersonator\Contracts\Impersonator as ImpersonatorContract;
use Arcanedev\LaravelImpersonator\Exceptions\ImpersonationException;
use Arcanedev\LaravelImpersonator\Impersonator;

/**
 * Class     ImpersonatorTest
 *
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
    public function it_can_be_instantiated(): void
    {
        $expectations = [
            ImpersonatorContract::class,
            Impersonator::class,
        ];

        foreach ($expectations as $expected) {
            static::assertInstanceOf($expected, $this->impersonator);
        }
    }

    /** @test */
    public function it_can_be_instantiated_with_helper(): void
    {
        $impersonator = impersonator();

        $expectations = [
            ImpersonatorContract::class,
            Impersonator::class,
        ];

        foreach ($expectations as $expected) {
            static::assertInstanceOf($expected, $impersonator);
        }
    }

    /** @test */
    public function it_can_check_is_impersonating(): void
    {
        static::assertFalse($this->impersonator->isImpersonating());

        $this->app['session']->put($this->impersonator->getSessionKey(), 1);
        $this->app['session']->put($this->impersonator->getSessionGuard(), 'custom');

        static::assertTrue($this->impersonator->isImpersonating());
        static::assertSame(1, $this->impersonator->getImpersonatorId());
        static::assertSame('custom', $this->impersonator->getImpersonatorGuard());
    }

    /** @test */
    public function it_can_clear_impersonating(): void
    {
        $this->app['session']->put($this->impersonator->getSessionKey(), 1);

        static::assertTrue($this->app['session']->has($this->impersonator->getSessionKey()));

        $this->impersonator->clear();

        static::assertFalse($this->app['session']->has($this->impersonator->getSessionKey()));
    }

    /** @test */
    public function it_can_start_impersonation(): void
    {
        $impersonator = $this->loginWithId($impersonatorId = 1);

        static::assertIsLoggedIn();

        $impersonated = $this->getUserById($impersonatedId = 2);

        $this->impersonator->start($impersonator, $impersonated);

        static::assertSame($impersonatedId, $this->getAuthenticatedUser()->getKey());
        static::assertSame($impersonatorId, $this->impersonator->getImpersonatorId());
        static::assertTrue($this->impersonator->isImpersonating());
    }

    /** @test */
    public function it_can_stop_impersonation(): void
    {
        $this->loginWithId($impersonatorId = 1);

        $impersonated = $this->getUserById($impersonatedId = 2);

        $this->impersonator->start($this->getAuthenticatedUser(), $impersonated);

        static::assertIsLoggedIn();
        static::assertTrue($this->impersonator->isImpersonating());
        static::assertSame($impersonatedId, $this->getAuthenticatedUser()->getKey());

        static::assertTrue($this->impersonator->stop());

        static::assertIsLoggedIn();
        static::assertFalse($this->impersonator->isImpersonating());
        static::assertSame($impersonatorId, $this->getAuthenticatedUser()->getKey());
    }

    /** @test */
    public function it_must_preserve_the_remember_token_when_starting_and_stopping_impersonation(): void
    {
        /** @var  \Arcanedev\LaravelImpersonator\Tests\Stubs\Models\User  $admin */
        $admin = $this->getUserById(1);
        $admin->forceFill(['remember_token' => 'impersonator_token'])->save();

        /** @var  \Arcanedev\LaravelImpersonator\Tests\Stubs\Models\User  $user */
        $user = $this->getUserById(2);
        $user->forceFill(['remember_token' => 'impersonated_token'])->save();

        $admin->impersonate($user);
        $user->stopImpersonation();

        $user->fresh();
        $admin->fresh();

        static::assertEquals('impersonator_token', $admin->remember_token);
        static::assertEquals('impersonated_token', $user->remember_token);
    }

    /** @test */
    public function it_must_throw_exception_if_impersonater_cannot_impersonate(): void
    {
        $this->expectException(ImpersonationException::class);
        $this->expectExceptionMessage('The impersonater with `id`=[2] doesn\'t have the ability to impersonate.');

        $this->loginWithId(2);

        $this->impersonator->start(
            $this->getAuthenticatedUser(),
            $this->getUserById(3)
        );
    }

    /** @test */
    public function it_must_throw_exception_if_impersonater_and_impersonated_are_same(): void
    {
        $this->expectException(ImpersonationException::class);
        $this->expectExceptionMessage('The impersonater & impersonated with must be different.');

        $this->loginWithId(1);

        $this->impersonator->start(
            $this->getAuthenticatedUser(),
            $this->getUserById(1)
        );
    }

    /** @test */
    public function it_must_throw_exception_if_impersonated_cannot_be_impersonated(): void // WHAT ??
    {
        $this->expectException(ImpersonationException::class);
        $this->expectExceptionMessage('The impersonated with `id`=[4] cannot be impersonated.');

        $this->loginWithId(1);

        $this->impersonator->start(
            $this->getAuthenticatedUser(),
            $this->getUserById(4)
        );
    }

    /** @test */
    public function it_must_throw_exception_if_impersonater_is_disabled(): void
    {
        $this->expectException(ImpersonationException::class);
        $this->expectExceptionMessage('The impersonation is disabled.');

        $this->disableImpersonations();

        $this->loginWithId(1);

        $this->impersonator->start(
            $this->getAuthenticatedUser(),
            $this->getUserById(2)
        );
    }

    /** @test */
    public function it_must_return_false_if_not_in_impersonating_mode(): void
    {
        static::assertFalse($this->impersonator->stop());
    }
}
