<?php

namespace sistema\Http\Controllers;

use Illuminate\Http\Request;
use sistema\ObraLugar;
use sistema\Lugar;
use sistema\Obra;
use sistema\Http\Requests\LugarFormRequest;
use sistema\Http\Requests\ObraLugarFormRequest;
use sistema\Http\Requests\StoreMultipleFormRequest;
use Illuminate\Support\Facades\Redirect;
use DB;
use Session;
use sistema\Utilidad;
use sistema\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use sistema\DocumentoLugar;
use sistema\LugarEtnia;

class ObraLugarController extends Controller
{
     public function index(Request $request)
    {
    }
    public function create()
    {
    }
    /*
    private function validarRoles(){
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin'])) {
            return Redirect::to('/');
        }
    }
    */
    public function store(ObraLugarFormRequest $request)
    {
                if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
                $ruta = "/obra_lugar/ligar/".$request->get('fk_obra');
                $respuesta = ObraLugar::firstOrCreate([
                    'fk_obra' => $request->get('fk_obra'),
                    'fk_lugar' => $request->get('fk_lugar'),
                    'latitud'=> $request->get('latitud'),
                    'longitud'=>$request->get('longitud'),
                    'complejo' => $request->get('complejo')
                    ],
                    [
                    'fk_obra' => $request->get('fk_obra'),
                    'fk_lugar' => $request->get('fk_lugar'),
                    'latitud'=> $request->get('latitud'),
                    'longitud'=>$request->get('longitud'),
                    'complejo' => $request->get('complejo')
                    ]);

                //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    1,
                    "obra_lugar",
                    "Se agregó el vinculo: ". json_encode($respuesta)
                );
                Session::flash('flash_message', ' ¡Vinculación exitosa!');
               // Session::flash('flash_message2', 'Ese vinculo ya existe!'.$respuesta);

