<?php

namespace App\Http\Controllers;

use App\Models\Circuito;
use App\Models\User;
use Illuminate\Http\Request;
use DB;
use Flash;
use Spatie\Permission\Models\Role;

class AsesorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function validar(Request $request){
        $request->validate([
            'name' => 'required|max:191',
            'cedula' => 'required|numeric|unique:users,cedula,'.$request->id,
            'password' => 'bail|required|max:191',
            'check_rol' => 'bail|required',
        ]);
    }
    public function index()
    {
//        $users= User::role(['Administrador','Supervisor'])->get();
//        $users= User::roleById([3,4,5,6,7])->get();
        $users= User::all();

        $asd = User::findOrFail(2);

        $roles= Role::all();
        $circuitos = Circuito::where('tipo','=','comercial')->get();
        return view('admin.gestion_asesores.index', compact('users','roles','circuitos'));
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
        $request->validate([
            'name' => 'required|max:191',
            'cedula' => 'required|numeric|unique:users,cedula,'.$request->id,
            'password' => 'required|max:191',
            'check_rol' => 'required',
        ]);

//        dd($request->all());

        try{
            DB::beginTransaction();

            $user = new User();
            $user->name = $request->name;
            $user->cedula = $request->cedula;
            $user->password = $request->password;

            $user->save();

            $user->syncRoles($request->radio_rol);

            Flash::success('Usuario creado correctamente');

//            throw new \Exception('No se pudo crear el usuario');

            DB::commit();

            return redirect()->back();
        }catch (\Exception $ex){
            DB::rollBack();

            Flash::error('Error al guardar - '.$ex->getMessage());

            return redirect()->back();
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
        try{
            $user = User::withTrashed()->with('roles')->findOrFail($id);

//            $rolID=[];
//            foreach ($user->roles as $rol){
//                $rolID=$rol->id;
//            }
//            $user->rol_id = $rolID;

//            dd($user);

            return response()->json($user,200);

        }catch (\Exception $ex){
            return response()->json(['message'=>'No se encuentra el usuario'],404);
//            return response()->json(['message'=>$ex],404);
        }
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
        $user = User::withTrashed()->findOrFail($id);

        try{
            DB::beginTransaction();

            $user->name = $request->name;
            $user->cedula = $request->cedula;
            $user->save();

            $user->syncRoles($request->radio_rol);

            DB::commit();

            Flash::success('Usuario editado correctamente');

            return response()->json($user,200);

        }catch (\Exception $ex){
            DB::rollBack();

            Flash::error('Error al editar - '.$ex->getMessage());

            return response()->json(['message'=>'No se encuentra el usuario'],404);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {


        try{
            DB::beginTransaction();

            //throw new \Exception('No se pudo crear el usuario');

            $user= User::destroy($id);

            DB::commit();

            Flash::success('Usuario eliminado correctamente');

            return response()->json('Usuario eliminado correctamente',200);

        }catch (\Exception $ex){
            DB::rollBack();

            //Flash::error('Error al editar - '.$ex->getMessage());

            return response()->json('No se puede eliminar el usuario',404);
        }


    }
    public function restore($id)
    {
        try{
            DB::beginTransaction();

            //throw new \Exception('No se pudo crear el usuario');

            User::withTrashed()->findOrFail($id)->restore();

            DB::commit();

            Flash::success('Usuario restaurado correctamente');

            return response()->json('Usuario Restaurado correctamente',200);

        }catch (\Exception $ex){
            DB::rollBack();

            //Flash::error('Error al editar - '.$ex->getMessage());

            return response()->json('No se pudo restaurar el usuario',404);
        }
    }

    public function asignarSupervisor(Request $request)
    {
//        dd(collect($request->check_circuito));
        $user = User::withTrashed()->findOrFail($request->id);

        try{
            DB::beginTransaction();

            $user->users_id = $request->supervisor;
            $user->save();
            $user->circuitos()->sync($request->check_circuito);

            DB::commit();

            Flash::success('Usuario editado correctamente');

            return response()->json($user,200);

        }catch (\Exception $ex){
            DB::rollBack();

            Flash::error('Error al editar - '.$ex->getMessage());

            return response()->json(['message'=>'Error al Editar -'.$ex->getMessage()],404);
        }

    }
}
