<?php

namespace App\Http\Controllers;

use App\Models\Dms;
use App\Models\PaqueteIncentivo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class PaqueteIncentivoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $paquetes= PaqueteIncentivo::all();
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

    public function validar(Request $request)
    {
//        try{
//            $pdv = Dms::findOrFail($request->idpdv)->select("nombre_punto",'circuito')->first();
//        }catch (\Exception $exception){
//            return response()->json(["mensaje"=>"No se encontró el punto de Venta"],404);
//        }

        $numero = preg_replace('/[^0-9]/', '', $request->numero);

        $paquete = $this->validarPaquete($numero);

//        dd($paquete->tipo_paquete=="");

        $info="";

        if($paquete->tipo_paquete==""){
            $info = collect([
//            "pdv" => $pdv,
                "simcard"=>[
                    "estado" => $paquete->estado,
                    "fecha_activacion" => ($paquete->date_last_update) ? $paquete->date_last_update->toDateTimeString():null,
                    "paquete"=>[],
                    "first_call"=>$paquete->first_call,
                    "date_first_call"=>($paquete->date_first_call) ? $paquete->date_first_call->toDateTimeString():null
                ],
                "mensaje" => "Todo bien"
            ]);

            $info["mensaje"]="Simcard no valida para Incentivo";
            return response()->json($info,404);
        }else{
            $info = collect([
//            "pdv" => $pdv,
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

}