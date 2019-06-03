<?php

namespace sistema\Http\Controllers;

use sistema\Documento;
use sistema\Editor;
use sistema\Http\Requests\EditorFormRequest;
use sistema\Http\Requests\DocumentoEditorFormRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use DB;
use sistema\DocumentoEditor;
use Session;
use sistema\Utilidad;
use sistema\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class DocumentoEditorController extends Controller
{
    public function index(Request $request)
    {
    }
    public function create()
    {

    }
    private function validarRoles(){
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
    }

    public static function eliminarDocumentoCascada($idDocumento) {
        //obtener todos los vinculos del documento con editor
        //iterar los vinculos
        //comprobar que cada editor no tenga vinculo con otros documentos
        //si tienen vinculos solo se elimina el vinculo con ese doc si no se elimina tambien el editor
        $items=DocumentoEditor::where('fk_doc', $idDocumento)->get();
        foreach ($items as $item) {
            $numItems = DocumentoEditor::where('fk_editor', $item->fk_editor)
                ->count();

            //Log::warning('numItemsDocumentoLigado: ' . $numItems);

            if ($numItems == 1) {
                $itemControl = DocumentoEditor::where('fk_editor', $item->fk_editor)
                    ->where('fk_doc', $item->fk_doc)
                    ->first();
            //Log::warning('itemControl: ' . $itemControl);
                $itemIdControl = $itemControl->fk_editor;
                $itemObjeto = Editor::where("id_editor", $itemIdControl)
                    ->first();
                Log::warning('itemControl: ' . $itemControl);
                Log::warning('itemObjeto: ' . $itemObjeto);
                $itemControl = DocumentoEditor::where('fk_editor', $item->fk_editor)
                    ->where('fk_doc', $item->fk_doc)
                    ->delete();
                $itemObjeto->delete();
            }
        }
        $itemControl = DocumentoEditor::where('fk_doc', $idDocumento)->delete();

    }
    public function store(DocumentoEditorFormRequest $request)
    {
           if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
            //EL ORDEN ES EL MAXIMO ORDEN +1
            $orden=DB::table('cntrl_editor')->max('orden');
            //cuando se inserta, va regresar a esta misma ruta
                $ruta = "/cntrl_editor/ligar/".$request->get('fk_doc');
                $respuesta = DocumentoEditor::firstOrCreate([
                    'fk_doc' => $request->get('fk_doc'),
                    'fk_editor' => $request->get('fk_editor')],
                    [
                     'orden'=>$orden+1,
                    'fk_doc' => $request->get('fk_doc'),
                    'fk_editor' => $request->get('fk_editor')
                    ]);

                $ordenReal = $orden;
                $valor=$respuesta->orden-$ordenReal;
                if($valor>0) {
                    Session::flash('flash_message', '¡Vinculación exitosa!');
                }
                else {
                    Session::flash('flash_message2', '¡Ese vínculo ya existe!');
                }

                LogController::agregarLog(
                    1,
                    "cntrl_editor",
                    "Se agregó el editor: ". json_encode($respuesta)
                );

                return Redirect::to($ruta);
    }

    //AQUI SE MUESTRAN LOS Editores QUE SE PUEDEN VINCULAR CON EL DOCUMENTO
    public function ligarDocumento(Request $request,$id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
         $documento =Documento::findOrFail($id);
         if ($request) {
            $query=trim($request->get('searchText'));
			//SE REALIZA LA BUSQUEDA


            $itemsDocumento =DB::table('cntrl_editor as ca')
            ->join('editor as a','a.id_editor','=','ca.fk_editor')
            ->where('fk_doc',$documento->Id_doc)
            ->orderBy('a.id_editor', 'desc')
            ->get();

            $idLigados = DB::table('cntrl_editor as ca')
                ->join('editor as a', 'a.id_editor', '=', 'ca.fk_editor')
                ->where('fk_doc', $documento->Id_doc)
                ->orderBy('a.id_editor', 'desc')
                ->pluck("a.id_editor")
                ->all();

            $numeroRegistros = DB::table('editor')
                ->where('editor', 'LIKE', '%' . $query . '%')
                ->orwhere('id_editor', 'LIKE', '%' . $query . '%')
                ->orderBy('id_editor', 'desc')
                ->count() - count($idLigados);


            //Desde este momento se empieza a crear la MAGIA
            $items = "";
            $numeroItems = 10;
            $auxIdLigados = $idLigados;
            while (true) {
                $aux = $numeroItems;

                $idsItems = DB::table('editor')
                    ->where('editor', 'LIKE', '%' . $query . '%')
                    ->orwhere('id_editor', 'LIKE', '%' . $query . '%')
                    ->orderBy('id_editor', 'desc')
                    ->paginate($numeroItems)
                    ->pluck("id_editor");


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
                    $items = DB::table('editor')
                        ->where('editor', 'LIKE', '%' . $query . '%')
                        ->orwhere('id_editor', 'LIKE', '%' . $query . '%')
                        ->orderBy('id_editor', 'desc')
                        ->paginate($numeroItems);

                    $itemsFinal = array();
                    foreach ($items as $item) {
                        $bandera = true;
                        foreach ($idLigados as $idLigado) {
                            if ($item->id_editor == $idLigado) {
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

        /*SELECT * from editor where editor.Id_editor not in
        (select ca.fk_editor from cntrl_editor ca join editor a
        on a.Id_editor = ca.fk_editor where ca.fk_doc=7993)*/
            /*
           $filtro = DB::table('editor')
           ->where('editor','LIKE','%'.$query.'%')
		   ->orwhere('id_editor','LIKE','%'.$query.'%')
           ->whereNotIn('id_editor', DB::table('cntrl_editor')
           ->join('editor', 'cntrl_editor.fk_editor', '=', 'editor.id_editor')
           ->where('cntrl_editor.fk_doc', $documento->Id_doc)
           ->pluck('editor.id_editor')->values())->orderBy('id_editor', 'desc')
           ->paginate(10);
            */
            $page=$request->get('page');

            if($page==null){
                $page=1;
            }
            /*
            $numeroRegistros= DB::table('editor')
                ->where('editor', 'LIKE', '%' . $query . '%')
                ->orwhere('id_editor', 'LIKE', '%' . $query . '%')
                ->orderBy('id_editor', 'desc')->count();
            */
            return view('documentoEditor.index',
                [
                    'filtro'=> $itemsFinal,
                    'cntrl_editor'=>$documento->Id_doc,
                    'editores' => $itemsFinal,
                    'editoresdelDocumento' => $itemsDocumento,
                    "searchText" => $query,
                    "totalRegistros"=> $numeroRegistros,
                    "page"=> $page,
                    'documento' =>$documento
                ]
            );
        }
    }


    //CUANDO UN editor NO EXISTE EN LA BASE DE DATOS
    //ESTA VISTA MANDA EL FORMULARIO PARA AGREGAR UN NUEVO editor

    public function nuevoEditorDocumento(Request $request,$id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
         $documento =Documento::findOrFail($id);

        return view('documentoEditor.createEditorDocumento',
        [
            'documento'=> $documento,
        ]);

    }

    //NO BORRAR ESTOS METODOS, QUE AUNQUE NO SE OCUPAN, SON NECESARIOS
    public function show()
    {
    }

    public function edit()
    {

    }

     //ESTE METODO CREA AL editor Y LO LIGA AUTOMATICAMENTE
    //CON ESE DOCUMENTO
    public function update(EditorFormRequest $request, $id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        //ID DEL DOCUMENTO
        $documento =Documento::findOrFail($id);

        $auxEditor =  $request->get('editor')? $request->get('editor'):'';
        $auxPais = $request->get('pais')? $request->get('pais'):'';
        $auxEstado =$request->get('estado')?$request->get('estado'):'';

        $editorExistente = Editor::where('editor',$auxEditor)->first(); //Obtengo si existe un editor con esos datos en la base

       

        if($editorExistente){

            $vinculoEditor = DocumentoEditor::where('fk_doc',$documento->Id_doc)->where('fk_editor',$editorExistente->id_editor)->first();

            if($vinculoEditor==null){
                $orden=DB::table('cntrl_editor')->max('orden');

                $respuesta =DocumentoEditor::firstOrCreate([
                    'fk_doc' => $documento->Id_doc,
                    'fk_editor' => $editorExistente->id_editor],
                    [
                     'orden'=>$orden+1,

                    'fk_doc' =>  $documento->Id_doc,
                    'fk_editor' => $editorExistente->id_editor
                    ]);
                //REGRESA A LA VISTA ORIGINAL DONDE PODRAS VER LOS editores LIGADOS
                $ruta = "/cntrl_editor/ligar/".$documento->Id_doc;
                Session::flash('flash_message3', 'El documento se vinculó con el editor  Id : ('.$editorExistente->id_editor.')');

                    //activar el log de la base de datos
                    DB::connection()->enableQueryLog();
                    //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                    LogController::agregarLog(
                        1,
                        "cntrl_editor",
                        "Se agregó el vínculo: ". json_encode($respuesta)
                    );

                    return Redirect::to($ruta);




            }else{
                
                Session::flash('flash_message2', 'El vínculo ya existe.');
                return Redirect::to("/cntrl_editor/ligar/".$documento->Id_doc);
            }





        }else{

        //EL ORDEN ES EL MAXIMO ORDEN +1
        $orden=DB::table('cntrl_editor')->max('orden');
        //datos del editor
        $editor = new Editor;
        $idEditor = Utilidad::getId("editor","id_editor");
        $editor->id_editor=$idEditor;
        $editor->editor = $auxEditor;
        $editor->pais=$auxPais;
		$editor->estado=$auxEstado;
		$editor->der_autor=$request->get('der_autor')==null? '' :$editor->der_autor=$request->get('der_autor');
        DB::connection()->enableQueryLog();
        //SE GUARDA EL editor
        $editor->save();

        //SE CREA LA LIGADURA
        $respuesta =DocumentoEditor::firstOrCreate([
                    'fk_doc' => $documento->Id_doc,
                    'fk_editor' => $idEditor],
                    [
                     'orden'=>$orden+1,

                    'fk_doc' =>  $documento->Id_doc,
                    'fk_editor' => $idEditor
                    ]);
        //REGRESA A LA VISTA ORIGINAL DONDE PODRAS VER LOS editores LIGADOS
            $ruta = "/cntrl_editor/ligar/".$documento->Id_doc.$respuesta;
            Session::flash('flash_message3', 'El documento se vinculó con el editor  Id : ('.$idEditor.')');

                //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    1,
                    "cntrl_editor",
                    "Se agregó el vínculo: ". json_encode($respuesta)
                );

                LogController::agregarLog(
                    1,
                    "editor",
                    "Se agregó el editor: ". json_encode($editor)
                );



            return Redirect::to($ruta);
            }
    }

    //AQUI SE DESVINCULA EL DOCUMENTO DEL editor
    public function destroy($id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        //Buscar el vinculo
        $vinculo = DocumentoEditor::findOrFail($id);
        $idEditor = $vinculo->fk_editor;

        //y se elimina
        $vinculo->delete();

        //COMPROBAR SI EL AUTOR TIENE MÁS VINCULOS
        //SI YA NO TIENE BORRAR AUTOR
        if (DocumentoEditor::where('fk_editor', '=', $idEditor)->count() == 0) {
            $editor =Editor::findOrFail($idEditor);
            $editor->delete();
            //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    3,
                    "editor",
                    "Se eliminó el Editor(".$editor.")"
                );
        }

        Session::flash('flash_message4', '¡El editor se desvinculó existosamente!');

        //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    3,
                    "cntrl_editor",
                    "Se eliminó el vínculo: Editor(".$vinculo->fk_editor.") del Doc (".$vinculo->fk_doc.")"
                );
        return Redirect::to("/cntrl_editor/ligar/".$vinculo->fk_doc);
    }
}
