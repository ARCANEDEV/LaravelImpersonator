<?php

declare(strict_types=1);

namespace Arcanedev\LaravelImpersonator\Tests;

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
    public function it_can_impersonate()
    {
        $admin = $this->loginWithId(1);

        static::assertTrue($admin->canImpersonate());
    }

    /** @test */
    public function it_can_not_impersonate()
    {
        $user = $this->loginWithId(2);

        static::assertFalse($user->canImpersonate());
    }

    /** @test */
    public function it_can_be_impersonated()
    {
        $user = $this->loginWithId(2);

        static::assertTrue($user->canBeImpersonated());
    }

    /** @test */
    public function it_can_not_be_impersonated()
    {
        $admin = $this->loginWithId(1);

        static::assertFalse($admin->canBeImpersonated());
    }

    /** @test */
    public function it_can_start_and_stop_the_impersonation()
    {
        /** @var \Arcanedev\LaravelImpersonator\Tests\Stubs\Models\User $admin */
        $admin = $this->loginWithId(1);

        static::assertFalse($admin->isImpersonated());

        /** @var \Arcanedev\LaravelImpersonator\Tests\Stubs\Models\User $user */
        $user = $this->impersonator()->findUserById(2);

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
