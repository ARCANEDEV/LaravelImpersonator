<?php

declare(strict_types=1);

namespace Arcanedev\LaravelImpersonator\Tests;

use Arcanedev\LaravelImpersonator\ImpersonatorServiceProvider;

/**
 * Class     ImpersonatorServiceProviderTest
 *
 * @package  Arcanedev\LaravelImpersonator\Tests
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class ImpersonatorServiceProviderTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanedev\LaravelImpersonator\ImpersonatorServiceProvider */
    protected $provider;

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    protected function setUp(): void
    {
        parent::setUp();

        $this->provider = $this->app->getProvider(ImpersonatorServiceProvider::class);
    }

    protected function tearDown(): void
    {
        unset($this->provider);

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
            \Illuminate\Support\ServiceProvider::class,
            \Illuminate\Contracts\Support\DeferrableProvider::class,
            \Arcanedev\Support\Providers\ServiceProvider::class,
            \Arcanedev\Support\Providers\PackageServiceProvider::class,
            \Arcanedev\LaravelImpersonator\ImpersonatorServiceProvider::class,
        ];

        foreach ($expectations as $expected) {
            static::assertInstanceOf($expected, $this->provider);
        }
    }

    /** @test */
    public function it_can_provides()
    {
        $expected = [
            \Arcanedev\LaravelImpersonator\Contracts\Impersonator::class,
        ];

        static::assertSame($expected, $this->provider->provides());
    }
}
