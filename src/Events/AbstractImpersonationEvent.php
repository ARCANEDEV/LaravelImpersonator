<?php

declare(strict_types=1);

namespace Arcanedev\LaravelImpersonator\Events;

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

    use Dispatchable,
        InteractsWithSockets,
        SerializesModels;

    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var \Arcanedev\LaravelImpersonator\Contracts\Impersonatable */
    public $impersonater;

    /** @var \Arcanedev\LaravelImpersonator\Contracts\Impersonatable */
    public $impersonated;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * AbstractImpersonationEvent constructor.
     *
     * @param  \Arcanedev\LaravelImpersonator\Contracts\Impersonatable|mixed  $impersonater
     * @param  \Arcanedev\LaravelImpersonator\Contracts\Impersonatable|mixed  $impersonated
     */
    public function __construct(Impersonatable $impersonater, Impersonatable $impersonated)
    {
        $this->impersonater = $impersonater;
        $this->impersonated = $impersonated;
    }
}
