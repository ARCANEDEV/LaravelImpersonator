<?php

declare(strict_types=1);

namespace Arcanedev\LaravelImpersonator\Tests\Stubs\Controllers;

use Arcanedev\LaravelImpersonator\Contracts\Impersonator;
use Arcanedev\LaravelImpersonator\Policies\ImpersonationPolicy;
use Arcanedev\LaravelImpersonator\Tests\Stubs\Models\User;
use Arcanedev\Support\Http\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * Class     ImpersonatorController
 *
 * @package  Arcanedev\LaravelImpersonator\Tests\Stubs\Controllers
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class ImpersonatorController extends Controller
{
    /* -----------------------------------------------------------------
     |  Traits
     | -----------------------------------------------------------------
     */

    use AuthorizesRequests;

    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var \Arcanedev\LaravelImpersonator\Contracts\Impersonator */
    private $impersonator;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * ImpersonatorController constructor.
     *
     * @param  \Arcanedev\LaravelImpersonator\Contracts\Impersonator  $impersonator
     */
    public function __construct(Impersonator $impersonator)
    {
        parent::__construct();

        $this->middleware('auth');

        $this->impersonator = $impersonator;
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    public function start($id)
    {
        $impersonated = User::findOrFail($id);

        $this->authorize(ImpersonationPolicy::CAN_BE_IMPERSONATED, [$impersonated]);

        return $this->impersonator->start(auth()->user(), $impersonated)
            ? 'Impersonation started'
            : 'Impersonation failed';
    }

    public function stop()
    {
        if ( ! $this->impersonator->isImpersonating()) {
            return redirect()->back();
        }

        return $this->impersonator->stop()
            ? 'Impersonation stopped'
            : 'Impersonation failed';
    }
}
