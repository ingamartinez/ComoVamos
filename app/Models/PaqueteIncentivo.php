<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class PaqueteIncentivo extends Model
{
    use SoftDeletes;

    protected $table = "paquetes_incentivos";

    protected $fillable=["validado_sistema"];

    protected $dates = ["deleted_at","date_last_update","date_first_call","fecha_paquete"];

    public function dms()
    {
        return $this->belongsTo('App\Models\DMS','dms_idpdv');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User','users_id');
    }

}
