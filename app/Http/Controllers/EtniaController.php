<?php

namespace sistema\Http\Controllers;

use Illuminate\Http\Request;
use sistema\Etnia;
use sistema\Http\Requests\EtniaFormRequest;
use Illuminate\Support\Facades\Redirect;
use DB;
use sistema\Utilidad;
use Illuminate\Support\Facades\Auth;

class EtniaController extends Controller
{
    public function __construct()
    {
        //si no esta logeado regresa al login
        //$this->middleware('auth');
    }
    /*
    private function validarRoles(){
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
    }
    */
    public function index(Request $request)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}

        if ($request) {
            $query = trim($request->get('searchText'));
            $etnias = DB::table('etnia')
                ->where('nombre', 'LIKE', '%' . $query . '%')
                ->orwhere('nombre2', 'LIKE', '%' . $query . '%')
                ->orwhere('territorio', 'LIKE', '%' . $query . '%')
                ->orwhere('familia', 'LIKE', '%' . $query . '%')
                ->orwhere('id_etnia', 'LIKE', '%' . $query . '%')
                ->orderBy('id_etnia', 'desc')
                ->paginate(10);

            $page=$request->get('page')!=null? $request->get('page'):1;

            $numeroRegistros= DB::table('etnia')
                ->where('nombre', 'LIKE', '%' . $query . '%')
                ->orwhere('nombre2', 'LIKE', '%' . $query . '%')
                ->orwhere('territorio', 'LIKE', '%' . $query . '%')
                ->orwhere('familia', 'LIKE', '%' . $query . '%')
                ->orwhere('id_etnia', 'LIKE', '%' . $query . '%')
                ->orderBy('id_etnia', 'desc')
                ->count();

            return view('etnia.index',
                [
                    'etnias' => $etnias,
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
        return view('etnia.create');
    }

    public function store(EtniaFormRequest $request)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        $etnia = new Etnia;
        //Obtiene el Id 100 real
        $etnia->id_etnia=Utilidad::getId("etnia","id_etnia");
        $etnia->nombre = $request->get('nombre')?$request->get('nombre'):"";
        $etnia->nombre2= $request->get('nombre2')?$request->get('nombre2'):"";
        $etnia->territorio = $request->get('territorio')?$request->get('territorio'):"";
        $etnia->familia = $request->get('familia')?$request->get('familia'):"";

        //activar el log de la base de datos
        DB::connection()->enableQueryLog();
        $etnia->save();
        //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
        LogController::agregarLog(
            1,
            "Etnia",
            "Se agregó la Etnia: ". json_encode($etnia)
        );

        return Redirect::to('etnia');
    }

    public function show($id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        return view('etnia.show', ["etnia" => Etnia::findOrFail($id)]);
    }
    public function edit($id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        return view('etnia.edit', ["etnia" => Etnia::findOrFail($id)]);
    }

    public function update(EtniaFormRequest $request, $id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        $etnia = Etnia::findOrFail($id);
        $etnia->nombre = $request->get('nombre')?$request->get('nombre'):"";
        $etnia->nombre2= $request->get('nombre2')?$request->get('nombre2'):"";
        $etnia->territorio = $request->get('territorio')?$request->get('territorio'):"";
        $etnia->familia = $request->get('familia')?$request->get('familia'):"";
        //activar el log de la base de datos
        DB::connection()->enableQueryLog();
        $etnia->update();
        //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
        LogController::agregarLog(
            2,
            "etnia",
            "Se actualizó la Etnia: " . json_encode($etnia)
        );
        return Redirect::to('etnia');

    }

    public function destroy($id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        $etnia = Etnia::findOrFail($id);
        //activar el log de la base de datos
        DB::connection()->enableQueryLog();
        $etnia->delete();
        //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
        LogController::agregarLog(
            3,
            "Etnia",
            "Se eliminó el etnia: " . json_encode($etnia)
        );

        //Session::flash('message','El Etnia fue eliminado');

        return Redirect::to('etnia');

    }
}
