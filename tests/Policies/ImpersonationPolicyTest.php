<?php

declare(strict_types=1);

namespace Arcanedev\LaravelImpersonator\Tests\Policies;

use Arcanedev\LaravelImpersonator\Policies\ImpersonationPolicy;
use Arcanedev\LaravelImpersonator\Tests\TestCase;

/**
 * Class     ImpersonationPolicyTest
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class ImpersonationPolicyTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_can_retrieve_policy_keys(): void
    {
        $expectations = [
            'can-impersonate'     => 'auth::impersonator.can-impersonate',
            'can-be-impersonated' => 'auth::impersonator.can-be-impersonated',
        ];

        foreach ($expectations as $ability => $expected) {
            static::assertSame($expected, ImpersonationPolicy::ability($ability));
        }
    }
}
