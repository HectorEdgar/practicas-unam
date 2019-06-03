<?php

namespace sistema\Http\Controllers;

use Illuminate\Http\Request;
use sistema\Institucion;
use sistema\Http\Requests\InstitucionFormRequest;
use Illuminate\Support\Facades\Redirect;
use DB;
use Session;
use sistema\Utilidad;
use Illuminate\Support\Facades\Auth;
use sistema\Http\Controllers\Controller;

class InstitucionController extends Controller
{
    public function __construct()
    {
        //si no esta logeado regresa al login
        //$this->middleware('auth');
    }

    /*
    private function validarRoles(){
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin', 'revisor'])) {return Redirect::to('/');}
    }
    */
    public function index(Request $request)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}

        if ($request) {
            $query = trim($request->get('searchText'));
            $instituciones = DB::table('institucion')
                ->where('nombre', 'LIKE', '%' . $query . '%')
                ->orwhere('siglas','LIKE','%' .$query . '%')
                ->orwhere('Id_institucion','LIKE','%' .$query . '%')
                ->orderBy('nombre', 'asc')
                ->paginate(10);
            $page=$request->get('page')!=null? $request->get('page'):1;

            $numeroRegistros = DB::table('institucion')
                ->where('nombre', 'LIKE', '%' . $query . '%')
                ->orwhere('siglas','LIKE','%' .$query . '%')
                ->orwhere('Id_institucion','LIKE','%' .$query . '%')
                ->orderBy('nombre', 'asc')
                ->count();

            return view('institucion.index',
                [
                    'instituciones' => $instituciones,
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
        $paises= DB::table('paises')->orderBy('nombre', 'asc')->get();

        return view('institucion.create',[
            'paises'=>$paises,
        ]);
    }

    public function store(InstitucionFormRequest $request)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        $institucion = new Institucion;
        //Obtiene el Id 100 real
        $id=Utilidad::getId("institucion","Id_institucion");

        $AuxSiglas ="";
        if($request->get('siglas')==null)
            $AuxSiglas ="";
        else
            $AuxSiglas=$request->get('siglas');

        $institucion->Id_institucion=$id;
        $institucion->nombre= $request->get('nombre');
		$institucion->siglas= $AuxSiglas;
 		$institucion->pais= $request->get('pais') ?$request->get('pais'):'';
 		$institucion->localidad= " ";
        $sector = $request->input('extra');

        if($sector=='Sector Institucional/Gubernamental'){
             $institucion->extra =2;
        }
        else if($sector=='Sector Social'){
        $institucion->extra =1;
        }
        else {
             $institucion->extra =0;
        }

        //activar el log de la base de datos
        DB::connection()->enableQueryLog();
        $institucion->save();
        //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
        LogController::agregarLog(
            1,
            "institución",
            "Se agregó la institución: ". json_encode($institucion)
        );
        Session::flash('message','La institución fue añadida con id ('.$id.')');

        return Redirect::to('institucion');
    }

    public function show($id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        //DOCUMENTOS DE LA INSTITUCIÓN
        //Tabla Cntrl_INSTITUCIÓN

        $documentos =DB::table('cntrl_instit as ctp')
            ->join('documento as doc','doc.Id_doc','=','ctp.fk_doc')
            ->where('fk_instit',$id)
            ->orderBy('doc.Id_doc', 'desc')
            ->get();

        //OBRAS DE LA INSTITUCIÓN
        $obras =DB::table('obra_inst as obp')
            ->join('obras as o','o.id_obra','=','obp.fk_obra')
            ->where('fk_inst',$id)
            ->orderBy('o.id_obra', 'desc')
            ->get();


        return view('institucion.verDetalle',
        ["institucion" => Institucion::findOrFail($id),
        "documentos" => $documentos,
        "obras"=>$obras
        ]);

    }
    public function edit($id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        $paises= DB::table('paises')->orderBy('nombre', 'asc')->get();
        return view('institucion.edit',
        ["institucion" => Institucion::findOrFail($id),
        "paises"=> $paises
        ]);
    }

    public function update(InstitucionFormRequest $request, $id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        $institucion = Institucion::findOrFail($id);
        $institucion->Id_institucion=$id;

        $AuxSiglas ="";
        if($request->get('siglas')==null)
            $AuxSiglas ="";
        else
            $AuxSiglas=$request->get('siglas');

        $institucion->nombre= $request->get('nombre');
		$institucion->siglas= $AuxSiglas;
 		$institucion->pais= $request->get('pais')?$request->get('pais'):'';
 		$institucion->localidad= " ";
        $sector = $request->input('extra');

        if($sector=='Sector Institucional/Gubernamental'){
             $institucion->extra =2;
        }
        else if($sector=='Sector Social'){
        $institucion->extra =1;
        }
        else {
             $institucion->extra =0;
        }

        //activar el log de la base de datos
        DB::connection()->enableQueryLog();
        $institucion->update();
        //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
        LogController::agregarLog(
            2,
            "institución",
            "Se actualizó la institucion: " . json_encode($institucion)
        );
        Session::flash('message2','La institución con id ('.$id.') fue editada ');

        return Redirect::to('institucion');

    }

    public function destroy($id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        $institucion = Institucion::findOrFail($id);
        //activar el log de la base de datos
        DB::connection()->enableQueryLog();
        $institucion->delete();
        //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
        LogController::agregarLog(
            3,
            "institución",
            "Se eliminó la institución: " . json_encode($institucion)
        );

        Session::flash('message3','La institución con id ('.$id.') fue eliminada');

        return Redirect::to('institucion');

    }
}