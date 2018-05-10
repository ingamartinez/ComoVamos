<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('logout','LoginController@logout');

Route::resource('login','LoginController');

Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return view('layouts.dashboard');
    });
});

Route::middleware(['auth'])->group(function () {

    Route::post('validar-usuario','UserController@validar')->name('usuario.validar');
    Route::post('restaurar-usuario/{id}','UserController@restore')->name('usuario.restore');
    Route::resource('gestion-usuarios','UserController',['names'=>[
        'store' => 'usuario.store',
        'show' => 'usuario.show',
        'update' => 'usuario.update',
        'delete' => 'usuario.delete'
    ]]);

    Route::put('asignar-supervisor','AsesorController@asignarSupervisor')->name('asesor.asignarSupervisor');
    Route::resource('asignar-asesores','AsesorController',['names'=>[
        'store' => 'asignar-asesores.store',
        'show' => 'asignar-asesores.show',
        'update' => 'asignar-asesores.update',
        'delete' => 'asignar-asesores.delete'
    ]]);

    Route::post('validar-circuito','CircuitoController@validar')->name('circuito.validar');
    Route::post('restaurar-circuito/{id}','CircuitoController@restore')->name('circuito.restore');
    Route::resource('gestion-circuito','CircuitoController',['names'=>[
        'store' => 'circuito.store',
        'show' => 'circuito.show',
        'update' => 'circuito.update',
        'delete' => 'circuito.delete'
    ]]);

    Route::resource('subir-dms','DMSController',['names'=>[
        'store' => 'dms.store',
        'show' => 'dms.show',
        'update' => 'dms.update',
        'delete' => 'dms.delete'
    ]]);

    Route::get('validar-paquete','PaqueteIncentivoController@validar')->name('paq.validar');
    Route::resource('paquete-incentivo','PaqueteIncentivoController',['names'=>[
        'store' => 'paq.store',
        'show' => 'paq.show',
        'update' => 'paq.update',
        'delete' => 'paq.delete'
    ]]);

});




Route::get('prueba', function () {
//    $role = Spatie\Permission\Models\Role::create(['name' => 'comercial']);

//    $user= \App\Models\User::with('roles')->findOrFail('1');
//
//    $user->syncRoles('comercial');
//    dd($user->roles()->pluck('name'));

//
//
//    dd($user->roles()->get());
//    dd(hash('sha256','123'));
});

Route::get('prueba3', function () {
    $user = \App\Models\User::findOrFail(2);

//    dd($user);

    $user->circuitos()->sync([1,2,3]);

    dd($user->circuitos()->get());
});

