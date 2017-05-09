<?php namespace Arcanedev\LaravelImpersonator\Events;

use Arcanedev\LaravelImpersonator\Contracts\Impersonatable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class     AbstractImpersonationEvent
 *
 * @package  Arcanedev\LaravelImpersonator\Events
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
abstract class AbstractImpersonationEvent
{
    /* -----------------------------------------------------------------
     |  Traits
     | -----------------------------------------------------------------
     */

    use Dispatchable, InteractsWithSockets, SerializesModels;

    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var \Arcanedev\LaravelImpersonator\Contracts\Impersonatable */
    public $impersonator;

    /** @var \Arcanedev\LaravelImpersonator\Contracts\Impersonatable */
    public $impersonated;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * AbstractImpersonationEvent constructor.
     *
     * @param  \Arcanedev\LaravelImpersonator\Contracts\Impersonatable  $impersonator
     * @param  \Arcanedev\LaravelImpersonator\Contracts\Impersonatable  $impersonated
     */
    public function __construct(Impersonatable $impersonator, Impersonatable $impersonated)
    {
        $this->impersonator = $impersonator;
        $this->impersonated = $impersonated;
    }
}
