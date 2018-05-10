<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dms extends Model
{
    protected $table="dms";

    protected $primaryKey="idpdv";

    protected $fillable = [
        'idpdv',
        'nombre_punto',
        'circuito',
        'telefono',
        'celular',
        'dueno',
        'documento',
        'ciudad',
        'barrio',
        'direccion',
        'lat',
        'long',
        'estado_dms',
        'fecha_creacion_dms',
        'fecha_modificacion_dms',
        'distribuidor',
        'moviles_epin',
        'cod_sub',
        'epin',
        'simcard',
        'mbox',
        'saldo',
        'fecha_saldo',
        'tipo_punto'
    ];

    protected $dates = [
        'fecha_creacion_dms',
        'fecha_modificacion_dms',
        'fecha_saldo'
    ];


//    public static function updateOrCreate($attributes, $values = array())
//    {
//        $instance = static::firstOrNew($attributes);
//
//        $instance->fill($values)->save();
//
//        return $instance;
//    }
}
