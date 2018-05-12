<?php

namespace App\Http\Controllers;

use App\Models\Dms;
use App\Models\PaqueteIncentivo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class PaqueteIncentivoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $paquetes="";

        if (auth()->user()->hasRole('Administrador')){
            $paquetes= PaqueteIncentivo::all()->take(40);
        }elseif(auth()->user()->hasRole('Asesor')){
            $paquetes= PaqueteIncentivo::where('users_id','=',auth()->user()->id)->get();
        }

        return view("asesores.paquete_incentivos.index",compact("paquetes"));
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
        if (PaqueteIncentivo::where('movil','=',$request->numero)->exists()){
            return response()->json(["mensaje"=>"Simcard anteriormente ingresada"],422);
        }

        try{
            $pdv = Dms::select("idpdv","nombre_punto",'circuito')->findOrFail($request->idpdv);
        }catch (\Exception $exception){
            return response()->json(["mensaje"=>"No se encontró el punto de Venta"],404);
        }

        $numero = preg_replace('/[^0-9]/', '', $request->numero);

        $paquete = $this->validarPaquete($numero);

//        dd($paquete->tipo_paquete=="");

        $info="";

        if($paquete->tipo_paquete==""){
            $info = collect([
                "pdv" => $pdv,
                "simcard"=>[
                    "estado" => $paquete->estado,
                    "fecha_activacion" => ($paquete->date_last_update) ? $paquete->date_last_update->toDateTimeString():null,
                    "paquete"=>[],
                    "first_call"=>$paquete->first_call,
                    "date_first_call"=>($paquete->date_first_call) ? $paquete->date_first_call->toDateTimeString():null
                ],
                "mensaje" => "Simcard no valida para Incentivo"
            ]);

            $info["mensaje"]="Simcard no valida para Incentivo";
            return response()->json($info,404);
        }else{
            $info = collect([
                "pdv" => $pdv,
                "simcard"=>[
                    "estado" => $paquete->estado,
                    "fecha_activacion" => ($paquete->date_last_update) ? $paquete->date_last_update->toDateTimeString():null,
                    "paquete"=>[
                        "fecha"=>$paquete->tipo_paquete["fecha"]->toDateTimeString(),
                        "paquete_id"=>$paquete->tipo_paquete["paquete_id"],
                        "recurso"=>$paquete->tipo_paquete["recurso"],
                        "tipo_recurso"=>$paquete->tipo_paquete["tipo_recurso"]
                    ],
                    "first_call"=>$paquete->first_call,
                    "date_first_call"=>($paquete->date_first_call) ? $paquete->date_first_call->toDateTimeString():null
                ],
                "mensaje" => "Todo bien"
            ]);
        }

        $mesActivacion=$paquete->date_last_update->month;
        $mesPaquete=$paquete->tipo_paquete["fecha"]->month;

        $mesActual=Carbon::now()->month;

