<?php namespace Arcanedev\LaravelImpersonator\Tests\Stubs\Models;

use Arcanedev\LaravelImpersonator\Contracts\Impersonatable;
use Arcanedev\LaravelImpersonator\Traits\CanImpersonate;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Class     User
 *
 * @package  Arcanedev\LaravelImpersonator\Tests\Stubs\Models
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @property  int             id
 * @property  string          name
 * @property  string          email
 * @property  string          password
 * @property  string          remember_token
 * @property  string          created_at
 * @property  \Carbon\Carbon  updated_at
 */
class User extends Authenticatable implements Impersonatable
{
    /* -----------------------------------------------------------------
     |  Traits
     | -----------------------------------------------------------------
     */

    use Notifiable, CanImpersonate;

    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * @var array
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /* -----------------------------------------------------------------
     |  Check Methods
     | -----------------------------------------------------------------
     */

    /**
     * Check if the current modal can impersonate other models.
     *
     * @return  bool
     */
    public function canImpersonate()
    {
        return $this->isAdmin();
    }

    /**
     * Check if the current model can be impersonated.
     *
     * @return  bool
     */
    public function canBeImpersonated()
    {
        return ! $this->canImpersonate();
    }

    /**
     * Check if the user is admin.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return in_array($this->attributes['email'], [
            'admin1@example.com', 'admin2@example.com'
        ]);
    }
}
