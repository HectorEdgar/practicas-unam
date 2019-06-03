<?php

namespace sistema\Http\Controllers;

use Illuminate\Http\Request;
use sistema\DocumentoObra;
use sistema\Documento;
use sistema\Obra;
use sistema\Http\Requests\ObraFormRequest;
use sistema\Http\Requests\DocumentoObraFormRequest;
use Illuminate\Support\Facades\Redirect;
use DB;
use Session;
use sistema\Utilidad;
use sistema\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DocumentoObraController extends Controller
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
    public function store(DocumentoObraFormRequest $request)
    {
                if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
                $ruta = "/cntrl_obra/ligar/".$request->get('fk_doc');
                

                if($request->get('editar')=='true'){
        

                   $respuesta =  DocumentoObra::where('fk_doc',$request->get('fk_doc'))
                   ->where('fk_obra',$request->get('fk_obra'))
                    ->update(['status'=> $request->get('status')]);

                    
                    //activar el log de la base de datos
                    DB::connection()->enableQueryLog();
                    //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                    LogController::agregarLog(
                        2,
                        "obra_doc",
                        "Se actualizó  el vínculo: de la obra :".$request->get('fk_obra'). "y el documento".$request->get('fk_doc')
                    );
                    Session::flash('flash_message', '¡Vinculación editada!');
              
                    




                }else{

                    $respuesta = DocumentoObra::firstOrCreate([
                        'fk_doc' => $request->get('fk_doc'),
                        'fk_obra' =>  $request->get('fk_obra'),
                        'status'=>  $request->get('status'),
                        'revisado'=>0,
                        'investigador'=>Auth::user()->name
                        ],
                        [
                        'fk_doc' => $request->get('fk_doc'),
                        'fk_obra' =>  $request->get('fk_obra'),
                        'status'=>  $request->get('status'),
                        'revisado'=>0,
                        'investigador'=>Auth::user()->name
                        ]);
    
    
    
                    //activar el log de la base de datos
                    DB::connection()->enableQueryLog();
                    //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                    LogController::agregarLog(
                        1,
                        "obra_doc",
                        "Se agregó el vínculo: ". json_encode($respuesta)
                    );
                    Session::flash('flash_message', '¡Vinculación exitosa!');
                   // Session::flash('flash_message2', 'Ese vinculo ya existe!'.$respuesta);


                }

                return Redirect::to($ruta);
    }
    public static function eliminarDocumentoCascada($idDocumento)
    {
        $items= DocumentoObra::where('fk_doc', $idDocumento)->get();
        foreach ($items as $item) {
            $numItems = DocumentoObra::where('fk_obra', $item->fk_obra)
                ->count();

            //Log::warning('numItemsDocumentoLigado: ' . $numItems);

            if ($numItems == 1) {
                $itemControl = DocumentoObra::where('fk_doc', $item->fk_doc)
                    ->where('fk_obra', $item->fk_obra)
                    ->first();
            //Log::warning('itemControl: ' . $itemControl);
                $itemIdControl = $itemControl->fk_obra;
                ObraEjeController::eliminarObraCascada($itemIdControl);
                ObraLugarController::eliminarObraCascada($itemIdControl);
                ObraInstitucionController::eliminarObraCascada($itemIdControl);
                ObraPersonaController::eliminarObraCascada($itemIdControl);
                ObraTemaController::eliminarObraCascada($itemIdControl);
                ObraProyectoController::eliminarObraCascada($itemIdControl);
                ObraObraController::eliminarObraCascada($itemIdControl);
                $itemObjeto = Obra::where("id_obra", $itemIdControl)
                    ->first();
                Log::warning('itemControl: ' . $itemControl);
                Log::warning('itemObjeto: ' . $itemObjeto);
                $itemControl = DocumentoObra::where('fk_obra', $item->fk_obra)
                ->where('fk_doc', $item->fk_doc)
                ->delete();
                $itemObjeto->delete();
            }

        }
        DocumentoObra::where('fk_doc', $idDocumento)->delete();
    }
    //AQUI SE MUESTRAN LOS obraes QUE SE PUEDEN VINCULAR CON EL DOCUMENTO
    public function ligarDocumento(Request $request,$id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
         $documento =Documento::findOrFail($id);
         $documentoClase =Documento::findOrFail($id);

         if ($request) {
            $query = trim($request->get('searchText'));


            $itemsDocumento =DB::table('obra_doc as ca')
            ->join('obras as a','a.id_obra','=','ca.fk_obra')
            ->join('documento as p','p.Id_doc',"=",'ca.fk_doc')
            ->join('status as s','s.id_status',"=",'ca.status')
            ->where('fk_doc',$documento->Id_doc)
            ->select('a.id_obra as id_obra','a.extra as extra','a.revisado as revisado','a.nombre as nombre','p.Id_doc as Id_doc','s.tip_est as status')
            ->orderBy('id_obra', 'desc')
            ->get();

            $idLigados = DB::table('obra_doc as ca')
                ->join('obras as a', 'a.id_obra', '=', 'ca.fk_obra')
                ->join('documento as p', 'p.Id_doc', "=", 'ca.fk_doc')
                ->join('status as s', 's.id_status', "=", 'ca.status')
                ->where('fk_doc', $documento->Id_doc)
                ->select('a.id_obra as id_obra', 'a.extra as extra', 'a.revisado as revisado', 'a.nombre as nombre', 'p.Id_doc as Id_doc', 's.tip_est as status')
                ->orderBy('ca.fk_obra', 'desc')
                ->pluck("id_obra")
                ->all();

            $numeroRegistros = DB::table('obras')
                ->where('nombre', 'LIKE', '%' . $query . '%')
                ->orwhere('extra', 'LIKE', '%' . $query . '%')
                ->orwhere('revisado', 'LIKE', '%' . $query . '%')
                ->orwhere('id_obra', 'LIKE', '%' . $query . '%')
                ->orderBy('nombre', 'asc')
                ->count() - count($idLigados);


            //Desde este momento se empieza a crear la MAGIA
            $items = "";
            $numeroItems = 10;
            $auxIdLigados = $idLigados;
            while (true) {
                $aux = $numeroItems;

                $idsItems = DB::table('obras')
                    ->where('nombre', 'LIKE', '%' . $query . '%')
                    ->orwhere('extra', 'LIKE', '%' . $query . '%')
                    ->orwhere('revisado', 'LIKE', '%' . $query . '%')
                    ->orwhere('id_obra', 'LIKE', '%' . $query . '%')
                    ->orderBy('nombre', 'asc')
                    ->paginate($numeroItems)
                    ->pluck("id_obra");


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
                    $items = DB::table('obras')
                        ->where('nombre', 'LIKE', '%' . $query . '%')
                        ->orwhere('extra', 'LIKE', '%' . $query . '%')
                        ->orwhere('revisado', 'LIKE', '%' . $query . '%')
                        ->orwhere('id_obra', 'LIKE', '%' . $query . '%')
                        ->orderBy('nombre', 'asc')
                        ->paginate($numeroItems);

                    $itemsFinal = array();
                    foreach ($items as $item) {
                        $bandera = true;
                        foreach ($idLigados as $idLigado) {
                            if ($item->id_obra == $idLigado) {
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

           /*$filtro = DB::table('obra')
           ->whereNotIn('id_obra', DB::table('cntrl_obra')
           ->join('obra', 'cntrl_obra.fk_obra', '=', 'obra.id_obra')
           ->where('cntrl_obra.fk_doc', $documento->Id_doc)
           ->pluck('obra.id_obra')->values())
           ->orderBy('id_obra', 'desc')

           ->paginate(10);
            */
            $page=$request->get('page');

            if($page==null){
                $page=1;
            }
            /*
            $numeroRegistros= DB::table('obras')
             ->where('nombre', 'LIKE', '%' . $query . '%')
                ->orwhere('extra', 'LIKE', '%' . $query . '%')
                ->orwhere('revisado', 'LIKE', '%' . $query . '%')
                ->orwhere('id_obra', 'LIKE', '%' . $query . '%')
                ->orderBy('id_obra', 'desc')
                ->count();
            */
            $status= DB::table('status')->orderBy('id_status', 'asc')->get();

            return view('documentoObra.index',
                [
                    'Id_doc'=>$documento->Id_doc,
                    'obras' => $itemsFinal,
                    'obrasdelDocumento' => $itemsDocumento,
                    "searchText" => $query,
                    "totalRegistros"=> $numeroRegistros,
                    "page"=> $page,
                    'documento' =>$documento,
                    'status'=>$status
                ]
            );
        }
    }


    //CUANDO UN obra NO EXISTE EN LA BASE DE DATOS
    //ESTA VISTA MANDA EL FORMULARIO PARA AGREGAR UN NUEVO obra

    public function nuevoObraDocumento(Request $request,$id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
         $documento =Documento::findOrFail($id);
         $status= DB::table('status')->orderBy('id_status', 'asc')->get();

        return view('documentoObra.createObraDocumento',
        [
            'documento'=> $documento,
            'status'=>$status
        ]);

    }

    public function show()
    {
        //
    }

    public function edit()
    {
        //
    }



     //ESTE METODO CREA AL obra Y LO LIGA AUTOMATICAMENTE
    //CON ESE DOCUMENTO
    public function update(ObraFormRequest $request, $id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}

        //ID DEL DOCUMENTO
        $documento =Documento::findOrFail($id);
        //datos del obra
        $obra = new Obra;
        $idObra=Utilidad::getId("obras","id_obra");
        $obra->id_obra=$idObra;
        $obra->nombre = $request->get('nombre');
        $tipo= $request->get('tipo');
        if($tipo=='obra'){
            $obra->extra=1;
        }
        else if($tipo =='complejo'){
            $obra->extra=2;
        }
        $status = $request->get('status');


        DB::connection()->enableQueryLog();
        //SE GUARDA EL obra
        $obra->save();
        //SE CREA LA LIGADURA
        $respuesta = DocumentoObra::firstOrCreate([
                    'fk_doc' => $documento->Id_doc,
                    'fk_obra' => $idObra,
                    'status'=> $status,
                    'revisado'=>0,
                    'investigador'=>Auth::user()->name
                    ],
                    [
                    'fk_doc' =>  $documento->Id_doc,
                    'fk_obra' => $idObra,
                    'status'=> $status,
                    'revisado'=>0,
                    'investigador'=>Auth::user()->name
                    ]);

        //REGRESA A LA VISTA ORIGINAL DONDE PODRAS VER LOS obra LIGADOS
            $ruta = "/cntrl_obra/ligar/".$documento->Id_doc;
            Session::flash('flash_message3', 'El documento se vinculó con la Obra  Id : ('.$idObra.')');

            //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    1,
                    "obra_doc",
                    "Se agregó el vínculo: ". json_encode($respuesta)
                );

                LogController::agregarLog(
                    1,
                    "obra",
                    "Se agregó la obra: ". json_encode($obra)
                );

            return Redirect::to($ruta);

    }

    //AQUI SE DESVINCULA EL DOCUMENTO DEL obra
    public function destroy($id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        //Buscar el vinculo en la tabla cntrl correspondiente
        $aux =DB::table('cntrl_obra')
        ->where('fk_obra',$id)
        ->where('fk_doc',$documento->Id_doc)
        ->get();

        //y se elimina
        $aux->delete();

        return Redirect::to("/cntrl_obra/ligar/".$documento->Id_doc);
    }

     public function destroy2($id,$id2)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        //Buscar el vinculo en la tabla cntrl correspondiente
        //y se elimina

        $numObras=DocumentoObra::where('fk_obra', $id)->count();
                //activar el log de la base de datos
                DB::connection()->enableQueryLog();
        if($numObras>1){
            DocumentoObra::where('fk_obra', $id)->where('fk_doc', $id2)->delete();
        }else{
            DocumentoObra::where('fk_obra', $id)->where('fk_doc', $id2)->delete();
            ObraEjeController::eliminarObraCascada($id);      
            ObraInstitucionController::eliminarObraCascada($id);
            ObraPersonaController::eliminarObraCascada($id);
            ObraTemaController::eliminarObraCascada($id);
            ObraProyectoController::eliminarObraCascada($id);
            ObraObraController::eliminarObraCascada($id);
            ObraLugarController::eliminarObraCascada($id);
            Obra::where('id_obra',$id)->delete();
        }

        Session::flash('flash_message4', ' ¡La obra se desvinculó existosamente!');

                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    3,
                    "cntrl_obra",
                    "Se eliminó el vinculo: Obra(".$id.") del Doc (".$id2.")"
                );
        return Redirect::to("/cntrl_obra/ligar/".$id2);
    }


    public function validarRevision($idDocumento,$idObra)
    {

        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}


       $registro = DocumentoObra::where('fk_doc',$idDocumento)
                    ->where('fk_obra',$idObra)
                    ->first();

        $status = $registro->status;

        $aux = DocumentoObra::where('fk_doc',$idDocumento)
        ->where('fk_obra',$idObra)
        ->delete();

        $valor= null;

        if($registro->revisado ==1){
            $valor= DocumentoObra::firstOrCreate([
                'fk_doc' => $idDocumento,
                'fk_obra' => $idObra,
                'status'=> $status,
                'revisado'=>0,
                'investigador'=>Auth::user()->name
            ],
            [
                'fk_doc' => $idDocumento,
                'fk_obra' => $idObra,
                'status'=> $status,
                'revisado'=>0,
                'investigador'=>Auth::user()->name
            ]
        );
        }else{

            $valor= DocumentoObra::firstOrCreate([
                'fk_doc' => $idDocumento,
                'fk_obra' => $idObra,
                'status'=> $status,
                'revisado'=>1,
                'investigador'=>Auth::user()->name
            ],
            [
                'fk_doc' => $idDocumento,
                'fk_obra' => $idObra,
                'status'=> $status,
                'revisado'=>1,
                'investigador'=>Auth::user()->name
            ]
        );


        }


            DB::connection()->enableQueryLog();

            //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
        LogController::agregarLog(
           2,
           "cntrl_obra",
           "Se actualizó el estado de revisión de la obra y el documento: ". json_encode($valor)
        );


        return back();


    }
}