//        dd($info,$mesActivacion==$mesActual,$mesPaquete==$mesActual);
//        dd($request->idpdv);

        if($info["simcard"]["first_call"]){

            $paquete_incentivo = new PaqueteIncentivo();
            $paquete_incentivo->movil = $numero;
            $paquete_incentivo->paquete = $paquete->tipo_paquete["paquete_id"];
            $paquete_incentivo->fecha_paquete = $paquete->tipo_paquete["fecha"]->toDateTimeString();
            $paquete_incentivo->movil_contacto = $request->numero_contacto;
            $paquete_incentivo->dms_idpdv = $pdv->idpdv;
            $paquete_incentivo->validado_sistema = 0;
            $paquete_incentivo->validado_callcenter = 0;
            $paquete_incentivo->users_id = auth()->user()->id;

            $paquete_incentivo->save();

            return response()->json($info,200);
        }else
            if (($mesActivacion==$mesActual) && ($mesPaquete==$mesActual)){
                $paquete_incentivo = new PaqueteIncentivo();
                $paquete_incentivo->movil = $numero;
                $paquete_incentivo->paquete = $paquete->tipo_paquete["paquete_id"];
                $paquete_incentivo->fecha_paquete = $paquete->tipo_paquete["fecha"]->toDateTimeString();
                $paquete_incentivo->movil_contacto = $request->numero_contacto;
                $paquete_incentivo->dms_idpdv = $pdv->idpdv;
                $paquete_incentivo->validado_sistema = 0;
                $paquete_incentivo->validado_callcenter = 0;
                $paquete_incentivo->users_id = auth()->user()->id;

                switch ($paquete->tipo_paquete["paquete_id"]){
                    case "4k":
                        $paquete_incentivo->valor = 1000;
                        break;
                    case "6k":
                        $paquete_incentivo->valor = 1500;
                        break;
                    case "10k":
                        $paquete_incentivo->valor = 1500;
                        break;
                    case "20k":
                        $paquete_incentivo->valor = 2000;
                        break;
                    case "bolsa":
                        $paquete_incentivo->valor = 2000;
                        break;
                    case "datos":
                        $paquete_incentivo->valor = 2000;
                        break;
                    case "minutera":
                        $paquete_incentivo->valor = 2000;
                        break;
                }

                $paquete_incentivo->save();

                return response()->json($info,200);
            }else{
                return response()->json($info,404);
            }
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

    public function validar(Request $request)
    {

        if ((new \App\Models\PaqueteIncentivo)->where('movil','=',$request->numero)->exists()){
            return response()->json(["mensaje"=>"Simcard anteriormente ingresada"],422);
        }

        try{
            $pdv = Dms::findOrFail($request->idpdv)->select("nombre_punto",'circuito')->first();
        }catch (\Exception $exception){
            return response()->json(["mensaje"=>"No se encontró el punto de Venta"],404);
        }

        $numero = preg_replace('/[^0-9]/', '', $request->numero);

        $paquete = $this->validarPaquete($numero);

//        dd($paquete->tipo_paquete=="");

        $info="";

        if($paquete->tipo_paquete==""){
            $info = collect([
            "pdv" => $pdv,
                "simcard"=>[
                    "estado" => $paquete->estado,
                    "fecha_activacion" => ($paquete->date_last_update) ? $paquete->date_last_update->toDateTimeString():null,
                    "paquete"=>[],
                    "first_call"=>$paquete->first_call,
                    "date_first_call"=>($paquete->date_first_call) ? $paquete->date_first_call->toDateTimeString():null
                ],
                "mensaje" => "Simcard no valida para Incentivo"
            ]);

            $info["mensaje"]="Simcard no valida para Incentivo";
            return response()->json($info,404);
        }else{
            $info = collect([
            "pdv" => $pdv,
                "simcard"=>[
                    "estado" => $paquete->estado,
                    "fecha_activacion" => ($paquete->date_last_update) ? $paquete->date_last_update->toDateTimeString():null,
                    "paquete"=>[
                        "fecha"=>$paquete->tipo_paquete["fecha"]->toDateTimeString(),
                        "paquete_id"=>$paquete->tipo_paquete["paquete_id"],
                        "recurso"=>$paquete->tipo_paquete["recurso"],
                        "tipo_recurso"=>$paquete->tipo_paquete["tipo_recurso"]
                    ],
                    "first_call"=>$paquete->first_call,
                    "date_first_call"=>($paquete->date_first_call) ? $paquete->date_first_call->toDateTimeString():null
                ],
                "mensaje" => "Todo bien"
            ]);
        }

        $mesActivacion=$paquete->date_last_update->month;
        $mesPaquete=$paquete->tipo_paquete["fecha"]->month;

        $mesActual=Carbon::now()->month;

//        dd($info,$mesActivacion==$mesActual,$mesPaquete==$mesActual);

        if($info["simcard"]["first_call"]){
            return response()->json($info,200);
        }else
            if (($mesActivacion==$mesActual) && ($mesPaquete==$mesActual)){
            return response()->json($info,200);
        }else{
            $info["mensaje"]="Simcard no valida para Incentivo";
            return response()->json($info,404);
        }
    }

    public function validarPaquete($numero){
        $client = new \Goutte\Client();
        $login = 'http://10.69.44.78:8080/Login.asp?submit1=Ingresar&Usuario=JS1052957402&password=%40mcomSAnov2017';
        $query = "http://10.69.44.78:8080/query.asp?IMSI=&MSISDN=57$numero&Consultar.x=0&Consultar.y=0";
        $historico = "http://10.69.44.78:8080/Recargas_ajustes_historico.asp?FECHAINI=1%2F1%2F2018&FECHAFIN=5%2F31%2F2018&MSISDN=$numero&Consultar.x=0&Consultar.y=0";

        $client->request('GET', $login);
        $crawler = $client->request('GET', $query);

        $data=collect();

        try{
            $crawler->filter('table')->eq(8)->each(function (\Symfony\Component\DomCrawler\Crawler $node ) use (&$data) {
                $data->estado = $node->filter('td')->eq(11)->text();
                $data->imsi = $node->filter('td')->eq(7)->text();
            });
        }catch (\Exception $ex){
            $data->estado = null;
            $data->imsi = null;
        }

        try{
            $crawler->filter('table')->eq(5)->each(function (\Symfony\Component\DomCrawler\Crawler $node ) use (&$data) {
                $data->date_last_update = \Carbon\Carbon::parse($node->filter('td > p > font')->eq(8)->text());
            });
        }catch (\Exception $ex){
            $data->date_last_update = null;
        }

        $crawler = $client->request('GET', $historico);


        $paquetes = collect();

        $crawler->filter('table')->eq(3)->each(function (\Symfony\Component\DomCrawler\Crawler $node ) use (&$data,&$fechas,&$paquetes) {
            try{

                $data->first_call = true;
                $data->date_first_call= \Carbon\Carbon::parse($node->filter('tr:contains("FIRST CALL") > td > font')->eq(0)->text());
            }catch (\Exception $ex){
                $data->first_call=false;
                $data->date_first_call=null;
            }

            $node->filter('tr:contains("Datos GPRS Larga Duracion Todo el Dia"):contains("CREDITO")')->each(function (\Symfony\Component\DomCrawler\Crawler $trs) use (&$fechas,&$data,&$paquetes){
                $recurso="";
                $fechas="";
                $tipoRecurso="";
                $fecha_vencimiento="";

                $trs->filter('td > font')->eq(10)->each(function ( \Symfony\Component\DomCrawler\Crawler $t) use (&$recurso,&$paquetes,&$tipo){
                    $recurso = trim($t->text());
                });
                $trs->filter('td > font')->eq(15)->each(function ( \Symfony\Component\DomCrawler\Crawler $t) use (&$tipoRecurso,&$paquetes){
                    $tipoRecurso = trim($t->text());
                });
                $trs->filter('td > font')->eq(0)->each(function ( \Symfony\Component\DomCrawler\Crawler $t) use (&$fechas,&$paquetes){
                    $fechas= trim($t->text());
                });
                $trs->filter('td > font')->eq(16)->each(function ( \Symfony\Component\DomCrawler\Crawler $t) use (&$fecha_vencimiento,&$paquetes){
                    $fecha_vencimiento= trim($t->text());
                });

                $paquetes->push([
                    "recurso" => $recurso,
                    "fecha" => Carbon::parse($fechas),
                    "tipo_recurso" => $tipoRecurso,
                    "fecha_vencimiento" => Carbon::parse($fecha_vencimiento),
                    "dias_recurso" => Carbon::parse($fecha_vencimiento)->diffInDays(Carbon::parse($fechas))
                ]);
            });

            $node->filter('tr:contains("Plan Tarifario Minuteros"):contains("CREDITO")')->each(function (\Symfony\Component\DomCrawler\Crawler $trs) use (&$fechas,&$data,&$paquetes){
                $recurso="";
                $fechas="";
                $tipoRecurso="";
                $fecha_vencimiento="";

                $trs->filter('td > font')->eq(10)->each(function ( \Symfony\Component\DomCrawler\Crawler $t) use (&$recurso,&$paquetes){
                    $recurso = trim($t->text());
                });
                $trs->filter('td > font')->eq(19)->each(function ( \Symfony\Component\DomCrawler\Crawler $t) use (&$tipoRecurso,&$paquetes){
                    $tipoRecurso = trim($t->text());
                });
                $trs->filter('td > font')->eq(0)->each(function ( \Symfony\Component\DomCrawler\Crawler $t) use (&$fechas,&$paquetes){
                    $fechas= trim($t->text());
                });
                $trs->filter('td > font')->eq(16)->each(function ( \Symfony\Component\DomCrawler\Crawler $t) use (&$fecha_vencimiento,&$paquetes){
                    $fecha_vencimiento= trim($t->text());
                });

                $paquetes->push([
                    "recurso" => $recurso,
                    "fecha" => Carbon::parse($fechas),
                    "tipo_recurso" => $tipoRecurso,
                    "fecha_vencimiento" => Carbon::parse($fecha_vencimiento),
                    "dias_recurso" => Carbon::parse($fecha_vencimiento)->diffInDays(Carbon::parse($fechas))
                ]);
            });

            $node->filter('tr:contains("Tigo Bag"):contains("CREDITO")')->each(function (\Symfony\Component\DomCrawler\Crawler $trs) use (&$fechas,&$data,&$paquetes){
                $recurso="";
                $fechas="";
                $tipoRecurso="";
                $fecha_vencimiento="";

                $trs->filter('td > font')->eq(10)->each(function ( \Symfony\Component\DomCrawler\Crawler $t) use (&$recurso,&$paquetes){
                    $recurso = trim($t->text());
                });
                $trs->filter('td > font')->eq(14)->each(function ( \Symfony\Component\DomCrawler\Crawler $t) use (&$tipoRecurso,&$paquetes){
                    $tipoRecurso = trim($t->text());
                });
                $trs->filter('td > font')->eq(0)->each(function ( \Symfony\Component\DomCrawler\Crawler $t) use (&$fechas,&$paquetes){
                    $fechas= trim($t->text());
                });
                $trs->filter('td > font')->eq(16)->each(function ( \Symfony\Component\DomCrawler\Crawler $t) use (&$fecha_vencimiento,&$paquetes){
                    $fecha_vencimiento= trim($t->text());
                });

                $paquetes->push([
                    "recurso" => $recurso,
                    "fecha" => Carbon::parse($fechas),
                    "tipo_recurso" => $tipoRecurso,
                    "fecha_vencimiento" => Carbon::parse($fecha_vencimiento),
                    "dias_recurso" => Carbon::parse($fecha_vencimiento)->diffInDays(Carbon::parse($fechas))
                ]);
            });

            $node->filter('tr:contains("Datos GPRS Corta Duracion Todo el Dia"):contains("CREDITO")')->each(function (\Symfony\Component\DomCrawler\Crawler $trs) use (&$fechas,&$data,&$paquetes){
                $recurso="";
                $fechas="";
                $tipoRecurso="";
                $fecha_vencimiento="";

                $trs->filter('td > font')->eq(10)->each(function ( \Symfony\Component\DomCrawler\Crawler $t) use (&$recurso,&$paquetes){
                    $recurso = trim($t->text());
                });
                $trs->filter('td > font')->eq(15)->each(function ( \Symfony\Component\DomCrawler\Crawler $t) use (&$tipoRecurso,&$paquetes){
                    $tipoRecurso = trim($t->text());
                });
                $trs->filter('td > font')->eq(0)->each(function ( \Symfony\Component\DomCrawler\Crawler $t) use (&$fechas,&$paquetes){
                    $fechas= trim($t->text());
                });
                $trs->filter('td > font')->eq(16)->each(function ( \Symfony\Component\DomCrawler\Crawler $t) use (&$fecha_vencimiento,&$paquetes){
                    $fecha_vencimiento= trim($t->text());
                });

                $paquetes->push([
                    "recurso" => $recurso,
                    "fecha" => Carbon::parse($fechas),
                    "tipo_recurso" => $tipoRecurso,
                    "fecha_vencimiento" => Carbon::parse($fecha_vencimiento),
                    "dias_recurso" => Carbon::parse($fecha_vencimiento)->diffInDays(Carbon::parse($fechas))
                ]);
            });

            $sorted = $paquetes->sortByDesc('recurso');

//            dd($sorted);

            $tipo_paquete="";
            $paquete_mes_anterior=false;

            $sorted->each(function ($item, $key) use (&$paquete_mes_anterior){
                if($item["fecha"]->month!=Carbon::now()->month){
                    $paquete_mes_anterior = true;
                    return false;
                }
            });

//            dd($paquete_mes_anterior);

            if (!$paquete_mes_anterior){
                $sorted->each(function ($item, $key) use (&$tipo_paquete){
                    $paq = $this->tipoPaquete($item);
                    if ($paq["paquete_id"]) {
                        $tipo_paquete = $paq;
                        return false;
                    }
                });
            }

            $data->tipo_paquete= $tipo_paquete;

//            dd($data->tipo_paquete);

        });

        return $data;
    }

    protected function tipoPaquete($paquete){
        switch ($paquete["tipo_recurso"]){
            case "Megabytes":
                switch ($paquete["recurso"]){
                    case "100":
                        $paquete["paquete_id"]="4k";
                        break;

                    case "120":
                        $paquete["paquete_id"]="6k";
                        break;

                    case "350":
                        $paquete["paquete_id"]="datos";
                        break;

                    case "250":
                        $paquete["paquete_id"]="10k";
                        break;

                    case "500":
                        if($paquete["dias_recurso"]==7){
                            $paquete["paquete_id"]="datos";
                        }elseif($paquete["dias_recurso"]==3){
                            $paquete["paquete_id"]="4k";
                        }
                        break;
                    case "562":
                        $paquete["paquete_id"]="datos";
                        break;

                    case "600":
                        $paquete["paquete_id"]="datos";
                        break;

                    case "750":
                        $paquete["paquete_id"]="20k";
                        break;

                    case "1024":
                        $paquete["paquete_id"]="10k";
                        break;

                    case "2098":
                        $paquete["paquete_id"]="datos";
                        break;

                    default:
                        $paquete["paquete_id"]=null;
                        break;
                }
                break;
            case "Plan Tarifario Minuteros":
                $paquete["paquete_id"]="minutera";
                break;
            case "Tigo Bag":
                $paquete["paquete_id"]="bolsa";
                break;
            default:
                $paquete["paquete_id"]=null;
                break;
        }
        return $paquete;
    }

