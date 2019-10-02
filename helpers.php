<?php

use Arcanedev\LaravelImpersonator\Contracts\Impersonator;

if ( ! function_exists('impersonator')) {
    /**
     * Get the impersonator instance.
     *
     * @return \Arcanedev\LaravelImpersonator\Contracts\Impersonator
     */
    function impersonator(): Impersonator {
        return app(Impersonator::class);
    }
}
