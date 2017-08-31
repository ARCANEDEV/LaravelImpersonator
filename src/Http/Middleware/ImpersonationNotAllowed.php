<?php namespace Arcanedev\LaravelImpersonator\Http\Middleware;

use Arcanedev\LaravelImpersonator\Contracts\Impersonator;
use Closure;

/**
 * Class     ImpersonationNotAllowed
 *
 * @package  Arcanedev\LaravelImpersonator\Http\Middleware
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class ImpersonationNotAllowed
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanedev\LaravelImpersonator\Contracts\Impersonator */
    protected $impersonator;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * ImpersonationNotAllowed constructor.
     *
     * @param  \Arcanedev\LaravelImpersonator\Contracts\Impersonator  $impersonator
     */
    public function __construct(Impersonator $impersonator)
    {
        $this->impersonator = $impersonator;
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Handle an incoming request.
     *
     * @param   \Illuminate\Http\Request  $request
     * @param   \Closure                  $next
     *
     * @return  mixed
     */
    public function handle($request, Closure $next)
    {
        return $this->impersonator->isImpersonating()
            ? redirect()->back()
            : $next($request);
    }
}
