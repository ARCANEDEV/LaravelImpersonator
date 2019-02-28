<?php namespace Arcanedev\LaravelImpersonator\Tests;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class     ImpersonationPoliciesTest
 *
 * @package  Arcanedev\LaravelImpersonator\Tests
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class ImpersonationPoliciesTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_can_allow_access_to_impersonator()
    {
        $this->loginWithId(1);

        $this->get(route('auth::impersonator.start', [2]))
             ->assertSuccessful()
             ->assertSessionHas('impersonator_id')
             ->assertSeeText('Impersonation started');
    }

    /** @test */
    public function it_can_deny_access_to_impersonator()
    {
        $this->loginWithId(2);

        $this->get(route('auth::impersonator.start', [3]))
             ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function it_can_deny_access_if_impersonated_can_not_be_impersonated()
    {
        $this->loginWithId(1);

        $this->get(route('auth::impersonator.start', [4]))
             ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function it_can_stop_ongoing_impersonation()
    {
        $this->loginWithId(1);

        $this->get(route('auth::impersonator.start', [2]))
             ->assertSuccessful()
             ->assertSessionHas('impersonator_id')
             ->assertSeeText('Impersonation started');

        $this->get(route('auth::impersonator.stop'))
             ->assertSuccessful()
             ->assertSessionMissing('impersonator_id')
             ->assertSeeText('Impersonation stopped');
    }

    /** @test */
    public function it_can_redirect_if_impersonation_not_started()
    {
        $this->loginWithId(1);

        $this->get(route('auth::impersonator.stop'))
             ->assertStatus(Response::HTTP_FOUND);
    }

    /** @test */
    public function it_can_deny_access_if_impersonation_is_disabled()
    {
        $this->disableImpersonations();

        $this->loginWithId(1);

        $this->get(route('auth::impersonator.start', [2]))
             ->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
