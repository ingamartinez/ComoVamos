<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Collection;
use PhpParser\Builder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable
{
    use Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $dates = ['deleted_at'];

    public function circuitos()
    {
        return $this->belongsToMany('App\Models\Circuito','users_has_circuitos','users_id','circuitos_id')->withTimestamps();;
    }

    public function cuota()
    {
        return $this->hasMany('App\Models\Cuota','users_id');
    }

    public function asesor()
    {
        return $this->hasMany('App\Models\User','users_id');
    }

    public function supervisor()
    {
        return $this->belongsTo('App\Models\User','users_id');
    }

    public function sucursal()
    {
        return $this->belongsTo('App\Models\Sucursal','sucursales_id');
    }

    public function scopeRoleById($query, $roles)
    {
        if ($roles instanceof Collection) {
            $roles = $roles->all();
        }

        if (! is_array($roles)) {
            $roles = [$roles];
        }

        $roles = array_map(function ($role) {
            if ($role instanceof Role) {
                return $role;
            }

            return app(Role::class)->findById($role, $this->getDefaultGuardName());
        }, $roles);

        return $query->whereHas('roles', function ($query) use ($roles) {
            $query->where(function ($query) use ($roles) {
                foreach ($roles as $role) {
                    $query->orWhere(config('permission.table_names.roles').'.id', $role->id);
                }
            });
        });
    }



}
