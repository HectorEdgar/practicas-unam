<?php

namespace sistema\Http\Controllers;

use Illuminate\Http\Request;
use sistema\Tema;
use sistema\Http\Requests\TemaFormRequest;
use Illuminate\Support\Facades\Redirect;
use DB;
use sistema\Utilidad;
use Illuminate\Support\Facades\Auth;

class TemaController extends Controller
{
    public function __construct()
    {
        //si no esta logeado regresa al login
        //$this->middleware('auth');
    }

    /*
    private function validarRoles(){
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin'])) {
            return Redirect::to('/');
        }
    }
    */
    public function index(Request $request)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}

        if ($request) {
            $query = trim($request->get('searchText'));
            $temas = DB::table('temas')
                ->where('descripcion', 'LIKE', '%' . $query . '%')
                ->orwhere('id_tema', 'LIKE', '%' . $query . '%')
                ->orderBy('id_tema', 'desc')
                ->paginate(10);

            $page=$request->get('page')!=null? $request->get('page'):1;

            $numeroRegistros= DB::table('temas')
                ->where('descripcion', 'LIKE', '%' . $query . '%')
                ->orwhere('id_tema', 'LIKE', '%' . $query . '%')
                ->orderBy('id_tema', 'desc')
                ->count();

            return view('tema.index',
                [
                    'temas' => $temas,
                    "searchText" => $query,
                    "totalRegistros"=> $numeroRegistros,
                    "page"=> $page
                ]
            );
        }
    }

    public function create()
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        return view('tema.create');
    }

    public function store(TemaFormRequest $request)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        $tema = new Tema;
        //Obtiene el Id 100 real
        $tema->id_tema=Utilidad::getId("temas","id_tema");
        $tema->descripcion = $request->get('descripcion');
        //activar el log de la base de datos
        DB::connection()->enableQueryLog();
        $tema->save();
        //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
        LogController::agregarLog(
            1,
            "tema",
            "Se agregó el tema: ". json_encode($tema)
        );

        return Redirect::to('tema');
    }

    public function show($id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        return view('tema.show', ["tema" => Tema::findOrFail($id)]);
    }
    public function edit($id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        return view('tema.edit', ["tema" => Tema::findOrFail($id)]);
    }

    public function update(TemaFormRequest $request, $id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        $tema = Tema::findOrFail($id);
        $tema->descripcion = $request->get('descripcion');
        //activar el log de la base de datos
        DB::connection()->enableQueryLog();
        $tema->update();
        //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
        LogController::agregarLog(
            2,
            "tema",
            "Se actualizó el tema: " . json_encode($tema)
        );
        return Redirect::to('tema');

    }

    public function destroy($id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        $tema = Tema::findOrFail($id);
        //activar el log de la base de datos
        DB::connection()->enableQueryLog();
        $tema->delete();
        //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
        LogController::agregarLog(
            3,
            "tema",
            "Se eliminó el tema: " . json_encode($tema)
        );

        //Session::flash('message','El tema fue eliminado');

        return Redirect::to('tema');

    }
}
