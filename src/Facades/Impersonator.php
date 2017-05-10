<?php namespace Arcanedev\LaravelImpersonator\Facades;

use Arcanedev\LaravelImpersonator\Contracts\Impersonator as ImpersonatorContract;
use Illuminate\Support\Facades\Facade;

/**
 * Class     Impersonator
 *
 * @package  Arcanedev\LaravelImpersonator\Facades
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class Impersonator extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return ImpersonatorContract::class; }
}
