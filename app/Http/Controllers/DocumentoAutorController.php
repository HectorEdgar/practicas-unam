<?php

namespace sistema\Http\Controllers;

use sistema\DocumentoAutor;
use sistema\Documento;
use sistema\Autor;
use sistema\Http\Requests\AutorFormRequest;
use sistema\Http\Requests\DocumentoAutorFormRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use DB;
use Session;
use sistema\Utilidad;
use Illuminate\Support\Facades\Auth;

use sistema\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\AcceptHeaderItem;
use Illuminate\Support\Facades\Log;

class DocumentoAutorController extends Controller
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
    public function store(DocumentoAutorFormRequest $request)
    {
                        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}

            //EL ORDEN ES EL MAXIMO ORDEN +1
            $orden=DB::table('cntrl_autor')->max('orden');
            $AuxExtra="";
            if($request->get('extra')==null)
                $AuxExtra ="";
            else
                $AuxExtra=$request->get('extra');
            //cuando se inserta, va regresar a esta misma ruta
                $ruta = "/cntrl_autor/ligar/".$request->get('fk_doc');
                $respuesta = DocumentoAutor::firstOrCreate([
                    'fk_doc' => $request->get('fk_doc'),
                    'fk_autor' => $request->get('fk_autor')],
                    [
                     'orden'=>$orden+1,
                     'extra'=> $AuxExtra,
                    'fk_doc' => $request->get('fk_doc'),
                    'fk_autor' => $request->get('fk_autor')
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
                    "cntrl_autor",
                    "Se agregó el autor: ". json_encode($respuesta)
                );

                return Redirect::to($ruta);
    }

    public static function eliminarDocumentoCascada($idDocumento) {
        //obtener todos los vinculos del documento con autor
        //iterar los vinculos
        //comprobar que cada autor no tenga vinculo con otros documentos
        //si tienen vinculos solo se elimina el vinculo con ese doc si no se elimina tambien el autor
        $items=DocumentoAutor::where('fk_doc', $idDocumento)->get();
        foreach ($items as $item) {
            $numItems = DocumentoAutor::where('fk_autor', $item->fk_autor)
                ->count();

            //Log::warning('numItemsDocumentoLigado: ' . $numItems);

            if ($numItems == 1) {
                $itemControl = DocumentoAutor::where('fk_autor', $item->fk_autor)
                    ->where('fk_doc', $item->fk_doc)
                    ->first();
            //Log::warning('itemControl: ' . $itemControl);
                $itemIdControl = $itemControl->fk_autor;
                $itemObjeto = Autor::where("Id_autor", $itemIdControl)
                    ->first();
                Log::warning('itemControl: ' . $itemControl);
                Log::warning('itemObjeto: ' . $itemObjeto);
                $itemControl = DocumentoAutor::where('fk_autor', $item->fk_autor)
                    ->where('fk_doc', $item->fk_doc)
                    ->delete();
                $itemObjeto->delete();
            }
        }
        $itemControl = DocumentoAutor::where('fk_doc', $idDocumento)->delete();

    }
    //AQUI SE MUESTRAN LOS AUTORES QUE SE PUEDEN VINCULAR CON EL DOCUMENTO
    public function ligarDocumento(Request $request,$id)
    {
                if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}

         $documento =Documento::findOrFail($id);
         if ($request) {
            $query = trim($request->get('searchText'));



            $itemsDocumento =DB::table('cntrl_autor as ca')
            ->join('autor as a','a.Id_autor','=','ca.fk_autor')
            ->where('fk_doc',$documento->Id_doc)
            ->orderBy('Id_autor', 'desc')
            ->get();

            $idLigados = DB::table('cntrl_autor as ca')
                ->join('autor as a', 'a.Id_autor', '=', 'ca.fk_autor')
                ->where('fk_doc', $documento->Id_doc)
                ->orderBy('Id_autor', 'desc')
                ->pluck("Id_autor")
                ->all();

            $numeroRegistros = DB::table('autor')
                ->where('nombre', 'LIKE', '%' . $query . '%')
                ->orwhere('apellidos', 'LIKE', '%' . $query . '%')
                ->orwhere('pseudonimo', 'LIKE', '%' . $query . '%')
                ->orwhere('Id_autor', 'LIKE', '%' . $query . '%')
                ->orderBy('apellidos', 'asc')
                ->count() - count($idLigados);


            //Desde este momento se empieza a crear la MAGIA
            $items = "";
            $numeroItems = 10;
            $auxIdLigados = $idLigados;
            while (true) {
                $aux = $numeroItems;

                $idsItems = DB::table('autor')
                    ->where('nombre', 'LIKE', '%' . $query . '%')
                    ->orwhere('apellidos', 'LIKE', '%' . $query . '%')
                    ->orwhere('pseudonimo', 'LIKE', '%' . $query . '%')
                    ->orwhere('Id_autor', 'LIKE', '%' . $query . '%')
                    ->orderBy('apellidos', 'asc')
                    ->paginate($numeroItems)
                    ->pluck("Id_autor");


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
                    $items = DB::table('autor')
                        ->where('nombre', 'LIKE', '%' . $query . '%')
                        ->orwhere('apellidos', 'LIKE', '%' . $query . '%')
                        ->orwhere('pseudonimo', 'LIKE', '%' . $query . '%')
                        ->orwhere('Id_autor', 'LIKE', '%' . $query . '%')
                        ->orderBy('apellidos', 'asc')
                        ->paginate($numeroItems);

                    $itemsFinal = array();
                    foreach ($items as $item) {
                        $bandera = true;
                        foreach ($idLigados as $idLigado) {
                            if ($item->Id_autor == $idLigado) {
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

        /*SELECT * from autor where autor.Id_autor not in
        (select ca.fk_autor from cntrl_autor ca join autor a
        on a.Id_autor = ca.fk_autor where ca.fk_doc=7993)*/
        /*
           $filtro = DB::table('autor')
           /*->where(function ($query2) {
                $query2->where('nombre', 'LIKE', '%' . $query . '%')
                ->where('apellidos', 'LIKE', '%' . $query . '%')
                ->where('pseudonimo', 'LIKE', '%' . $query . '%')
                ->where('Id_autor', 'LIKE', '%' . $query . '%');
            })
           ->whereNotIn('Id_autor', DB::table('cntrl_autor')
           ->join('autor', 'cntrl_autor.fk_autor', '=', 'autor.Id_autor')
                ->where('cntrl_autor.fk_doc', $documento->Id_doc)
                ->pluck('autor.Id_autor')
                ->values())

            ->orderBy('Id_autor', 'desc')
            ->paginate(10);
        */
            $page=$request->get('page');

            if($page==null){
                $page=1;
            }
            /*
            $numeroRegistros= DB::table('autor')
            ->whereNotIn('Id_autor', DB::table('cntrl_autor')
            ->join('autor', 'cntrl_autor.fk_autor', '=', 'autor.Id_autor')
                ->where('cntrl_autor.fk_doc', $documento->Id_doc)
                ->pluck('autor.Id_autor')
                ->values())
           ->orderBy('Id_autor', 'desc')
           ->count();
            */
            return view('documentoAutor.index',
                [
                    'filtro'=> $itemsFinal,

                    'cntrl_autor'=>$documento->Id_doc,
                    'autores' => $itemsFinal,
                    'autoresdelDocumento' => $itemsDocumento,
                    "searchText" => $query,
                    "totalRegistros"=> $numeroRegistros,
                    "page"=> $page,
                    'documento' =>$documento
                ]
            );
        }
    }


    //CUANDO UN AUTOR NO EXISTE EN LA BASE DE DATOS
    //ESTA VISTA MANDA EL FORMULARIO PARA AGREGAR UN NUEVO AUTOR

    public function nuevoAutorDocumento(Request $request,$id)
    {
                                if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}

         $documento =Documento::findOrFail($id);

        return view('documentoAutor.createAutorDocumento',
        [
            'documento'=> $documento,
        ]);

    }

    public function show()
    {

    }

    public function edit(DocumentoAutor $documentoAutor)
    {
        //
    }



     //ESTE METODO CREA AL AUTOR Y LO LIGA AUTOMATICAMENTE
    //CON ESE DOCUMENTO
    public function update(AutorFormRequest $request, $id)
    {
                                if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        //ID DEL DOCUMENTO
        $documento =Documento::findOrFail($id);



        $ruta = "/cntrl_autor/ligar/".$documento->Id_doc;
        $ruta2 = "/cntrl_autor/nuevoAutor/".$documento->Id_doc;


    //VALIDACIÓN
    if($request->get('pseudonimo')==null && $request->get('nombre')==null ){
        Session::flash('Aviso', 'Ingrese por lo menos el Nombre del Autor');
        return Redirect::to($ruta2);
    }
    else {

        $auxApellidos = $request->get('apellidos')?$request->get('apellidos'):'';
        $auxNombre =  $request->get('nombre')?$request->get('nombre'):'';
        $auxPseudonimo  =$request->get('pseudonimo')?$request->get('pseudonimo'):'';


        $autorExistente = Autor::where('pseudonimo',$auxPseudonimo)->where('nombre',$auxNombre)->where('apellidos',$auxApellidos)->first(); //Obtengo si existe un autor con esos datos en la base

      
        if($autorExistente){

            $vinculoAutor = DocumentoAutor::where('fk_doc',$documento->Id_doc)->where('fk_autor',$autorExistente->Id_autor)->first();
          
            if($vinculoAutor==null){ // no existe vel vinculo
                
                $orden=DB::table('cntrl_autor')->max('orden');
                $AuxExtra = $request->get('extra')?$request->get('extra'):'';
                //SE CREA LA LIGADURA
                $respuesta = DocumentoAutor::firstOrCreate([
                'fk_doc' => $documento->Id_doc,
                'fk_autor' => $autorExistente->Id_autor], // id Autor que existe en la base de datos
                [
                'orden'=>$orden+1,
                 'extra'=>$AuxExtra,
                'fk_doc' =>  $documento->Id_doc,
                'fk_autor' => $autorExistente->Id_autor // id Autor que existe en la base de datos
                ]);
             //REGRESA A LA VISTA ORIGINAL DONDE PODRAS VER LOS AUTORES LIGADOS
             Session::flash('flash_message3', 'El documento se vinculó con el autor  Id : ('.$autorExistente->Id_autor.')');

             //activar el log de la base de datos
             DB::connection()->enableQueryLog();
            //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
             LogController::agregarLog(
               1,
               "cntrl_autor",
               "Se agregó el vínculo: ". json_encode($respuesta)
             );

             return Redirect::to($ruta);

            }else{

                Session::flash('flash_message5', 'El vínculo ya existe.');
                return Redirect::to($ruta);
            }


        }else{

        //EL ORDEN ES EL MAXIMO ORDEN +1
        $orden=DB::table('cntrl_autor')->max('orden');
        $idAutor = Utilidad::getId("autor","Id_autor");
        //datos del autor
        $autor = new Autor;
                $autor->Id_autor= $idAutor;
                $autor->pseudonimo = $auxPseudonimo;
                $autor->nombre =$auxNombre;
                $autor->apellidos = $auxApellidos;
        DB::connection()->enableQueryLog();
        //SE GUARDA EL AUTOR
        $autor->save();
        //SE GUARDA EN EL LOG DE CAMBIOS
        $AuxExtra = $request->get('extra')?$request->get('extra'):'';

    
        //SE CREA LA LIGADURA
        $respuesta = DocumentoAutor::firstOrCreate([
                    'fk_doc' => $documento->Id_doc,
                    'fk_autor' => $idAutor],
                    [
                     'orden'=>$orden+1,
                     'extra'=>$AuxExtra,
                    'fk_doc' =>  $documento->Id_doc,
                    'fk_autor' => $idAutor
                    ]);
        //REGRESA A LA VISTA ORIGINAL DONDE PODRAS VER LOS AUTORES LIGADOS
            Session::flash('flash_message3', 'El documento se vinculó con el autor  Id : ('.$idAutor.')');

                //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    1,
                    "cntrl_autor",
                    "Se agregó el vínculo: ". json_encode($respuesta)
                );

                LogController::agregarLog(
                    1,
                    "autor",
                    "Se agregó el autor: ". json_encode($autor)
                );

            return Redirect::to($ruta);
            }
        }
    }

    //AQUI SE DESVINCULA EL DOCUMENTO DEL AUTOR
    public function destroy($id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        //obtener el vinculo
        $vinculo = DocumentoAutor::findOrFail($id);
        $idAutor = $vinculo->fk_autor;
        //y se elimina
        $vinculo->delete();

        //COMPROBAR SI EL AUTOR TIENE MÁS VINCULOS
        //SI YA NO TIENE BORRAR AUTOR
        if (DocumentoAutor::where('fk_autor', '=', $idAutor)->count() == 0) {
            $autor =Autor::findOrFail($idAutor);
            $autor->delete();
            //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    3,
                    "autor",
                    "Se eliminó autor(".$autor.")"
                );
        }



        Session::flash('flash_message4', 'El documento se desvinculó exitosamente');
        //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    3,
                    "cntrl_autor",
                    "Se eliminó el vínculo: Autor(".$vinculo->fk_autor.") del Doc (".$vinculo->fk_doc.")"
                );
        return Redirect::to("/cntrl_autor/ligar/".$vinculo->fk_doc);
    }
}
