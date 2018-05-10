<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class PaqueteIncentivo extends Model
{
    use SoftDeletes;

    protected $table = "paquetes_incentivos";

    protected $dates = ["deleted_at","date_last_update","date_first_call","fecha_paquete"];


}
