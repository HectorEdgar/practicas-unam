<?php

namespace sistema\Http\Controllers;

use Illuminate\Http\Request;
use sistema\DocumentoSubtema;
use sistema\Documento;
use sistema\Subtema;
use sistema\Http\Requests\SubtemaFormRequest;
use sistema\Http\Requests\DocumentoSubtemaFormRequest;
use Illuminate\Support\Facades\Redirect;
use DB;
use Session;
use sistema\Utilidad;
use sistema\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DocumentoSubtemaController extends Controller
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
    }*/
    public function store(DocumentoSubtemaFormRequest $request)
    {
           if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
                $ruta = "/cntrl_subtema/ligar/".$request->get('fk_doc');
                $respuesta = DocumentoSubtema::firstOrCreate([
                    'fk_doc' => $request->get('fk_doc'),
                    'fk_sub' => $request->get('fk_sub')],
                    [
                    'fk_doc' => $request->get('fk_doc'),
                    'fk_sub' => $request->get('fk_sub')
                    ]);

                $subtema =Subtema::findOrFail($request->get('fk_sub'));

                //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    1,
                    "cntrl_sub",
                    "Se agregó el vínculo: ". json_encode($respuesta)
                );
                Session::flash('flash_message', 'Vinculación exitosa con el subtema: ('.$subtema->subtema.')');
                return Redirect::to($ruta);
    }
    public static function eliminarDocumentoCascada($idDocumento)
    {
        $documentosSubtemas = DocumentoSubtema::where('fk_doc', $idDocumento)
            ->get();
        foreach ($documentosSubtemas as $documentoSubtema) {
            $aux=DocumentoSubtema::where('fk_sub', $documentoSubtema->fk_sub)
                ->count();
            if($aux!=0){
                if($aux==1){
                    DocumentoSubtema::where('fk_sub', $documentoSubtema->fk_sub)
                        ->where('fk_doc', $idDocumento)
                        ->delete();
                    Subtema::where("id_sub", $documentoSubtema->fk_sub)->delete();
                }else{
                    if($aux>1){
                        DocumentoSubtema::where('fk_sub', $documentoSubtema->fk_sub)
                            ->where('fk_doc', $idDocumento)
                            ->delete();
                    }
                }
            }

        }


    }
    //AQUI SE MUESTRAN LOS subtemas QUE SE PUEDEN VINCULAR CON EL DOCUMENTO
    public function ligarDocumento(Request $request,$id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
         $documento =Documento::findOrFail($id);
         $documentoClase =Documento::findOrFail($id);

         if ($request) {
            $query = trim($request->get('searchText'));



            $itemsDocumento =DB::table('cntrl_sub as ca')
            ->join('subtema as a','a.id_sub','=','ca.fk_sub')
            ->where('fk_doc',$documento->Id_doc)
            ->orderBy('a.id_sub', 'desc')
            ->get();

            $idLigados = DB::table('cntrl_sub as ca')
                ->join('subtema as a', 'a.id_sub', '=', 'ca.fk_sub')
                ->where('fk_doc', $documento->Id_doc)
                ->orderBy('a.id_sub', 'desc')
                ->pluck("a.id_sub")
                ->all();

            $numeroRegistros = DB::table('subtema')
                ->where('subtema', 'LIKE', '%' . $query . '%')
                ->orwhere('id_sub', 'LIKE', '%' . $query . '%')
                ->orderBy('id_sub', 'desc')
                ->count() - count($idLigados);


            //Desde este momento se empieza a crear la MAGIA
            $items = "";
            $numeroItems = 10;
            $auxIdLigados = $idLigados;
            while (true) {
                $aux = $numeroItems;

                $idsItems = DB::table('subtema')
                    ->where('subtema', 'LIKE', '%' . $query . '%')
                    ->orwhere('id_sub', 'LIKE', '%' . $query . '%')
                    ->orderBy('id_sub', 'desc')
                    ->paginate($numeroItems)
                    ->pluck("id_sub");


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
                    $items = DB::table('subtema')
                        ->where('subtema', 'LIKE', '%' . $query . '%')
                        ->orwhere('id_sub', 'LIKE', '%' . $query . '%')
                        ->orderBy('id_sub', 'desc')
                        ->paginate($numeroItems);

                    $itemsFinal = array();
                    foreach ($items as $item) {
                        $bandera = true;
                        foreach ($idLigados as $idLigado) {
                            if ($item->id_sub == $idLigado) {
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
           $filtro = DB::table('subtema')
           ->where('subtema', 'LIKE', '%' . $query . '%')
           ->orwhere('id_sub', 'LIKE', '%' . $query . '%')

           ->whereNotIn('id_sub', DB::table('cntrl_sub')
           ->join('subtema', 'cntrl_sub.fk_sub', '=', 'subtema.id_sub')
           ->where('cntrl_sub.fk_doc', $documento->Id_doc)
           ->pluck('subtema.id_sub')->values())->orderBy('id_sub', 'desc')
           ->paginate(10);
            */
            $page=$request->get('page');

            if($page==null){
                $page=1;
            }
            /*
            $numeroRegistros= DB::table('subtema')
                ->where('subtema', 'LIKE', '%' . $query . '%')
                ->orwhere('id_sub', 'LIKE', '%' . $query . '%')
                ->orderBy('id_sub', 'desc')->count();
            */
            return view('documentoSubtema.index',
                [
                    'filtro'=> $itemsFinal,
                    'cntrl_subtema'=>$documento->Id_doc,
                    'subtemas' => $itemsFinal,
                    'subtemasdelDocumento' => $itemsDocumento,
                    "searchText" => $query,
                    "totalRegistros"=> $numeroRegistros,
                    "page"=> $page,
                    'documento' =>$documento
                ]
            );
        }
    }


    //CUANDO UN subtema NO EXISTE EN LA BASE DE DATOS
    //ESTA VISTA MANDA EL FORMULARIO PARA AGREGAR UN NUEVO subtema

    public function nuevoSubtemaDocumento(Request $request,$id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
         $documento =Documento::findOrFail($id);

        return view('documentoSubtema.createSubtemaDocumento',
        [
            'documento'=> $documento,
        ]);

    }

    public function show()
    {
    }

    public function edit()
    {
        //
    }



     //ESTE METODO CREA AL subtema Y LO LIGA AUTOMATICAMENTE
    //CON ESE DOCUMENTO
    public function update(SubtemaFormRequest $request, $id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        //ID DEL DOCUMENTO
        $documento =Documento::findOrFail($id);
        $auxSubtema = $request->get('subtema')?$request->get('subtema'):'';

        $subtemaExistente = Subtema::where('subtema',$auxSubtema)->first(); 



        if($subtemaExistente){

            $vinculoSubtema = DocumentoSubtema::where('fk_doc',$documento->Id_doc)->where('fk_sub',$subtemaExistente->id_sub)->first();


            if($vinculoSubtema==null){

                $respuesta = DocumentoSubtema::firstOrCreate([
                    'fk_doc' => $documento->Id_doc,
                    'fk_sub' => $subtemaExistente->id_sub],
                    [
                    'fk_doc' =>  $documento->Id_doc,
                    'fk_sub' => $subtemaExistente->id_sub
                    ]);
      

        //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    1,
                    "cntrl_sub",
                    "Se agregó el vínculo: ". json_encode($respuesta)
                );

             

        //REGRESA A LA VISTA ORIGINAL DONDE PODRAS VER LOS subtema LIGADOS
            $ruta = "/cntrl_subtema/ligar/".$documento->Id_doc;
            Session::flash('flash_message3', 'El documento se vinculó al Subtema : ('.$subtemaExistente->id_sub.') exitosamente');

            return Redirect::to($ruta);

            }else{

                Session::flash('flash_message2', 'El vínculo ya existe.');
                return Redirect::to( "/cntrl_subtema/ligar/".$documento->Id_doc);

            }


        }else{
        //datos del subtema
        $subtema = new Subtema;
        $idSubtema = Utilidad::getId("subtema","id_sub");
        $subtema->id_sub=$idSubtema;
        $subtema->subtema = $auxSubtema;
        DB::connection()->enableQueryLog();
        //SE GUARDA EL subtema
        $subtema->save();
        //SE CREA LA LIGADURA
        $respuesta = DocumentoSubtema::firstOrCreate([
                    'fk_doc' => $documento->Id_doc,
                    'fk_sub' => $idSubtema],
                    [
                    'fk_doc' =>  $documento->Id_doc,
                    'fk_sub' => $idSubtema
                    ]);
        $subtema =Subtema::findOrFail($idSubtema);

        //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    1,
                    "cntrl_sub",
                    "Se agregó el vínculo: ". json_encode($respuesta)
                );

                LogController::agregarLog(
                    1,
                    "subtemas",
                    "Se agregó el subtema: ". json_encode($subtema)
                );

        //REGRESA A LA VISTA ORIGINAL DONDE PODRAS VER LOS subtema LIGADOS
            $ruta = "/cntrl_subtema/ligar/".$documento->Id_doc.$respuesta;
            Session::flash('flash_message3', 'El documento se vinculó al Subtema : ('.$subtema->subtema.') exitosamente');

            return Redirect::to($ruta);
            }
    }

    //AQUI SE DESVINCULA EL DOCUMENTO DEL subtema
    public function destroy($id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        //Buscar el vinculo en la tabla cntrl correspondiente
        $aux =DB::table('cntrl_sub')
        ->where('fk_sub',$id)
        ->where('fk_doc',$documento->Id_doc)
        ->get();

        //y se elimina
        $aux->delete();

        return Redirect::to("/cntrl_subtema/ligar/".$documento->Id_doc);
    }

    public function destroy2($id, $id2)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin', 'revisor'])) {
            return Redirect::to('/');
        }
        //Buscar el vinculo en la tabla cntrl correspondiente
        //y se elimina

        $numSubtema = DocumentoSubtema::where('fk_sub', $id)->count();
        //activar el log de la base de datos
        DB::connection()->enableQueryLog();
        if ($numSubtema > 1) {
            DocumentoSubtema::where('fk_sub', $id)->where('fk_doc', $id2)->delete();
        } else {
            DocumentoSubtema::where('fk_sub', $id)->where('fk_doc', $id2)->delete();
            Subtema::where('id_sub', $id)->delete();
        }

        Session::flash('flash_message', ' ¡El Subtema se desvinculó existosamente!');


                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
        LogController::agregarLog(
            3,
            "cntrl_sub",
            "Se eliminó el vínculo: Subtema(" . $id . ") del Doc (" . $id2 . ")"
        );

        return Redirect::to("/cntrl_subtema/ligar/" . $id2);
    }
}
