<?php

if ( ! function_exists('impersonator')) {
    /**
     * Get the impersonator instance.
     *
     * @return \Arcanedev\LaravelImpersonator\Contracts\Impersonator
     */
    function impersonator() {
        return app(Arcanedev\LaravelImpersonator\Contracts\Impersonator::class);
    }
}
