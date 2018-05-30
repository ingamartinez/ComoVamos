<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class ResumenController extends Controller
{
    public function __construct()
    {
        Carbon::setLocale('es');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $fechaInicial=$request->input('fechaInicial');
        $fechaFinal=$request->input('fechaFinal');

        if (!isset($fechaInicial) or !isset($fechaInicial)){
            $fechaInicial=Carbon::now()->startOfDay()->toDateTimeString();
            $fechaFinal=Carbon::now()->endOfDay()->toDateTimeString();
        }else{
            $fechaInicial=Carbon::parse($fechaInicial)->startOfDay()->toDateTimeString();
            $fechaFinal=Carbon::parse($fechaFinal)->endOfDay()->toDateTimeString();
        }

//        dd($fechaInicial,$fechaFinal);

        $resumenPorPaquete= \DB::table('paquetes_incentivos')
            ->select(
                'paquetes_incentivos.paquete',
                \DB::raw('Sum(paquetes_incentivos.valor) as valor'),
                \DB::raw('Count(paquetes_incentivos.movil) as cantidad')
            )
            ->where('paquetes_incentivos.created_at','>=',$fechaInicial)
            ->where('paquetes_incentivos.created_at','<=',$fechaFinal)
            ->groupBy('paquetes_incentivos.paquete')
        ->get();

        $resumenPorSupervisor= \DB::table('users')
            ->join('paquetes_incentivos',"paquetes_incentivos.users_id",'users.id')
            ->join('users as supervisores',"users.users_id",'supervisores.id')
            ->select(
                'supervisores.name',
                \DB::raw('Sum(paquetes_incentivos.valor) as valor'),
                \DB::raw('Count(paquetes_incentivos.movil) as cantidad')
            )
            ->where('paquetes_incentivos.created_at','>=',$fechaInicial)
            ->where('paquetes_incentivos.created_at','<=',$fechaFinal)
            ->groupBy('supervisores.name')
        ->get();

        $resumenPorAsesor= \DB::table('users')
            ->join('paquetes_incentivos',"paquetes_incentivos.users_id",'users.id')
            ->select(
                'users.name',
                \DB::raw('Sum(paquetes_incentivos.valor) as valor'),
                \DB::raw('Count(paquetes_incentivos.movil) as cantidad')
            )
            ->where('paquetes_incentivos.created_at','>=',$fechaInicial)
            ->where('paquetes_incentivos.created_at','<=',$fechaFinal)
            ->groupBy('users.name')
            ->get();
        $fechaInicial=Carbon::parse($fechaInicial);
        $fechaFinal=Carbon::parse($fechaFinal);

        return view('administrativos.index',compact('resumenPorPaquete','resumenPorSupervisor','resumenPorAsesor','fechaInicial','fechaFinal'));
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
        //
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