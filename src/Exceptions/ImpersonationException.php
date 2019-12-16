<?php namespace Arcanedev\LaravelImpersonator\Exceptions;

use Arcanedev\LaravelImpersonator\Contracts\Impersonatable;

/**
 * Class     ImpersonationException
 *
 * @package  Arcanedev\LaravelImpersonator\Exceptions
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class ImpersonationException extends \Exception
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Make a new exception.
     *
     * @param  string  $message
     *
     * @return static
     */
    public static function make(string $message): self
    {
        return new static($message);
    }

    /**
     * Make an exception when the impersonater and impersonated are same person.
     *
     * @return static
     */
    public static function selfImpersonation(): self
    {
        return static::make('The impersonater & impersonated with must be different.');
    }

    /**
     * Make an exception when the impersonater cannot (or not allowed) impersonate.
     *
     * @param  \Arcanedev\LaravelImpersonator\Contracts\Impersonatable  $impersonater
     *
     * @return static
     */
    public static function cannotImpersonate(Impersonatable $impersonater): self
    {
        return static::make(
            __("The impersonater with `:impersonator_name`=[:impersonator_id] doesn't have the ability to impersonate.", [
                'impersonator_name' => $impersonater->getAuthIdentifierName(),
                'impersonator_id'   => $impersonater->getAuthIdentifier(),
            ])
        );
    }

    /**
     * Make an exception when the impersonated cannot be impersonated.
     *
     * @param  \Arcanedev\LaravelImpersonator\Contracts\Impersonatable  $impersonated
     *
     * @return static
     */
    public static function cannotBeImpersonated(Impersonatable $impersonated)
    {
        return static::make(
            __('The impersonated with `:impersonated_name`=[:impersonated_id] cannot be impersonated.', [
                'impersonated_name' => $impersonated->getAuthIdentifierName(),
                'impersonated_id' => $impersonated->getAuthIdentifier()
            ])
        );
    }

    /**
     * Make an exception when the impersonator and the impersonated are the same person.
     *
     * @return static
     */
    public static function impersonaterAndImpersonatedAreSame()
    {
        return static::make(
            __('The impersonater & impersonated with must be different.')
        );
    }
}
