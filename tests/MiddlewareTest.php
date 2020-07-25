<?php

declare(strict_types=1);

namespace Arcanedev\LaravelImpersonator\Tests;

/**
 * Class     MiddlewareTest
 *
 * @package  Arcanedev\LaravelImpersonator\Tests
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class MiddlewareTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_can_block_the_access_to_secured_routes_while_impersonating(): void
    {
        $this->loginWithId(1);

        $response = $this->get(route('admin::dashboard'));

        $response->assertSuccessful();
        $response->assertSee('Dashboard page');

        $this->get(route('auth::impersonator.start', [2]));

        $response = $this->get(route('admin::dashboard'));

        $response->assertStatus(302);

        $this->get(route('auth::impersonator.stop'));

        $response = $this->get(route('admin::dashboard'));

        $response->assertSuccessful();
        $response->assertSee('Dashboard page');
    }
}
