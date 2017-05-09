<?php namespace Arcanedev\LaravelImpersonator\Tests;

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

        $this->assertTrue($admin->canImpersonate());
    }

    /** @test */
    public function it_can_not_impersonate()
    {
        $user = $this->loginWithId(2);

        $this->assertFalse($user->canImpersonate());
    }

    /** @test */
    public function it_can_be_impersonated()
    {
        $user = $this->loginWithId(2);

        $this->assertTrue($user->canBeImpersonated());
    }

    /** @test */
    public function it_can_not_be_impersonated()
    {
        $admin = $this->loginWithId(1);

        $this->assertFalse($admin->canBeImpersonated());
    }

    /** @test */
    public function it_can_start_and_stop_the_impersonation()
    {
        /** @var \Arcanedev\LaravelImpersonator\Tests\Stubs\Models\User $admin */
        $admin = $this->loginWithId(1);

        $this->assertFalse($admin->isImpersonated());

        /** @var \Arcanedev\LaravelImpersonator\Tests\Stubs\Models\User $user */
        $user = $this->impersonator()->findUserById(2);

        $admin->impersonate($user);

        $this->assertTrue($user->isImpersonated());
        $this->assertSame($user->id, $this->getAuthenticatedUser()->getKey());
        $this->assertSame($admin->id, $this->impersonator()->getImpersonatorId());

        $admin->stopImpersonation();

        $this->assertFalse($user->isImpersonated());
        $this->assertSame($admin->id, $this->getAuthenticatedUser()->getKey());
        $this->assertNotSame($user->id, $this->getAuthenticatedUser()->getKey());
    }
}
