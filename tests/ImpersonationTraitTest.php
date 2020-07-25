<?php

declare(strict_types=1);

namespace Arcanedev\LaravelImpersonator\Tests;

use Arcanedev\LaravelImpersonator\Tests\Stubs\Models\User;

/**
 * Class     ImpersonationTraitTest
 *
 * @package  Arcanedev\LaravelImpersonator\Tests
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class ImpersonationTraitTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_can_impersonate(): void
    {
        $admin = $this->loginWithId(1);

        static::assertTrue($admin->canImpersonate());
    }

    /** @test */
    public function it_can_not_impersonate(): void
    {
        $user = $this->loginWithId(2);

        static::assertFalse($user->canImpersonate());
    }

    /** @test */
    public function it_can_be_impersonated(): void
    {
        $user = $this->loginWithId(2);

        static::assertTrue($user->canBeImpersonated());
    }

    /** @test */
    public function it_can_not_be_impersonated(): void
    {
        $admin = $this->loginWithId(1);

        static::assertFalse($admin->canBeImpersonated());
    }

    /** @test */
    public function it_can_start_and_stop_the_impersonation(): void
    {
        /** @var \Arcanedev\LaravelImpersonator\Tests\Stubs\Models\User $admin */
        $admin = $this->loginWithId(1);

        static::assertFalse($admin->isImpersonated());

        /** @var \Arcanedev\LaravelImpersonator\Tests\Stubs\Models\User $user */
        $user = User::query()->findOrFail(2);

        $admin->impersonate($user);

        static::assertTrue($user->isImpersonated());
        static::assertSame($user->id, $this->getAuthenticatedUser()->getKey());
        static::assertSame($admin->id, $this->impersonator()->getImpersonatorId());

        $admin->stopImpersonation();

        static::assertFalse($user->isImpersonated());
        static::assertSame($admin->id, $this->getAuthenticatedUser()->getKey());
        static::assertNotSame($user->id, $this->getAuthenticatedUser()->getKey());
    }
}
