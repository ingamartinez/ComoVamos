<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Flash;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('login');
    }

    public function store(Request $request)
    {
        $user = User::withTrashed()->where([
            ['cedula','=',$request->cedula],
            ['password','=',$request->password]
        ])->first();

        if ($user == null ){
            Flash::error('Credenciales Incorrectas');
            return redirect()->back();

        }else if ($user->trashed()){
            Flash::error('Usuario deshabilitado');
            return redirect()->back();

        }else if($user){
            Auth::login($user);
            return redirect()->intended('/');

        }

    }

    public function logout(){
        Auth::logout();
        return redirect()->intended('login');
    }

}
