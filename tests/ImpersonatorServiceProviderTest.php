<?php

declare(strict_types=1);

namespace Arcanedev\LaravelImpersonator\Tests;

use Arcanedev\LaravelImpersonator\Contracts\Impersonator as ImpersonatorContract;
use Arcanedev\LaravelImpersonator\ImpersonatorServiceProvider;
use Arcanedev\Support\Providers\PackageServiceProvider;
use Arcanedev\Support\Providers\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

/**
 * Class     ImpersonatorServiceProviderTest
 *
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
    public function it_can_be_instantiated(): void
    {
        $expectations = [
            IlluminateServiceProvider::class,
            DeferrableProvider::class,
            ServiceProvider::class,
            PackageServiceProvider::class,
            ImpersonatorServiceProvider::class,
        ];

        foreach ($expectations as $expected) {
            static::assertInstanceOf($expected, $this->provider);
        }
    }

    /** @test */
    public function it_can_provides(): void
    {
        $expected = [
            ImpersonatorContract::class,
        ];

        static::assertSame($expected, $this->provider->provides());
    }
}