//generarPlanillaIndex
//generarPlanillaExcel

    public function generarPlanillaIndex(Request $request){
        $asesores= ( new\App\Models\User)->join('paquetes_incentivos',"paquetes_incentivos.users_id",'users.id')
            ->select('users.id','users.name')
            ->where('paquetes_incentivos.validado_sistema','=',0)
            ->groupBy('id','name')
            ->get();

        return view("asesores.generar_planilla_incentivo",compact("asesores"));

    }

    public function generarPlanillaExcel(Request $request){
        $lineas = ( new\App\Models\PaqueteIncentivo)->where('validado_sistema',"=",0)->where('users_id',"=",$request->asesor)->get();

//    dd($lineas->first()->dms->circuito);
        $ids = array_pluck($lineas,'id');
    \App\Models\PaqueteIncentivo::whereIn('id',$ids)->update(["validado_sistema"=>1]);
//    dd($lineas);

        Excel::load(public_path('assets/Planilla.xlsx'), function($reader) use (&$lineas) {

            $reader->sheet('Planilla', function($sheet) use (&$lineas) {
                $lowValue=0;
                $countLowValue=0;
                $midValue=0;
                $countMidValue=0;
                $highValue=0;
                $countHighValue=0;

//            ENUM('4k', '6k', '10k', '20k', 'bolsa', 'datos', 'minutera') G10
                $count=0;
                foreach ($lineas as $linea) {

//                echo($linea);

                    switch ($linea->paquete){
                        case "4k":
                            $sheet->prependRow(10,[
                                $linea->dms_idpdv,
                                $linea->dms->nombre_punto,
                                $linea->dms->circuito,
                                $linea->movil_contacto,
                                $linea->movil,
                                "",
                                "X"
                            ]);
                            $lowValue+=$linea->valor;
                            $countLowValue++;
                            break;
                        case "6k":
                            $sheet->prependRow(10,[
                                $linea->dms_idpdv,
                                $linea->dms->nombre_punto,
                                $linea->dms->circuito,
                                $linea->movil_contacto,
                                $linea->movil,
                                "",
                                "",
                                "X"
                            ]);
                            $midValue+=$linea->valor;
                            $countMidValue++;
                            break;
                        case "10k":
                            $sheet->prependRow(10,[
                                $linea->dms_idpdv,
                                $linea->dms->nombre_punto,
                                $linea->dms->circuito,
                                $linea->movil_contacto,
                                $linea->movil,
                                "",
                                "",
                                "",
                                "X"
                            ]);
                            $midValue+=$linea->valor;
                            $countMidValue++;
                            break;
                        case "20k":
                            $sheet->prependRow(10,[
                                $linea->dms_idpdv,
                                $linea->dms->nombre_punto,
                                $linea->dms->circuito,
                                $linea->movil_contacto,
                                $linea->movil,
                                "",
                                "",
                                "",
                                "",
                                "X"
                            ]);
                            break;
                        case "bolsa":
                            $sheet->prependRow(10,[
                                $linea->dms_idpdv,
                                $linea->dms->nombre_punto,
                                $linea->dms->circuito,
                                $linea->movil_contacto,
                                $linea->movil,
                                "",
                                "",
                                "",
                                "",
                                "",
                                "X"
                            ]);
                            $highValue+=$linea->valor;
                            $countHighValue++;
                            break;
                        case "datos":
                            $sheet->prependRow(10,[
                                $linea->dms_idpdv,
                                $linea->dms->nombre_punto,
                                $linea->dms->circuito,
                                $linea->movil_contacto,
                                $linea->movil,
                                "",
                                "",
                                "",
                                "",
                                "",
                                "",
                                "X"
                            ]);
                            $highValue+=$linea->valor;
                            $countHighValue++;
                            break;
                        case "minutera":
                            $sheet->prependRow(10,[
                                $linea->dms_idpdv,
                                $linea->dms->nombre_punto,
                                $linea->dms->circuito,
                                $linea->movil_contacto,
                                $linea->movil,
                                "",
                                "",
                                "",
                                "",
                                "",
                                "",
                                "",
                                "X"
                            ]);
                            $highValue+=$linea->valor;
                            $countHighValue++;
                            break;
                    }
                    $count++;
                }

                $sheet->removeRow(9);

                $sheet->setCellValue(
                    'G'.(count($lineas)+11),
                    $countLowValue.' x $1.000 = $'.$lowValue
                );

                $sheet->setCellValue(
                    'G'.(count($lineas)+13),
                    $countMidValue.' x $1.500 = $'.$midValue
                );

                $sheet->setCellValue(
                    'G'.(count($lineas)+15),
                    $countHighValue.' x $2.000 = $'.$highValue
                );

                $sheet->setCellValue(
                    'M'.(count($lineas)+13),
                    'TOTAL: $'.($lowValue+$midValue+$highValue)
                );

                $sheet->setCellValue(
                    'E'.(count($lineas)+12),
                    'Asesor: '.$lineas->first()->user->name
                );

//                $sheet->setCellValue(
//                    'B'.(count($lineas)+12),
//                    'Sistemas: '.auth()->user()->name
//                );

            });
        })->export('xlsx');

        return redirect()->back();
    }

}