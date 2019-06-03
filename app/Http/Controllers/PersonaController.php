<?php

namespace sistema\Http\Controllers;

use Illuminate\Http\Request;
use sistema\Persona;
use sistema\Http\Requests\PersonaFormRequest;
use Illuminate\Support\Facades\Redirect;
use DB;
use Session;
use sistema\Utilidad;
use Illuminate\Support\Facades\Auth;
use sistema\Http\Controllers\Controller;

class PersonaController extends Controller
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
            $personas = DB::table('persona')
                ->where('nombre', 'LIKE', '%' . $query . '%')
                ->orwhere('apellidos', 'LIKE', '%' . $query . '%')
                ->orwhere('cargo', 'LIKE', '%' . $query . '%')
                ->orwhere('Id_persona', 'LIKE', '%' . $query . '%')
                ->orderBy('apellidos', 'asc')
                ->paginate(10);

            $page=$request->get('page')!=null? $request->get('page'):1;

            $numeroRegistros= DB::table('persona')
                ->where('nombre', 'LIKE', '%' . $query . '%')
                ->orwhere('apellidos', 'LIKE', '%' . $query . '%')
                ->orwhere('cargo', 'LIKE', '%' . $query . '%')
                ->orwhere('Id_persona', 'LIKE', '%' . $query . '%')
                ->orderBy('apellidos', 'asc')->count();

            return view('persona.index',
                [
                    'personas' => $personas,
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
        return view('persona.create');
    }

    public function store(PersonaFormRequest $request)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        $persona = new Persona;
        //Obtiene el Id 100 real
        $id=Utilidad::getId("persona","Id_persona");
        $persona->Id_persona=$id;

        $AuxCargo="";

        if($request->get('cargo')==null)
            $AuxCargo ="";
        else
            $AuxCargo=$request->get('cargo');


        $persona->cargo = $AuxCargo;


        $persona->nombre = $request->get('nombre');
        $persona->apellidos = $request->get('apellidos');
        $sector = $request->input('extra');

        if($sector=='Sector Institucional/Gubernamental'){
             $persona->extra =2;
        }
        else if($sector=='Sector Social'){
        $persona->extra =1;
        }
        else {
             $persona->extra =0;
        }

        //activar el log de la base de datos
        DB::connection()->enableQueryLog();
        $persona->save();
        //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
        LogController::agregarLog(
            1,
            "persona",
            "Se agregó la persona: ". json_encode($persona)
        );
        Session::flash('message','La persona fue añadida con id ('.$id.')');

        return Redirect::to('persona');
    }

    public function show($id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        //DOCUMENTOS DE PERSONAS
        //Tabla Cntrl_Persona

        $documentos =DB::table('cntrl_persona as ctp')
            ->join('documento as doc','doc.Id_doc','=','ctp.fk_doc')
            ->where('fk_persona',$id)
            ->orderBy('doc.Id_doc', 'desc')
            ->get();

        //OBRAS DE LA PERSONA
        $obras =DB::table('obra_persona as obp')
            ->join('obras as o','o.id_obra','=','obp.fk_obra')
            ->where('fk_persona',$id)
            ->orderBy('o.id_obra', 'desc')
            ->get();



        return view('persona.verDetalle',
        ["persona" => Persona::findOrFail($id),
        "documentos" => $documentos,
        "obras"=>$obras
        ]);
    }
    public function edit($id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        return view('persona.edit', ["persona" => Persona::findOrFail($id)]);
    }

    public function update(PersonaFormRequest $request, $id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        $persona = Persona::findOrFail($id);
        $AuxCargo="";

        if($request->get('cargo')==null)
            $AuxCargo ="";
        else
            $AuxCargo=$request->get('cargo');


        $persona->cargo = $AuxCargo;
        $persona->nombre = $request->get('nombre');
        $persona->apellidos = $request->get('apellidos');

        $sector =$request->input('extra');
        if($sector=='Sector Institucional/Gubernamental'){
             $persona->extra =2;
        }
        else if($sector=='Sector Social'){
        $persona->extra =1;
        }
        else {
             $persona->extra =0;
        }

        //activar el log de la base de datos
        DB::connection()->enableQueryLog();
        $persona->update();
        //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
        LogController::agregarLog(
            2,
            "persona",
            "Se actualizó la persona: " . json_encode($persona)
        );
        Session::flash('message2','La persona fue editada con id ('.$id.')');

        return Redirect::to('persona');

    }

    public function destroy($id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        $persona = Persona::findOrFail($id);
        //activar el log de la base de datos
        DB::connection()->enableQueryLog();
        $persona->delete();
        //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
        LogController::agregarLog(
            3,
            "persona",
            "Se eliminó la persona: " . json_encode($persona)
        );

        Session::flash('message3','La persona con id ('.$id.') fue eliminada');

        return Redirect::to('persona');

    }
}
