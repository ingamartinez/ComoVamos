<?php

namespace App\Http\Controllers;

use App\Models\Dms;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class DMSController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.dms.subir_dms');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        Excel::filter('chunk')->load($request->file('dms')->getRealPath())->chunk(250, function($results){
            $results->each(function($row) {
                Dms::updateOrCreate(
                    [
                        'idpdv' => $row['cod_punto']
                    ],
                    [
                        'nombre_punto' => $row['punto'],
                        'circuito' => $row->circuito,
                        'telefono' => $row['telefono'],
                        'celular' => $row['celular'],
                        'dueno' => $row['dueno'],
                        'documento' => $row['documento'],
                        'ciudad' => $row['ciudad'],
                        'barrio' => $row['barrio'],
                        'direccion' => $row['direccion'],
                        'lat' => $row['latitud'],
                        'long' => $row['longitud'],
                        'estado_dms' => $row['estado_dms'],
                        'fecha_creacion_dms' => Carbon::createFromFormat('d/m/Y',$row['fecha_creacion']),
                        'fecha_modificacion_dms' => Carbon::createFromFormat('d/m/Y',$row['fecha_ultima_modificacion']),
                        'distribuidor' => $row['distribuidor'],
                        'moviles_epin' => substr($row['moviles_epin'],'0','44'),
                        'cod_sub' => $row['cod.sub'],
                        'epin' => $row['serve_pin_tigo'],
                        'simcard' => $row['servsimcard_tigo'],
                        'mbox' => $row['servmbox'],
//                        'saldo' => $row['saldo'],
//                        'fecha_saldo' => Carbon::createFromFormat('d/m/Y',$row['fecha_saldo']),
                        'tipo_punto' => $row['tipo']
                    ]
                );
            });

        });

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