                return Redirect::to($ruta);
    }
    public static function eliminarObraCascada($idObra)
    {

        $obraLugares=ObraLugar::where('fk_obra',$idObra)->get();

        foreach ($obraLugares as $obraLugar) {

            $numAuxObraLugares=ObraLugar::where('fk_lugar',$obraLugar->fk_lugar)->count();
            Log::warning("numAuxObraLugares". $numAuxObraLugares);


            if($numAuxObraLugares!=0){
                if($numAuxObraLugares==1){

                    $numAuxDocumentoLugares=DocumentoLugar::where('fk_lugar',$obraLugar->fk_lugar)->count();

                   
                    if($numAuxDocumentoLugares==0){
                        if($numAuxDocumentoLugares==0){

                            $numAuxLugarEtnia=LugarEtnia::where('fk_lugar', $obraLugar->fk_lugar)->count();

                            
                         

                            if($numAuxLugarEtnia==0){

                        
                                ObraLugar::where('fk_obra', $idObra)
                                    ->where('fk_lugar', $obraLugar->fk_lugar)->delete();

                                Lugar::where('id_lugar', $obraLugar->fk_lugar)->delete();
                                return;
                            }
                        }
                    }
                    ObraLugar::where('fk_obra', $idObra)
                        ->where('fk_lugar', $obraLugar->fk_lugar)->delete();
                }else {
                    ObraLugar::where('fk_obra', $idObra)
                        ->where('fk_lugar', $obraLugar->fk_lugar)->delete();
                }
            }




        }

        



    }
    //AQUI SE MUESTRAN LOS lugars QUE SE PUEDEN VINCULAR CON EL DOCUMENTO
    public function ligarObra(Request $request,$id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
         $obra =Obra::findOrFail($id);
         $obraClase =Obra::findOrFail($id);

         if ($request) {
            $query = trim($request->get('searchText'));

            $lugaresdelaObra =DB::table('cntrl_ubic as ca')
            ->join('lugar as a','a.id_lugar','=','ca.fk_lugar')
            ->join('paises as p','p.id_pais',"=",'a.pais')
            ->join('region as r','r.id_region',"=",'a.region_geografica')
            //->join('obras as o','o.id_obra',"=",'ca.complejo')
            ->select('a.id_lugar as id_lugar','a.ubicacion as ubicacion',
            'p.nombre as pais' ,'r.nombrereg as region','ca.latitud as latitud',
            'ca.longitud as longitud','ca.complejo as complejo')
            //'o.id_obra as complejo','o.extra as extra','o.nombre as nombrecomplejo')
            ->where('fk_obra',$obra->id_obra)
            ->orderBy('id_lugar', 'desc')
            ->get();

            $complejo = DB::table('obras as o')
            ->join('cntrl_ubic as cp','cp.complejo',"=",'o.id_obra')
            ->select('o.id_obra as id_obra','o.nombre as nombre')
            ->where('fk_obra',$obra->id_obra)
            ->first();

           $complejo = DB::table('obras as o')
            ->join('cntrl_ubic as cp','cp.complejo',"=",'o.id_obra')
            ->select('o.id_obra as id_obra','o.nombre as nombre')
            ->where('fk_obra',$obra->id_obra)
            ->first();



            $idLigados = DB::table('cntrl_ubic as ca')
            ->join('lugar as a','a.id_lugar','=','ca.fk_lugar')
                ->where('fk_obra', $obra->id_obra)
                ->orderBy('id_lugar', 'desc')
                ->pluck("a.id_lugar")
                ->all();

            $numeroRegistros = DB::table('lugar')
               ->where('ubicacion', 'LIKE', '%' . $query . '%')
                ->orwhere('pais', 'LIKE', '%' . $query . '%')
                ->orwhere('region_geografica', 'LIKE', '%' . $query . '%')
                ->orwhere('id_lugar', 'LIKE', '%' . $query . '%')
                ->count() - count($idLigados);


            //Desde este momento se empieza a crear la MAGIA
            $lugares="";
            $numeroElementos = 10;
            $auxIdLigados=$idLigados;
            while (true) {
                $aux = $numeroElementos;

                $idLugares = DB::table('lugar')
                ->where('ubicacion', 'LIKE', '%' . $query . '%')
                ->orwhere('pais', 'LIKE', '%' . $query . '%')
                ->orwhere('region_geografica', 'LIKE', '%' . $query . '%')
                ->orwhere('id_lugar', 'LIKE', '%' . $query . '%')
                ->orderBy('id_lugar', 'desc')
                ->paginate($numeroElementos)
                ->pluck("id_lugar");


                for ($i=0; $i < count($idLugares); $i++) {
                    for ($j=0; $j < count($auxIdLigados) ; $j++) {
                        if ($idLugares[$i]== $auxIdLigados[$j]) {
                            $numeroElementos= $numeroElementos + 1;
                            $aux=$aux-1;
                            unset($auxIdLigados[$j]);
                            $auxIdLigados =array_values($auxIdLigados);
                            break;
                        }
                    }
                }
                if($aux>=10 || $numeroRegistros<10) {
                    $lugares = DB::table('lugar as l')
                        ->where('l.ubicacion', 'LIKE', '%' . $query . '%')
                        ->orwhere('l.pais', 'LIKE', '%' . $query . '%')
                        ->orwhere('l.region_geografica', 'LIKE', '%' . $query . '%')
                        ->orwhere('l.id_lugar', 'LIKE', '%' . $query . '%')
                        ->join('paises as p','p.id_pais',"=",'l.pais')
                        ->join('region as r','r.id_region',"=",'l.region_geografica')
                       ->select('l.id_lugar as id_lugar','l.ubicacion as ubicacion',
                        'p.nombre as pais' ,'r.nombrereg as region_geografica')
                        ->orderBy('l.id_lugar', 'desc')
                        ->paginate($numeroElementos);

                    $lugaresFinal=array();
                    foreach ($lugares as $item) {
                        $bandera = true;
                        foreach ($idLigados as $idLigado) {
                            if($item->id_lugar == $idLigado){
                                unset($idLigado);
                                $auxIdLigados = array_values($idLigados);
                                $bandera=false;
                                break;
                            }
                        }
                        if($bandera) {
                            array_push($lugaresFinal,$item);
                        }
                    }
                    break;
                }
            }

            $page=$request->get('page');

            if($page==null){
                $page=1;
            }

            return view('obraLugar.index',
                [
                    'obra_lugar'=>$obra->id_obra,
                    'complejo'=>$complejo,
                    'lugares' => $lugaresFinal,
                    'lugaresdelaObra' => $lugaresdelaObra,
                    "searchText" => $query,
                    "totalRegistros"=> $numeroRegistros,
                    "page"=> $page,
                    'obra' =>$obra,
                ]
            );
        }
    }

    //CUANDO UN lugar NO EXISTE EN LA BASE DE DATOS
    //ESTA VISTA MANDA EL FORMULARIO PARA AGREGAR UN NUEVO lugar
    public function nuevoObraLugar(Request $request,$id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
         $obra =Obra::findOrFail($id);
           $paises= DB::table('paises')->orderBy('nombre', 'asc')->get();
            $regiones= DB::table('region')->orderBy('nombrereg', 'asc')->get();
            $complejos = DB::table('obras as p')
            ->where('extra',2)
            ->orderBy('nombre', 'asc')
            ->get();

        return view('obraLugar.createObraLugar',
        [
            'obra'=> $obra,
            'paises'=>$paises,
            'regiones'=>$regiones,
            'complejos'=>$complejos
        ]);

    }

    public function editar($id,$id2)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        $obra =Obra::findOrFail($id);
        $lugar =Lugar::findOrFail($id2);

        $vinculo = ObraLugar::where('fk_lugar', $id2)->where('fk_obra', $id)->first();

        $paises= DB::table('paises')->orderBy('nombre', 'asc')->get();
        $regiones= DB::table('region')->orderBy('nombrereg', 'asc')->get();
        $complejos = DB::table('obras as p')
            ->where('extra',2)
            ->orderBy('nombre', 'asc')
            ->get();

        return view('obraLugar.editar',
        [
            'paises'=>$paises,
            'regiones'=>$regiones,
            'obra'=> $obra,
            "lugar" => $lugar,
            'complejos'=>$complejos,
            'vinculo'=>$vinculo

        ]);
    }


    public function editarVinculo(StoreMultipleFormRequest $request)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        $pais =$request->get('pais');
        $regionGeo = $request->input('region_geografica');

        if($pais =="0" || $regionGeo =="0"){
        Session::flash('messageError','Seleccione un país y una región que sean válidas');
        return Redirect::to('obra_lugar/nuevoObra/'.$id);

        }

        else {
        //ID DELa obra
        $obra =Obra::findOrFail($request->get('fk_obra'));
        //datos del lugar
        $lugar = Lugar::findOrFail($request->get('fk_lugar'));

        $lugar->ubicacion = $request->get('ubicacion');
        $lugar->pais = $pais;
        $lugar->region_geografica = $regionGeo;

        $vinculo = DB::table('cntrl_ubic')
        ->where('fk_lugar', $request->get('fk_lugar'))
        ->where('fk_obra',$request->get('fk_obra'))
        ->update(['latitud' => $request->get('latitud'),
                  'longitud'=>$request->get('longitud'),
                  'complejo'=>$request->get('complejo')]);

        DB::connection()->enableQueryLog();
        //SE GUARDA EL lugar
        $lugar->update();
        //SE guarda los datos de vinculo
        //$vinculo->update();


        //REGRESA A LA VISTA ORIGINAL DONDE PODRAS VER LOS lugars LIGADOS
            $ruta = "/obra_lugar/ligar/".$obra->id_obra;
            Session::flash('flash_message3', ' ¡El vínculo se modificó exitosamente!');

            //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    1,
                    "lugar",
                    "Se modificó el lugar: ". json_encode($lugar)
                );

                LogController::agregarLog(
                    1,
                    "obra_lugar",
                    "Se modifico el vinculo: ". json_encode($vinculo)
                );

            return Redirect::to($ruta);
            }
    }




    public function vincular($id,$id2)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        $obra =Obra::findOrFail($id);
        $lugar =Lugar::findOrFail($id2);
        $complejos = DB::table('obras as p')
        ->where('extra',2)
        ->orderBy('nombre', 'asc')
        ->get();

        return view('obraLugar.vincular',
        [
            'obra'=> $obra,
            'lugar'=>$lugar,
            'complejos'=>$complejos,
        ]);

    }


    public function show()
    {
    }

    public function edit()
    {
        //
    }



     //ESTE METODO CREA el lugar Y LO LIGA AUTOMATICAMENTE
    //CON ESta obra


    //StoreMultipleFormRequest es una clase compuesta de varios form request
    //en este caso tiene incluido el de la tabla cntrl_ubic y el de lugar a la vez
    public function update(StoreMultipleFormRequest $request, $id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}

        $pais =$request->get('pais');
        $regionGeo = $request->input('region_geografica');
        $ubicacion =  $request->get('ubicacion')?$request->get('ubicacion'):'';

        $lugarExistente = Lugar::where('pais',$pais)->where('region_geografica',$regionGeo)->where('ubicacion',$ubicacion)->first(); //Obtengo si existe un lugar con esos datos en la base

        $obra =Obra::findOrFail($id);
        //ID DELa obra
     
        if($pais =="0" || $regionGeo =="0"){
        Session::flash('messageError','Seleccione un País y una Región Validos');
        return Redirect::to('obra_lugar/nuevoObra/'.$id);

        }else if($lugarExistente){

            $vinculoObraLugar = ObraLugar::where('fk_obra',$id)->where('fk_lugar',$lugarExistente->id_lugar)->first();

            if($vinculoObraLugar==null){
                $obra =Obra::findOrFail($id);


                //se crea ligadura con datos correspondientes

                $respuesta = ObraLugar::firstOrCreate([
                    'fk_obra' => $obra->id_obra,
                    'fk_lugar' => $lugarExistente->id_lugar,
                    'latitud'=> $request->get('latitud'),
                    'longitud'=>$request->get('longitud'),
                    'complejo' => $request->get('complejo')
                    ],
                    [
                    'fk_obra' =>  $obra->id_obra,
                    'fk_lugar' => $lugarExistente->id_lugar,
                    'latitud'=> $request->get('latitud'),
                    'longitud'=>$request->get('longitud'),
                    'complejo' => $request->get('complejo')
                    ]);
                //REGRESA A LA VISTA ORIGINAL DONDE PODRAS VER LOS lugars LIGADOS
                    $ruta = "/obra_lugar/ligar/".$obra->id_obra.$respuesta;
                 Session::flash('flash_message3', 'La obra se vinculó al Lugar con Id : ('.$lugarExistente->id_lugar.')');

                    //activar el log de la base de datos
                 DB::connection()->enableQueryLog();
                    //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                     LogController::agregarLog(
                    1,
                    "lugar_obra",
                    "Se agregó el vinculo: ". json_encode($respuesta)
                );

                return Redirect::to($ruta);




            }else{

                Session::flash('flash_message2', 'El vínculo ya existe.');
                return Redirect::to("/obra_lugar/ligar/".$obra->id_obra);

            }



        }else {
        
        //datos del lugar
        $lugar = new Lugar;
        $idLugar = Utilidad::getId("lugar","id_lugar");
        $lugar->Id_lugar=$idLugar;
        $lugar->ubicacion = $ubicacion;
        $lugar->pais = $pais;
        $lugar->region_geografica = $regionGeo;

        DB::connection()->enableQueryLog();
        //SE GUARDA EL lugar
        $lugar->save();
        //SE CREA LA LIGADURA
        $respuesta = ObraLugar::firstOrCreate([
                    'fk_obra' => $obra->id_obra,
                    'fk_lugar' => $idLugar,
                    'latitud'=> $request->get('latitud'),
                    'longitud'=>$request->get('longitud'),
                    'complejo' => $request->get('complejo')
                    ],
                    [
                    'fk_obra' =>  $obra->id_obra,
                    'fk_lugar' => $idLugar,
                    'latitud'=> $request->get('latitud'),
                    'longitud'=>$request->get('longitud'),
                    'complejo' => $request->get('complejo')
                    ]);
        //REGRESA A LA VISTA ORIGINAL DONDE PODRAS VER LOS lugars LIGADOS
            $ruta = "/obra_lugar/ligar/".$obra->id_obra.$respuesta;
            Session::flash('flash_message3', 'La obra se vinculó al Lugar con Id : ('.$idLugar.')');

            //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    1,
                    "lugar_obra",
                    "Se agregó el vinculo: ". json_encode($respuesta)
                );

                LogController::agregarLog(
                    1,
                    "lugar",
                    "Se agregó el lugar: ". json_encode($lugar)
                );

            return Redirect::to($ruta);
            }
    }

    //AQUI SE DESVINCULA la obra DEL lugar
    public function destroy($id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        //Buscar el vinculo en la tabla obra_ correspondiente
        $aux =DB::table('cntrl_ubic')
        ->where('fk_lugar',$id)
        ->where('fk_obra',$obraumento->id_obra)
        ->get();
        //y se elimina
        $aux->delete();
        return Redirect::to("/cntrl_lugar/ligar/".$obra->id_obra);
    }

     public function destroy2($id,$id2)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        //Buscar el vinculo en la tabla cntrl correspondiente
        //y se elimina

        $numLugar=ObraLugar::where('fk_lugar', $id)->count();
        $numlugarDocumento=DocumentoLugar::where('fk_lugar',$id)->count();
        $numLugarGrupo=LugarEtnia::where('fk_lugar',$id)->count();
        if($numLugar>1 || $numlugarDocumento>0 || $numLugarGrupo>0){
            ObraLugar::where('fk_lugar', $id)->where('fk_obra', $id2)->delete();
        }else{
            ObraLugar::where('fk_lugar', $id)->where('fk_obra', $id2)->delete();
            Lugar::where('id_lugar',$id)->delete();
        }



        Session::flash('flash_message4', ' ¡El lugar se desvinculó existosamente!');
        //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    3,
                    "lugar_obra",
                    "Se eliminó el vinculo: ObraLugar(".$id.") del Doc (".$id2.")"
                );
        return Redirect::to("/obra_lugar/ligar/".$id2);
    }
}
