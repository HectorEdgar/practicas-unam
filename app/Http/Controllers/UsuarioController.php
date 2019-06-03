<?php

namespace sistema\Http\Controllers;

use Illuminate\Http\Request;
use sistema\Http\Controllers\Controller;
use sistema\User;
use Illuminate\Support\Facades\Redirect;
use sistema\Http\Requests\UsuarioFormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use sistema\Role;

class UsuarioController extends Controller
{

    public function validarRoles()
    {
        if (!Auth::user()->authorizeRoles(['admin'])) {return Redirect::to('/');}
    }

    public function __construct()
    {
        //$this->middleware('auth');

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!Auth::user()->authorizeRoles(['admin'])) {return Redirect::to('/');}
        if ($request) {
            $query = trim($request->get('searchText'));
            $usuarios = DB::table('users')
                ->where('name', 'LIKE', '%' . $query . '%')
                ->orderBy('id', 'desc')
                ->paginate(10);
            $totalRegistros = DB::table('users')
                ->where('name', 'LIKE', '%' . $query . '%')
                ->orderBy('id', 'desc')
                ->count();

            $page = $request->get('page') != null ? $request->get('page') : 1;
            return view('usuario.index',
                [
                'usuarios' => $usuarios,
                "searchText" => $query,
                "totalRegistros"=> $totalRegistros,
                "page" => $page
                ]
            );
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Auth::user()->authorizeRoles(['admin'])) {return Redirect::to('/');}
        return view("usuario.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UsuarioFormRequest $request)
    {
        if (!Auth::user()->authorizeRoles(['admin'])) {return Redirect::to('/');}
        $usuario = new User;
        $usuario->name = $request->get('name');
        $usuario->email = $request->get('email');
        $usuario->password = bcrypt($request->get('password'));

        DB::connection()->enableQueryLog();
        $usuario->save();
        LogController::agregarLog(
            1,
            "usuario",
            "Se agregó el usuario: " . json_encode($usuario)
        );
        $usuario->roles()->attach(Role::where('nombre', $request->get("permisos"))->first());

        return Redirect::to('usuario');
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
          //Roles
        if (!Auth::user()->authorizeRoles(['admin'])) {return Redirect::to('/');}
        return view('usuario.edit', ["usuario" => User::findOrFail($id)]);
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
        if (!Auth::user()->authorizeRoles(['admin'])) {return Redirect::to('/');}
        $usuario = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:6|confirmed',
        ]);
        $usuario->name = $request->get('name');
        //$usuario->email = $validated->get('email');
        $usuario->password = bcrypt($request->get('password'));

        DB::connection()->enableQueryLog();

        $usuario->update();
        $usuario->roles()->attach(Role::where('nombre', $request->get("permisos"))->first());
        LogController::agregarLog(
            2,
            "usuario",
            "Se actualizó el usuario: " . json_encode($usuario)
        );
        return Redirect::to('usuario');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!Auth::user()->authorizeRoles(['admin'])) {return Redirect::to('/');}

        $usuario = User::findOrFail($id);
        DB::connection()->enableQueryLog();

        $usuario->delete();
        LogController::agregarLog(
            3,
            "usuario",
            "Se eliminó el usuario: " . json_encode($usuario)
        );
        return Redirect::to('usuario');
    }
}
