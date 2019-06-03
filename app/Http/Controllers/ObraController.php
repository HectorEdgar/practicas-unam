<?php

namespace sistema\Http\Controllers;

use Illuminate\Http\Request;
use sistema\Obra;
use sistema\Http\Requests\ObraFormRequest;
use Illuminate\Support\Facades\Redirect;
use DB;
use Session;
use sistema\Utilidad;
use Illuminate\Support\Facades\Auth;
use sistema\DocumentoObra;
use sistema\ObraEje;
use sistema\ObraPersona;
use sistema\ObraTema;
use Illuminate\Support\Facades\Log;

class ObraController extends Controller
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
            $obras = DB::table('obras')
                ->where('nombre', 'LIKE', '%' . $query . '%')
                ->orwhere('extra', 'LIKE', '%' . $query . '%')
                ->orwhere('revisado', 'LIKE', '%' . $query . '%')
                ->orwhere('id_obra', 'LIKE', '%' . $query . '%')
                ->orderBy('id_obra', 'desc')
                ->paginate(10);

            $page=$request->get('page')!=null? $request->get('page'):1;

            $numeroRegistros= DB::table('obras')
                ->where('nombre', 'LIKE', '%' . $query . '%')
                ->orwhere('extra', 'LIKE', '%' . $query . '%')
                ->orwhere('revisado', 'LIKE', '%' . $query . '%')
                ->orwhere('id_obra', 'LIKE', '%' . $query . '%')
                ->orderBy('id_obra', 'desc')
                ->count();



            return view('obras.index',
                [
                    'obras' => $obras,
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
        return view('obras.create');
    }

    public function store(ObraFormRequest $request)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        $obra = new Obra;
        //Obtiene el Id 100 real
        $id=Utilidad::getId("obras","id_obra");
        $obra->id_obra=$id;
        $obra->nombre = $request->get('nombre');
        $tipo= $request->get('tipo');
        if($tipo=='obra'){
            $obra->extra =1;
        }
        else if($tipo =='complejo'){
            $obra->extra =2;
        }

        //REVISADO 1
        // NO REVISADO=0
        $obra->revisado = 0;

        //activar el log de la base de datos
        DB::connection()->enableQueryLog();
        $obra->save();
        //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
        LogController::agregarLog(
            1,
            "obra",
            "Se agregó la obra: ". json_encode($obra)
        );
        Session::flash('message','La Obra fue añadida con id ('.$id.')');

        return Redirect::to('obras');
    }

    public function show($id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}

        $obra = Obra::findOrFail($id);

        if($obra->extra==1){
            $tipo = "Obra";
        }
        else if ($obra->extra==2){
             $tipo = "Complejo";
        }
            // se obtienen los documeentos  relacionados a la obra
        $documentos = DB::table('documento as d')
        ->join('obra_doc as od','od.fk_doc',"=",'.d.Id_doc')
        ->join('obras as o','o.id_obra',"=",'od.fk_obra')
        ->join('status as s','s.id_status',"=",'od.status')
        ->leftjoin('fecha as f','f.fk_doc',"=",'d.Id_doc')
        ->leftjoin('fecha_extra as fx','fx.id_fx',"=",'d.Id_doc')
        ->select('d.titulo as titulo','d.Id_doc as Id_doc','s.tip_est as status','od.revisado as revisado','od.investigador as investigador',
        'f.fecha as fecha','fx.mes as mes', 'fx.mes2 as mes2', 'fx.anio as anio','d.fecha_publi as fecha_publi','d.linea as linea','od.fk_obra as fk_obra')
        ->where('fk_obra',$obra->id_obra)
        ->get();



         // se obtienen los ejes  relacionados a la obra
        $ejes =DB::table('eje as e')
        ->join('eje_obra as eo','eo.fk_eje',"=",'e.Id_eje')
        ->join('obras as o','o.id_obra',"=",'eo.fk_obra')
        ->where('fk_obra',$obra->id_obra)
        ->get();

         // se obtienen los lugares  relacionados a la obra

       $lugares =DB::table('lugar as l')
        ->join('cntrl_ubic as cp','cp.fk_lugar',"=",'l.id_lugar')
        ->join('paises as p','p.id_pais',"=",'l.pais')
        ->join('region as r','r.id_region',"=",'l.region_geografica')
        //->join('obras as o','o.id_obra',"=",'cp.complejo')
        ->select('l.id_lugar as id','l.ubicacion as ubicacion',
        'p.nombre as pais' ,'r.nombrereg as region','cp.latitud as latitud',
        'cp.longitud as longitud','cp.complejo as complejo')
        //'cp.complejo as complejo','o.extra as extra')
        ->where('fk_obra',$obra->id_obra)
        ->get();



         $instituciones =DB::table('institucion as i')
        ->join('obra_inst as cp','cp.fk_inst',"=",'i.Id_institucion')
        ->join('obras as o','o.id_obra',"=",'cp.fk_obra')
        ->select('i.Id_institucion as id','i.nombre as nombre',
        'i.siglas as siglas','i.pais as pais','i.localidad as localidad',
        'cp.extra as extra')
        ->where('fk_obra',$obra->id_obra)
        ->get();



         $proyectos =DB::table('catalogo_proyecto as cat')
        ->join('obra_proyec as cp','cp.fk_proyec',"=",'cat.id_proyecto')
        ->join('obras as o','o.id_obra',"=",'cp.fk_obra')
        ->select('cat.proyecto as proyecto','cat.id_proyecto as id')
        ->where('fk_obra',$obra->id_obra)
        ->get();

        $temas =DB::table('temas as t')
        ->join('obra_tema as cp','cp.fk_tema',"=",'t.id_tema')
        ->join('obras as o','o.id_obra',"=",'cp.fk_obra')
        ->where('fk_obra',$obra->id_obra)
        ->get();

        $actoresSociales = DB::table('persona as p')
        ->join('obra_persona as cp','cp.fk_persona',"=",'p.Id_persona')
        ->join('obras as o','o.id_obra',"=",'cp.fk_obra')
        ->where('fk_obra',$obra->id_obra)
        ->select('p.nombre as nombre','p.Id_persona','p.apellidos','p.cargo')
        ->get();

        
    
        $obrasAux = DB::table('obras as p')
        ->join('obra_obra as cp','cp.fk_obra2',"=",'p.id_obra')
        ->where('fk_obra',$obra->id_obra);
       

        $obras = DB::table('obras as p')
        ->join('obra_obra as cp','cp.fk_obra',"=",'p.id_obra')
        ->where('fk_obra2',$obra->id_obra)
        ->union($obrasAux)  // Se realiza una union con la conulta de obrasAux. 
        ->groupBy('id_obra')
        ->get();

        
        

        

        Log::warning( $actoresSociales);



        return view('obras.verDetalle',
         ["obra" => $obra,
        "obras"=> $obras,
        "ejes"=>$ejes,
        "lugares"=>$lugares,
        "instituciones"=>$instituciones,
        "actoresSociales"=>$actoresSociales,
        "proyectos"=>$proyectos,
        "temas"=>$temas,
         "tipo"=>$tipo,
		 "documentos"=> $documentos
         ])
        ;
    }
    public function edit($id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        return view('obras.edit', ["obra" => Obra::findOrFail($id)]);
    }

    public function update(ObraFormRequest $request, $id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        $obra = Obra::findOrFail($id);
        $obra->nombre = $request->get('nombre');
        $tipo= $request->get('tipo');
        if($tipo=='obra'){
            $obra->extra =1;
        }
        else if($tipo =='complejo'){
            $obra->extra =2;
        }
        $obra->revisado =$request->get('revisado');
        //activar el log de la base de datos
        DB::connection()->enableQueryLog();
        $obra->update();
        //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
        LogController::agregarLog(
            2,
            "obra",
            "Se actualizó la obra: " . json_encode($obra)
        );
        Session::flash('message2','La Obra con id ('.$id.') fue editada');

        return Redirect::to('obras');

    }

    public function destroy($id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}

        DocumentoObra::where("fk_obra",$id)->delete();

        ObraEjeController::eliminarObraCascada($id);
        ObraLugarController::eliminarObraCascada($id);
        ObraInstitucionController::eliminarObraCascada($id);
        ObraPersonaController::eliminarObraCascada($id);
        ObraTemaController::eliminarObraCascada($id);
        ObraProyectoController::eliminarObraCascada($id);
        ObraObraController::eliminarObraCascada($id);





        $obra = Obra::findOrFail($id);
        //activar el log de la base de datos
        DB::connection()->enableQueryLog();
        $obra->delete();
        //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
        LogController::agregarLog(
            3,
            "obra",
            "Se eliminó la obra: " . json_encode($obra)
        );
        Session::flash('message3','La Obra con id ('.$id.') fue eliminada');
        return Redirect::to('obras');

    }

    public function validarCoordenadas($id)
    {
        $obra = Obra::findOrFail($id);
        $obra->revisado ==1? $obra->revisado=0:  $obra->revisado=1;

        if($obra->save()){

            DB::connection()->enableQueryLog();

            //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
        LogController::agregarLog(
           2,
           "Obra",
           "Se actualizó el estado de las coordendas de la obra: ". json_encode($obra)
        );
        }
        return back();
    }


}