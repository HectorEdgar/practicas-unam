<?php

namespace sistema\Http\Controllers;

use Illuminate\Http\Request;
use sistema\DocumentoLugar;
use sistema\Documento;
use sistema\Lugar;
use sistema\Http\Requests\LugarFormRequest;
use sistema\Http\Requests\DocumentoLugarFormRequest;
use Illuminate\Support\Facades\Redirect;
use DB;
use Session;
use sistema\Utilidad;
use sistema\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use sistema\ObraLugar;
use sistema\DocumentoObra;
use sistema\LugarEtnia;

class DocumentoLugarController extends Controller
{
     public function index(Request $request)
    {
    }
    public function create()
    {
    }
    /*
    private function validarRoles(){
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
    }
    */
    public function store(DocumentoLugarFormRequest $request)
    {
                if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
                $ruta = "/cntrl_lugar/ligar/".$request->get('fk_doc');
                $respuesta = DocumentoLugar::firstOrCreate([
                    'fk_doc' => $request->get('fk_doc'),
                    'fk_lugar' => $request->get('fk_lugar')],
                    [
                    'fk_doc' => $request->get('fk_doc'),
                    'fk_lugar' => $request->get('fk_lugar')
                    ]);

                //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    1,
                    "cntrl_lugar",
                    "Se agregó el vínculo: ". json_encode($respuesta)
                );
                Session::flash('flash_message', '¡Vinculación exitosa!');
               // Session::flash('flash_message2', 'Ese vinculo ya existe!'.$respuesta);

