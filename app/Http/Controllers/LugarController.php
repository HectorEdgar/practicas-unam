<?php

namespace sistema\Http\Controllers;

use Illuminate\Http\Request;
use sistema\Lugar;
use sistema\LugarEtnia;
use sistema\DocumentoLugar;
use sistema\ObraLugar;
use sistema\Http\Requests\LugarFormRequest;
use Illuminate\Support\Facades\Redirect;
use DB;
use Session;
use Illuminate\Support\Facades\Log;
use sistema\Utilidad;
use Illuminate\Support\Facades\Auth;
use sistema\Http\Controllers\Controller;

class LugarController extends Controller
{
    public function __construct()
    {
        //si no esta logeado regresa al login
        //$this->middleware('auth');
    }
    /*
    private function validarRoles(){
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin', 'revisor'])) {return Redirect::to('/');}

    }*/
    public function index(Request $request)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}

        if ($request) {
            $query = trim($request->get('searchText'));
            /*
            $lugares = DB::table('lugar')
                ->where('ubicacion', 'LIKE', '%' . $query . '%')
                ->orwhere('pais', 'LIKE', '%' . $query . '%')
                ->orwhere('region_geografica', 'LIKE', '%' . $query . '%')
                ->orwhere('id_lugar', 'LIKE', '%' . $query . '%')
                ->orderBy('id_lugar', 'desc')
                ->paginate(10);
            */

            $lugares =DB::table('lugar as l')
            ->join('paises as p','p.id_pais',"=",'l.pais')
            ->join('region as r','r.id_region',"=",'l.region_geografica')
            ->where( DB::raw("CONCAT( l.ubicacion, '', p.nombre )"), 'LIKE', '%' . $query . '%')
            ->orwhere('l.id_lugar',$query)
            ->orderBy(DB::raw("CONCAT( l.ubicacion, '', p.nombre )"), 'asc')
            ->select('l.id_lugar as id_lugar','l.ubicacion as ubicacion','p.nombre as pais' ,'r.nombrereg as region')
            ->paginate(10);


            $page=$request->get('page')!=null? $request->get('page'):1;

            $numeroRegistros= DB::table('lugar as l')
            ->join('paises as p','p.id_pais',"=",'l.pais')
            ->join('region as r','r.id_region',"=",'l.region_geografica')
            ->where( DB::raw("CONCAT( l.ubicacion, '', p.nombre )"), 'LIKE', '%' . $query . '%')
            ->orwhere('l.id_lugar',$query)
            ->orderBy(DB::raw("CONCAT( l.ubicacion, '', p.nombre )"), 'asc')
            ->select('l.id_lugar as id_lugar','l.ubicacion as ubicacion','p.nombre as pais' ,'r.nombrereg as region')
            ->count();

            return view('lugar.index',
                [
                  
                    'lugares' => $lugares,
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
        $regiones= DB::table('region')->orderBy('nombrereg', 'asc')->get();

        return view('lugar.create',
        [
            'paises'=>$paises,
            'regiones'=>$regiones
        ]);
    }

    public function store(LugarFormRequest $request)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}

        $pais =$request->get('pais');
        $regionGeo = $request->get('region_geografica');
        $ubicacion =$request->get('ubicacion')?$request->get('ubicacion'):'';

        $lugarExistente = Lugar::where('ubicacion',$ubicacion)->where('region_geografica',$regionGeo)->where('pais',$pais)->first(); //Obtengo si existe un lugar con esos datos en la base


        if($pais =="0" || $regionGeo =="0"){
        Session::flash('messageError','Seleccione un país y una región válidos');
        return Redirect::to('lugar/create');

        }else if($lugarExistente) {

            Session::flash('messageError','El lugar ya existe en la base de datos');
            return Redirect::to('lugar/create');



        }else{
        $lugar = new Lugar;
        //Obtiene el Id 100 real
        $id=Utilidad::getId("lugar","id_lugar");
        $lugar->id_lugar=$id;
        $lugar->ubicacion = $request->get('ubicacion')?$request->get('ubicacion'):'';
        $lugar->pais = $pais;
        $lugar->region_geografica = $regionGeo;

        //activar el log de la base de datos
        DB::connection()->enableQueryLog();
        $lugar->save();
        //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
        LogController::agregarLog(
            1,
            "lugar",
            "Se agregó el lugar: ". json_encode($lugar)
        );
        Session::flash('message',' El lugar fue añadido con id ('.$id.')');

        return Redirect::to('lugar');
        }
    }

    public function show($id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}

        //DOCUMENTOS DEL LUGAR
        //Tabla Cntrl_Lugar

        $documentos =DB::table('cntrl_lugar as ca')
            ->join('documento as doc','doc.Id_doc','=','ca.fk_doc')
            ->where('fk_lugar',$id)
            ->orderBy('doc.Id_doc', 'desc')
            ->get();

        //OBRAS DEL LUGAR
        $obras =DB::table('cntrl_ubic as cu')
            ->join('obras as o','o.id_obra','=','cu.fk_obra')
            ->where('fk_lugar',$id)
            ->orderBy('o.id_obra', 'desc')
            ->get();

        //GRUPOS DEL LUGAR
        $grupos =DB::table('cntrl_etnia as ce')
            ->join('etnia as e','e.id_etnia','=','ce.fk_etnia')
            ->where('fk_lugar',$id)
            ->orderBy('e.id_etnia', 'desc')
            ->get();

        return view('lugar.verDetalle',
        ["lugar" => Lugar::findOrFail($id),
        "documentos" => $documentos,
        "obras"=>$obras,
        "grupos"=>$grupos
        ]);
    }
    public function edit($id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        $paises= DB::table('paises')->orderBy('nombre', 'asc')->get();
        $regiones= DB::table('region')->orderBy('nombrereg', 'asc')->get();

        $lugar2 =DB::table('lugar as l')
            ->join('paises as p','p.id_pais',"=",'l.pais')
            ->join('region as r','r.id_region',"=",'l.region_geografica')
            ->where('l.id_lugar', '=', $id)
            ->select('l.id_lugar as id_lugar','l.ubicacion as ubicacion','p.nombre as pais' ,'r.nombrereg as region')
            ->first();

        return view('lugar.edit',
        [
            'paises'=>$paises,
            'regiones'=>$regiones,
            'lugarEdit'=>$lugar2,
            "lugar" => Lugar::findOrFail($id)
        ]);
    }

    public function update(LugarFormRequest $request, $id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        $lugar = Lugar::findOrFail($id);
        $lugar->ubicacion = $request->get('ubicacion') ? $request->get('ubicacion'):'';


        $cambioPais=$request->get('pais');
        $cambioRegion= $request->get('region_geografica');
        if($cambioPais!=0){
        $lugar->pais = $request->get('pais');
        }
        if($cambioRegion!=0){
        $lugar->region_geografica = $request->get('region_geografica');
        }
        //activar el log de la base de datos
        DB::connection()->enableQueryLog();
        $lugar->update();
        //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
        LogController::agregarLog(
            2,
            "lugar",
            "Se actualizó el lugar: " . json_encode($lugar)
        );
        Session::flash('message2',' El lugar con ('.$id.') fue editado!');

        return Redirect::to('lugar');

    }

    public function destroy($id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}

        // se obtien el número de vinculaciones para el lugar

        $numEtnia=LugarEtnia::where('fk_lugar', $id)->count();
        $numLugar=DocumentoLugar::where('fk_lugar', $id)->count();
        $numLugarObra=ObraLugar::where('fk_lugar',$id)->count();
        $lugar = Lugar::findOrFail($id);


        Log::warning($numEtnia);
        Log::warning($numLugar);
        Log::warning($numLugarObra);

        if($numEtnia==0 && $numLugar==0 && $numLugarObra==0 ){

            DB::connection()->enableQueryLog();
            $lugar->delete();
            //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar

            LogController::agregarLog(
                3,
                "lugar",
                "Se eliminó el lugar: " . json_encode($lugar)
            );
    
            Session::flash('message3','El lugar con id ('.$id.') fue eliminado');


        }elseif($numEtnia==1 && $numLugar<=0 &&  $numLugarObra<=0){

           $aux= LugarEtnia::where('fk_lugar', $id)->delete(); // elimina la relación

            //activar el log de la base de datos
            DB::connection()->enableQueryLog();
            $lugar->delete();
            //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar

            LogController::agregarLog(
                3,
                "cntrl_etnia",
                "Se eliminó el vínculo con el  lugar: ".$id
            );
    
            LogController::agregarLog(
                3,
                "lugar",
                "Se eliminó el lugar: " . json_encode($lugar)
            );
    
            Session::flash('message3','El lugar con id ('.$id.') fue eliminado');


            
        }else if( $numLugar==1 && $numEtnia<=0   &&  $numLugarObra<=0){

           $aux= DocumentoLugar::where('fk_lugar', $id)->delete(); // elimina la relación

            //activar el log de la base de datos
            DB::connection()->enableQueryLog();
            $lugar->delete();
            //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar

            LogController::agregarLog(
                3,
                "cntrl_lugar",
                "Se eliminó el vínculo con el  lugar: ".$id
            );
    
            LogController::agregarLog(
                3,
                "lugar",
                "Se eliminó el lugar: " . json_encode($lugar)
            );
    
            Session::flash('message3','El lugar con id ('.$id.') fue eliminado');


        }else if( $numLugarObra==1 && $numEtnia<=0 && $numLugar<=0 ){
          
            $aux= ObraLugar::where('fk_lugar', $id)->delete(); // elimina la relación

            //activar el log de la base de datos
            DB::connection()->enableQueryLog();
            $lugar->delete();
            //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar

            LogController::agregarLog(
                3,
                "cntrl_ubic",
                "Se eliminó el vínculo con el  lugar: ".$id
            );
    
            LogController::agregarLog(
                3,
                "lugar",
                "Se eliminó el lugar: " . json_encode($lugar)
            );
    
            Session::flash('message3','El lugar con id ('.$id.') fue eliminado');
        }else{
            Session::flash('message4','El lugar con id ('.$id.') no se puede eliminar.');

        }

        return Redirect::to('lugar');

    }
}
