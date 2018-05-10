<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Circuito extends Model
{
    protected $table = "circuitos";

    public function users()
    {
        return $this->belongsToMany('App\Models\User','users_has_circuitos','users_id','circuitos_id')->withTimestamps();
    }

}