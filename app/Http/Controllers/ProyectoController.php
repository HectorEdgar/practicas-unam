<?php

namespace sistema\Http\Controllers;

use Illuminate\Http\Request;
use sistema\Proyecto;
use sistema\Http\Requests\ProyectoFormRequest;
use Illuminate\Support\Facades\Redirect;
use DB;
use sistema\Utilidad;
use Illuminate\Support\Facades\Auth;
use sistema\Http\Controllers\Controller;

class ProyectoController extends Controller
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
            $proyectos = DB::table('catalogo_proyecto')
                ->where('proyecto', 'LIKE', '%' . $query . '%')
                ->orwhere('id_proyecto', 'LIKE', '%' . $query . '%')
                ->orderBy('id_proyecto', 'desc')
                ->paginate(10);

            $page=$request->get('page')!=null? $request->get('page'):1;

            $numeroRegistros= DB::table('catalogo_proyecto')
                 ->where('proyecto', 'LIKE', '%' . $query . '%')
                ->orwhere('id_proyecto', 'LIKE', '%' . $query . '%')
                ->orderBy('id_proyecto', 'desc')->count();

            return view('proyecto.index',
                [
                    'proyectos' => $proyectos,
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
        return view('proyecto.create');
    }

    public function store(ProyectoFormRequest $request)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        $proyecto = new Proyecto;
        //Obtiene el Id 100 real
        $proyecto->id_proyecto=Utilidad::getId("catalogo_proyecto","id_proyecto");
        $proyecto->proyecto = $request->get('proyecto');

        //activar el log de la base de datos
        DB::connection()->enableQueryLog();
        $proyecto->save();
        //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
        LogController::agregarLog(
            1,
            "catalogo_proyecto",
            "Se agregó el proyecto: ". json_encode($proyecto)
        );

        return Redirect::to('proyecto');
    }

    public function show($id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        return view('proyecto.show', ["proyecto" => Proyecto::findOrFail($id)]);
    }
    public function edit($id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        return view('proyecto.edit', ["proyecto" => Proyecto::findOrFail($id)]);
    }

    public function update(ProyectoFormRequest $request, $id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        $proyecto = Proyecto::findOrFail($id);
        $proyecto->proyecto = $request->get('proyecto');

        //activar el log de la base de datos
        DB::connection()->enableQueryLog();
        $proyecto->update();
        //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
        LogController::agregarLog(
            2,
            "catalogo_proyecto",
            "Se actualizó el proyecto: " . json_encode($proyecto)
        );
        return Redirect::to('proyecto');

    }

    public function destroy($id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        $proyecto = Proyecto::findOrFail($id);
        //activar el log de la base de datos
        DB::connection()->enableQueryLog();
        $proyecto->delete();
        //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
        LogController::agregarLog(
            3,
            "catalogo_proyecto",
            "Se eliminó el proyecto: " . json_encode($proyecto)
        );

        //Session::flash('message','El proyecto fue eliminado');

        return Redirect::to('proyecto');

    }
}
