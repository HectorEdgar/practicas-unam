<?php

namespace sistema\Http\Controllers;

use Illuminate\Http\Request;
use sistema\DocumentoTema;
use sistema\Documento;
use sistema\Tema;
use sistema\Http\Requests\TemaFormRequest;
use sistema\Http\Requests\DocumentoTemaFormRequest;
use Illuminate\Support\Facades\Redirect;
use DB;
use Session;
use sistema\Utilidad;
use sistema\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DocumentoTemaController extends Controller
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
    public function store(DocumentoTemaFormRequest $request)
    {
                if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
                $ruta = "/cntrl_tema/ligar/".$request->get('fk_doc');
                $respuesta = DocumentoTema::firstOrCreate([
                    'fk_doc' => $request->get('fk_doc'),
                    'fk_tema' => $request->get('fk_tema')],
                    [
                    'fk_doc' => $request->get('fk_doc'),
                    'fk_tema' => $request->get('fk_tema')
                    ]);

                //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    1,
                    "cntrl_tema",
                    "Se agregó el vínculo: ". json_encode($respuesta)
                );
                Session::flash('flash_message', ' ¡Vinculación exitosa!');
               // Session::flash('flash_message2', 'Ese vinculo ya existe!'.$respuesta);

                return Redirect::to($ruta);
    }

    //AQUI SE MUESTRAN LOS temas QUE SE PUEDEN VINCULAR CON EL DOCUMENTO
    public function ligarDocumento(Request $request,$id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
         $documento =Documento::findOrFail($id);
         $documentoClase =Documento::findOrFail($id);

         if ($request) {
            $query = trim($request->get('searchText'));



            $itemsDocumento =DB::table('cntrl_tema as ca')
            ->join('temas as a','a.id_tema','=','ca.fk_tema')
            ->where('fk_doc',$documento->Id_doc)
            ->orderBy('a.id_tema', 'desc')
            ->get();


            $idLigados = DB::table('cntrl_tema as ca')
                ->join('temas as a', 'a.id_tema', '=', 'ca.fk_tema')
                ->where('fk_doc', $documento->Id_doc)
                ->orderBy('id_tema', 'desc')
                ->pluck("a.id_tema")
                ->all();

            $numeroRegistros = DB::table('temas')
                ->where('descripcion', 'LIKE', '%' . $query . '%')
                ->orwhere('id_tema', 'LIKE', '%' . $query . '%')
                ->orderBy('id_tema', 'desc')
                ->count() - count($idLigados);


            //Desde este momento se empieza a crear la MAGIA
            $items = "";
            $numeroItems = 10;
            $auxIdLigados = $idLigados;
            while (true) {
                $aux = $numeroItems;

                $idsItems = DB::table('temas')
                    ->where('descripcion', 'LIKE', '%' . $query . '%')
                    ->orwhere('id_tema', 'LIKE', '%' . $query . '%')
                    ->orderBy('id_tema', 'desc')
                    ->paginate($numeroItems)
                    ->pluck("id_tema");


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
                    $items = DB::table('temas')
                        ->where('descripcion', 'LIKE', '%' . $query . '%')
                        ->orwhere('id_tema', 'LIKE', '%' . $query . '%')
                        ->orderBy('id_tema', 'desc')
                        ->paginate($numeroItems);

                    $itemsFinal = array();
                    foreach ($items as $item) {
                        $bandera = true;
                        foreach ($idLigados as $idLigado) {
                            if ($item->id_tema == $idLigado) {
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
           $filtro = DB::table('temas')
           ->whereNotIn('id_tema', DB::table('cntrl_tema')
           ->join('temas', 'cntrl_tema.fk_tema', '=', 'temas.id_tema')
           ->where('cntrl_tema.fk_doc', $documento->Id_doc)
           ->pluck('temas.id_tema')->values())->orderBy('id_tema', 'desc')
           ->paginate(10);
        */
            $page=$request->get('page');

            if($page==null){
                $page=1;
            }
        /*
            $numeroRegistros= DB::table('temas')
            ->whereNotIn('id_tema', DB::table('cntrl_tema')
           ->join('temas', 'cntrl_tema.fk_tema', '=', 'temas.id_tema')
           ->where('cntrl_tema.fk_doc', $documento->Id_doc)
           ->pluck('temas.id_tema')
           ->values())
           ->orderBy('id_tema', 'desc')
           ->count();
            */

            return view('documentoTema.index',
                [
                    'filtro'=> $itemsFinal,
                    'cntrl_tema'=>$documento->Id_doc,
                    'temas' => $itemsFinal,
                    'temasdelDocumento' => $itemsDocumento,
                    "searchText" => $query,
                    "totalRegistros"=> $numeroRegistros,
                    "page"=> $page,
                    'documento' =>$documento
                ]
            );
        }
    }


    //CUANDO UN tema NO EXISTE EN LA BASE DE DATOS
    //ESTA VISTA MANDA EL FORMULARIO PARA AGREGAR UN NUEVO tema

    public function nuevoTemaDocumento(Request $request,$id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
         $documento =Documento::findOrFail($id);

        return view('documentoTema.createTemaDocumento',
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



     //ESTE METODO CREA AL tema Y LO LIGA AUTOMATICAMENTE
    //CON ESE DOCUMENTO
    public function update(TemaFormRequest $request, $id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}

        //ID DEL DOCUMENTO
        $documento =Documento::findOrFail($id);
        //datos del tema
        $tema = new Tema;
        $idTema = Utilidad::getId("temas","id_tema");
        $tema->id_tema=$idTema;
        $tema->descripcion = $request->get('descripcion');

        DB::connection()->enableQueryLog();
        //SE GUARDA EL tema
        $tema->save();
        //SE CREA LA LIGADURA
        $respuesta = DocumentoTema::firstOrCreate([
                    'fk_doc' => $documento->Id_doc,
                    'fk_tema' => $idTema],
                    [
                    'fk_doc' =>  $documento->Id_doc,
                    'fk_tema' => $idTema
                    ]);
        //REGRESA A LA VISTA ORIGINAL DONDE PODRAS VER LOS tema LIGADOS
            $ruta = "/cntrl_tema/ligar/".$documento->Id_doc.$respuesta;
            Session::flash('flash_message3', 'El documento se vinculó con el tema Id : ('.$idTema.')');

            //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    1,
                    "cntrl_tema",
                    "Se agregó el vínculo: ". json_encode($respuesta)
                );

                LogController::agregarLog(
                    1,
                    "tema",
                    "Se agregó la tema: ". json_encode($tema)
                );

            return Redirect::to($ruta);
    }
    public static function eliminarDocumentoCascada($idDocumento){
        $aux = DB::table('cntrl_tema')
            ->where('fk_doc', $idDocumento)
            ->delete();
    }
    //AQUI SE DESVINCULA EL DOCUMENTO DEL tema
    public function destroy($id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        //Buscar el vinculo en la tabla cntrl correspondiente
        $aux =DB::table('cntrl_tema')
        ->where('fk_tema',$id)
        ->where('fk_doc',$documento->Id_doc)
        ->get();

        //y se elimina
        $aux->delete();

        return Redirect::to("/cntrl_tema/ligar/".$documento->Id_doc);
    }

     public function destroy2($id,$id2)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        //Buscar el vinculo en la tabla cntrl correspondiente
        //y se elimina
        $borrar =DocumentoTema::where('fk_tema', $id)->where('fk_doc', $id2)->delete();
        Session::flash('flash_message4', ' ¡El tema se desvinculó Existosamente!');
        //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    3,
                    "cntrl_tema",
                    "Se eliminó el vínculo: Tema(".$id.") del Doc (".$id2.")"
                );
        return Redirect::to("/cntrl_tema/ligar/".$id2);
    }
}