Route::get('prueba4', function (\Illuminate\Http\Request $request) {
    $client = new \Goutte\Client();
    $login = 'http://10.69.44.78:8080/Login.asp?submit1=Ingresar&Usuario=JS1052957402&password=%40mcomSAnov2017';
    $query = "http://10.69.44.78:8080/query.asp?IMSI=&MSISDN=57$request->num&Consultar.x=0&Consultar.y=0";
    $historico = "http://10.69.44.78:8080/Recargas_ajustes_historico.asp?FECHAINI=1%2F1%2F2018&FECHAFIN=5%2F31%2F2018&MSISDN=$request->num&Consultar.x=0&Consultar.y=0";

    $client->request('GET', $login);
    $crawler = $client->request('GET', $query);

    $data=collect();

    try{
        $crawler->filter('table')->eq(8)->each(function (\Symfony\Component\DomCrawler\Crawler $node ) use (&$data) {
            $data["estado"]= $node->filter('td')->eq(11)->text();
            $data["imsi"]= $node->filter('td')->eq(7)->text();
        });
    }catch (\Exception $ex){
        $data["estado"]= null;
        $data["imsi"]= null;
    }

    try{
        $crawler->filter('table')->eq(5)->each(function (\Symfony\Component\DomCrawler\Crawler $node ) use (&$data) {
            $data['date_last_update'] = \Carbon\Carbon::parse($node->filter('td > p > font')->eq(8)->text());
        });
    }catch (\Exception $ex){
        $data['date_last_update'] = null;
    }

    $crawler = $client->request('GET', $historico);

    $megas=array();
    $fechas=array();

    function convert($n, $m)
    {
        switch ($n){
            case "100":
                $n="4k";
            break;

            case "120":
                $n="6k";
            break;

            case "350":
                $n="datos";
                break;

            case "250":
                $n="10k";
                break;

            case "562":
                $n="datos";
                break;

            case "600":
                $n="datos";
                break;

            case "750":
                $n="20k";
                break;

            case "2098":
                $n="datos";
                break;

            case "minutera":
                break;

            case "bolsa":
                break;

            default:
                $n=null;
            break;
        }
        if ($n==null){
            return array(
                "fecha_paquete"=>null,
                "paquete"=>null
            );
        }else
            return array(
            "fecha_paquete"=>$m,
            "paquete"=>$n
        );
    }

    $crawler->filter('table')->eq(3)->each(function (\Symfony\Component\DomCrawler\Crawler $node ) use (&$data,&$megas,&$fechas) {
        try{

            $data['first_call']= true;
//            $data['date_first_call']= \Carbon\Carbon::parse($node->filter('tr:contains("FIRST CALL") > td > font')->eq(0)->text());
            $data['date_first_call']= trim($node->filter('tr:contains("FIRST CALL") > td > font')->eq(0)->text());
        }catch (\Exception $ex){
            $data['first_call']=false;
            $data['date_first_call']=null;
        }

        $node->filter('tr:contains("Datos GPRS Larga Duracion Todo el Dia"):contains("CREDITO")')->each(function (\Symfony\Component\DomCrawler\Crawler $trs) use (&$megas,&$fechas,&$data){
            $trs->filter('td > font')->eq(10)->each(function ( \Symfony\Component\DomCrawler\Crawler $t) use (&$megas){
                $megas[] = trim($t->text());
            });
            $trs->filter('td > font')->eq(0)->each(function ( \Symfony\Component\DomCrawler\Crawler $t) use (&$fechas){
                $fechas[]= trim($t->text());
            });
        });

        $node->filter('tr:contains("Plan Tarifario Minuteros"):contains("CREDITO")')->each(function (\Symfony\Component\DomCrawler\Crawler $trs) use (&$megas,&$fechas,&$data){
            $trs->filter('td > font')->eq(0)->each(function ( \Symfony\Component\DomCrawler\Crawler $t) use (&$fechas,&$megas){
                $megas[] = "minutera";
                $fechas[]= trim($t->text());
            });
        });

        $node->filter('tr:contains("Tigo Bag"):contains("CREDITO")')->each(function (\Symfony\Component\DomCrawler\Crawler $trs) use (&$megas,&$fechas,&$data){
            $trs->filter('td > font')->eq(0)->each(function ( \Symfony\Component\DomCrawler\Crawler $t) use (&$fechas,&$megas){
                $megas[] = "bolsa";
                $fechas[]= trim($t->text());
            });
        });

        $node->filter('tr:contains("Datos GPRS Corta Duracion Todo el Dia"):contains("CREDITO")')->each(function (\Symfony\Component\DomCrawler\Crawler $trs) use (&$megas,&$fechas,&$data){
            $trs->filter('td > font')->eq(10)->each(function ( \Symfony\Component\DomCrawler\Crawler $t) use (&$megas){
                $megas[] = trim($t->text());
            });
            $trs->filter('td > font')->eq(0)->each(function ( \Symfony\Component\DomCrawler\Crawler $t) use (&$fechas){
                $fechas[]= trim($t->text());
            });
        });

//        dd($megas);


        try{
            $fecha_paquete = array_last(array_filter(array_map("convert",$megas,$fechas)))["fecha_paquete"];
            $paquete = array_last(array_filter(array_map("convert",$megas,$fechas)))["paquete"];

            if ($fecha_paquete){
//                $data["fecha_paquete"]= \Carbon\Carbon::parse($fecha_paquete);
                $data["fecha_paquete"]= ($fecha_paquete);
            }else{
                $data["fecha_paquete"]= null;
            }

            $data["paquete"]=$paquete;
        }catch (\Exception $ex){
            $data["fecha_paquete"]=null;
            $data["paquete"]=null;
        }
    });

    dd($data);
});

Route::get('prueba5', function () {
    dd(\Carbon\Carbon::createFromFormat('d/m/Y',"27/09/2017"));
});