                return Redirect::to($ruta);
    }
    public static function eliminarDocumentoCascada($idDocumento)
    {

        //Primero se saca la lista de los lugares que se vincularon al documento
        //Se iterra la lista y se busca si el lugar esta en otro documento  y si está se borra solo la referencia
        //Si no se busca en la tabla obraLugar si hay un registro existente se elimina solo la referencia
        //si no se busca en la tabla LugarEtnia si hay registros se elimina la referencia si no se elimina solo el lugar
        //too hard :´v


        $documentoLugares= DocumentoLugar::where('fk_doc', $idDocumento)->get();

        foreach ($documentoLugares as $documentoLugar) {

            $auxNumDocumentoLugares=DocumentoLugar::where('fk_lugar',$documentoLugar->fk_lugar)->count();

            if($auxNumDocumentoLugares!=0){
                if($auxNumDocumentoLugares==1){

                    $auxDocumentoLugar= DocumentoLugar::where('fk_lugar', $documentoLugar->fk_lugar)->first();
                    $numObraLugar=ObraLugar::where('fk_lugar',$auxDocumentoLugar->fk_lugar)->count();

                    if($numObraLugar==0){

                        $numLugarEtnia=LugarEtnia::where('fk_lugar', $auxDocumentoLugar->fk_lugar)->count();

                        if($numLugarEtnia==0){

                            DocumentoLugar::where('fk_lugar', $documentoLugar->fk_lugar)->delete();
                            Lugar::where('id_lugar', $documentoLugar->fk_lugar)->delete();
                            return;
                        }
                    }
                    DocumentoLugar::where('fk_lugar', $documentoLugar->fk_lugar)->delete();
                }else{
                    DocumentoLugar::where('fk_lugar', $documentoLugar->fk_lugar)
                    ->where('fk_doc', $idDocumento)
                    ->delete();
                }
            }
        }
    }
    //AQUI SE MUESTRAN LOS lugares QUE SE PUEDEN VINCULAR CON EL DOCUMENTO
    public function ligarDocumento(Request $request,$id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
         $documento =Documento::findOrFail($id);
         $documentoClase =Documento::findOrFail($id);

         if ($request) {
            $query = trim($request->get('searchText'));



            $itemsDocumento =DB::table('cntrl_lugar as ca')
            ->join('lugar as a','a.id_lugar','=','ca.fk_lugar')
            ->join('paises as p','p.id_pais',"=",'a.pais')
            ->join('region as r','r.id_region',"=",'a.region_geografica')
            ->where('fk_doc',$documento->Id_doc)
            ->select('a.id_lugar as id_lugar','a.ubicacion as ubicacion','p.nombre as pais' ,'r.nombrereg as region')
            ->orderBy('ca.fk_lugar', 'desc')
            ->get();

            $idLigados = DB::table('cntrl_lugar as ca')
                ->join('lugar as a', 'a.id_lugar', '=', 'ca.fk_lugar')
                ->join('paises as p', 'p.id_pais', "=", 'a.pais')
                ->join('region as r', 'r.id_region', "=", 'a.region_geografica')
                ->where('fk_doc', $documento->Id_doc)
                ->select('a.id_lugar as id_lugar', 'a.ubicacion as ubicacion', 'p.nombre as pais', 'r.nombrereg as region')
                ->orderBy('ca.fk_lugar', 'desc')
                ->pluck("a.id_lugar")
                ->all();

            $numeroRegistros = DB::table('lugar as l')
                ->join('paises as p', 'p.id_pais', "=", 'l.pais')
                ->join('region as r', 'r.id_region', "=", 'l.region_geografica')
                ->where( DB::raw("CONCAT( l.ubicacion, '', p.nombre )"), 'LIKE', '%' . $query . '%')    
                ->orwhere('l.id_lugar',$query)
                ->orderBy(DB::raw("CONCAT( l.ubicacion, '', p.nombre )"), 'asc')
                ->select('l.id_lugar as id_lugar', 'l.ubicacion as ubicacion', 'p.nombre as pais', 'r.nombrereg as region')
                ->count() ;


            //Desde este momento se empieza a crear la MAGIA
            $items = "";
            $numeroItems = 10;
            $auxIdLigados = $idLigados;

            while (true) {
                $aux = $numeroItems;

                $idsItems = DB::table('lugar as l')
                    ->join('paises as p', 'p.id_pais', "=", 'l.pais')
                    ->join('region as r', 'r.id_region', "=", 'l.region_geografica')
                    ->where( DB::raw("CONCAT( l.ubicacion, '', p.nombre )"), 'LIKE', '%' . $query . '%')
                    ->orwhere('l.id_lugar',$query)
                    ->orderBy(DB::raw("CONCAT( l.ubicacion, '', p.nombre )"), 'asc')
                    ->select('l.id_lugar as id_lugar', 'l.ubicacion as ubicacion', 'p.nombre as pais', 'r.nombrereg as region')
                    ->paginate($numeroItems)
                    ->pluck("id_lugar");


                for ($i = 0; $i < count($idsItems); $i++) {
                    for ($j = 0; $j < count($auxIdLigados); $j++) {
                        if ($idsItems[$i] == $auxIdLigados[$j]) {
                            $numeroItems = $numeroItems + 1;
                            $aux = $aux - 1;
                            unset($auxIdLigados[$j]);
                            $auxIdLigados = array_values($auxIdLigados);
                            break;
                        }
                    }
                }
                if ($aux >= 10 || $numeroRegistros < 10) {
                    $items = DB::table('lugar as l')
                        ->join('paises as p', 'p.id_pais', "=", 'l.pais')
                        ->join('region as r', 'r.id_region', "=", 'l.region_geografica')
                        ->where( DB::raw("CONCAT( l.ubicacion, '', p.nombre )"), 'LIKE', '%' . $query . '%')
                        ->orwhere('l.id_lugar',$query)
                        ->orderBy(DB::raw("CONCAT( l.ubicacion, '', p.nombre )"), 'asc')
                        ->select('l.id_lugar as id_lugar', 'l.ubicacion as ubicacion', 'p.nombre as pais', 'r.nombrereg as region')
                        ->paginate($numeroItems);

                    $itemsFinal = array();
                    foreach ($items as $item) {
                        $bandera = true;
                        foreach ($idLigados as $idLigado) {
                            if ($item->id_lugar == $idLigado) {
                                unset($idLigado);
                                $auxIdLigados = array_values($idLigados);
                                $bandera = false;
                                break;
                            }
                        }
                        if ($bandera) {
                            array_push($itemsFinal, $item);
                        }
                    }
                    break;
                }
            }

        //AQUI SE APLICARÁ EL FILTRO PARA MOSTRAR SOLO
        //AQUELLOS QUE NO ESTÁN VINCULADOS
        //DISCRIMINAR AQUELLOS QUE NO TENGAN EL ID DEL DOCUMENTO ASOCIADO
            /*
           $filtro = DB::table('lugar')
           ->whereNotIn('id_lugar', DB::table('cntrl_lugar')
           ->join('lugar', 'cntrl_lugar.fk_lugar', '=', 'lugar.id_lugar')
           ->where('cntrl_lugar.fk_doc', $documento->Id_doc)
           ->pluck('lugar.id_lugar')->values())
           ->orderBy('id_lugar', 'desc')
           ->paginate(10);
           */
            $page=$request->get('page');

            if($page==null){
                $page=1;
            }
            /*
            $numeroRegistros= DB::table('lugar')
            ->whereNotIn('id_lugar', DB::table('cntrl_lugar')
           ->join('lugar', 'cntrl_lugar.fk_lugar', '=', 'lugar.id_lugar')
           ->where('cntrl_lugar.fk_doc', $documento->Id_doc)
           ->pluck('lugar.id_lugar')
           ->values())
           ->orderBy('id_lugar', 'desc')
           ->count();
            */
            $paises= DB::table('paises')->orderBy('nombre', 'asc')->get();
            $regiones= DB::table('region')->orderBy('nombrereg', 'asc')->get();

            return view('documentoLugar.index',
                [
                    'filtro'=> $itemsFinal,
                    'cntrl_lugar'=>$documento->Id_doc,
                    'lugares' => $itemsFinal,
                    'lugaresdelDocumento' => $itemsDocumento,
                    "searchText" => $query,
                    "totalRegistros"=> $numeroRegistros,
                    "page"=> $page,
                    'documento' =>$documento,
                    'paises'=>$paises,
                    'regiones'=>$regiones
                ]
            );
        }
    }


    //CUANDO UN lugar NO EXISTE EN LA BASE DE DATOS
    //ESTA VISTA MANDA EL FORMULARIO PARA AGREGAR UN NUEVO lugar

    public function nuevoLugarDocumento(Request $request,$id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
         $documento =Documento::findOrFail($id);
            $paises= DB::table('paises')->orderBy('nombre', 'asc')->get();
            $regiones= DB::table('region')->orderBy('nombrereg', 'asc')->get();
        return view('documentoLugar.createLugarDocumento',
        [
            'documento'=> $documento,
            'paises'=>$paises,
            'regiones'=>$regiones
        ]);

    }

    public function show()
    {
    }

    public function edit()
    {
        //
    }



     //ESTE METODO CREA AL lugar Y LO LIGA AUTOMATICAMENTE
    //CON ESE DOCUMENTO
    public function update(LugarFormRequest $request, $id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        $pais =$request->get('pais');
        $regionGeo = $request->input('region_geografica');
        $ubicacion =  $request->get('ubicacion')?$request->get('ubicacion'):'';

        $lugarExistente = Lugar::where('pais',$pais)->where('region_geografica',$regionGeo)->where('ubicacion',$ubicacion)->first(); //Obtengo si existe un lugar con esos datos en la base

          //ID DEL DOCUMENTO
        $documento =Documento::findOrFail($id); //obtengo documento

        if($pais =="0" || $regionGeo =="0"){  // comparación para que verfificar la seleccion de país y región
        Session::flash('messageError','Seleccione un país y una región válidos');
        return Redirect::to('/cntrl_lugar/nuevoLugar/'.$id);
        }
        else if($lugarExistente) { // lugar ya existe en la b.d

            $vinculoDocumentoLugar = DocumentoLugar::where('fk_doc',$documento->Id_doc)->where('fk_lugar',$lugarExistente->id_lugar)->first();

            if($vinculoDocumentoLugar==null){  // si no existe el vinculo ente el documento y el lugar se crea

                // SOLO SE CREA EL VÍNCULO CON LA REFERENCIA DEL LUGAR EXISTENTE

                $respuesta = DocumentoLugar::firstOrCreate([
                    'fk_doc' => $documento->Id_doc,
                    'fk_lugar' => $lugarExistente->id_lugar],
                    [
                    'fk_doc' =>  $documento->Id_doc,
                    'fk_lugar' =>$lugarExistente->id_lugar
                    ]);
    
    
                    $ruta = "/cntrl_lugar/ligar/".$documento->Id_doc;
                    Session::flash('flash_message3', 'El documento se vinculó con el lugar  Id : ('.$lugarExistente->id_lugar.')');
        
                    //activar el log de la base de datos
                        DB::connection()->enableQueryLog();
                        //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                        LogController::agregarLog(
                            1,
                            "cntrl_lugar",
                            "Se agregó el vínculo: ". json_encode($respuesta)
                        );
    
        
                    return Redirect::to($ruta);

            }else{
                Session::flash('messageError', 'El vínculo ya existe.');
                return Redirect::to('/cntrl_lugar/nuevoLugar/'.$id);
               
                

            }

           


        }else{

            // NO EXISTE VÍNCULO NI LUGAR POR ELLO SE CREA LUGAR Y SE REALIZA LA VINCULACIÓN
        //datos del lugar
        $lugar = new Lugar;
        $idLugar = Utilidad::getId("lugar","id_lugar");
        $lugar->id_lugar=$idLugar;
        $lugar->ubicacion =$ubicacion;
        $lugar->pais = $pais;
        $lugar->region_geografica = $regionGeo;


        DB::connection()->enableQueryLog();
        //SE GUARDA EL lugar
        $lugar->save();
        //SE CREA LA LIGADURA
        $respuesta = DocumentoLugar::firstOrCreate([
                    'fk_doc' => $documento->Id_doc,
                    'fk_lugar' => $idLugar],
                    [
                    'fk_doc' =>  $documento->Id_doc,
                    'fk_lugar' => $idLugar
                    ]);
        //REGRESA A LA VISTA ORIGINAL DONDE PODRAS VER LOS lugar LIGADOS
            $ruta = "/cntrl_lugar/ligar/".$documento->Id_doc;
            Session::flash('flash_message3', 'El documento se vinculó con el lugar  Id : ('.$idLugar.')');

            //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    1,
                    "cntrl_lugar",
                    "Se agregó el vínculo: ". json_encode($respuesta)
                );

                LogController::agregarLog(
                    1,
                    "lugar",
                    "Se agregó la lugar: ". json_encode($lugar)
                );

            return Redirect::to($ruta);
        }
    }

    //AQUI SE DESVINCULA EL DOCUMENTO DEL lugar
    public function destroy($id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        //Buscar el vinculo en la tabla cntrl correspondiente
        $aux =DB::table('cntrl_lugar')
        ->where('fk_lugar',$id)
        ->where('fk_doc',$documento->Id_doc)
        ->get();

        //y se elimina
        $aux->delete();

        return Redirect::to("/cntrl_lugar/ligar/".$documento->Id_doc);
    }

     public function destroy2($id,$id2)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        //Buscar el vinculo en la tabla cntrl correspondiente
        //y se elimina
        $numLugar=DocumentoLugar::where('fk_lugar', $id)->count();
        $numLugarObra=ObraLugar::where('fk_lugar',$id)->count();
        $numLugarGrupo=LugarEtnia::where('fk_lugar',$id)->count();
        //activar el log de la base de datos
        DB::connection()->enableQueryLog();
        if($numLugar>1 || $numLugarObra>0 || $numLugarGrupo>0){
            DocumentoLugar::where('fk_lugar', $id)->where('fk_doc', $id2)->delete();
        } else{
            DocumentoLugar::where('fk_lugar', $id)->where('fk_doc', $id2)->delete();
            Lugar::where('id_lugar',$id)->delete();
        }


        Session::flash('flash_message4', ' ¡El lugar se desvinculó existosamente!');


                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    3,
                    "cntrl_lugar",
                    "Se eliminó el vínculo: Lugar(".$id.") del Doc (".$id2.")"
                );
        return Redirect::to("/cntrl_lugar/ligar/".$id2);
    }
}
