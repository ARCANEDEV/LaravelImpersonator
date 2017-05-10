<?php namespace Arcanedev\LaravelImpersonator\Tests;

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

        $response = $this->get(route('auth::impersonator.start', [2]));

        $response->assertSuccessful();
        $response->assertSessionHas('impersonator_id');
        $response->assertSeeText('Impersonation started');
    }

    /** @test */
    public function it_can_deny_access_to_impersonator()
    {
        $this->loginWithId(2);

        $response = $this->get(route('auth::impersonator.start', [3]));

        $response->assertStatus(403);
    }

    /** @test */
    public function it_can_deny_access_if_impersonated_can_not_be_impersonated()
    {
        $this->loginWithId(1);

        $response = $this->get(route('auth::impersonator.start', [4]));

        $response->assertStatus(403);
    }

    /** @test */
    public function it_can_stop_ongoing_impersonation()
    {
        $this->loginWithId(1);

        $response = $this->get(route('auth::impersonator.start', [2]));

        $response->assertSuccessful();
        $response->assertSessionHas('impersonator_id');
        $response->assertSeeText('Impersonation started');

        $response = $this->get(route('auth::impersonator.stop'));

        $response->assertSuccessful();
        $response->assertSessionMissing('impersonator_id');
        $response->assertSeeText('Impersonation stopped');
    }

    /** @test */
    public function it_can_redirect_if_impersonation_not_started()
    {
        $this->loginWithId(1);

        $response = $this->get(route('auth::impersonator.stop'));

        $response->assertStatus(302);
    }

    /** @test */
    public function it_can_deny_access_if_impersonation_is_disabled()
    {
        $this->disableImpersonations();

        $this->loginWithId(1);

        $response = $this->get(route('auth::impersonator.start', [2]));

        $response->assertStatus(403);
    }
}
