<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    protected $table = "sucursales";

    public function users()
    {
        return $this->hasMany('App\Models\User','sucursales_id');
    }
}
