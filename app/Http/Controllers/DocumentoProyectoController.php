<?php

namespace sistema\Http\Controllers;

use Illuminate\Http\Request;
use sistema\DocumentoProyecto;
use sistema\Documento;
use sistema\Proyecto;
use sistema\Http\Requests\ProyectoFormRequest;
use sistema\Http\Requests\DocumentoProyectoFormRequest;
use Illuminate\Support\Facades\Redirect;
use DB;
use Session;
use sistema\Utilidad;
use sistema\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class DocumentoProyectoController extends Controller
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
    public function store(DocumentoProyectoFormRequest $request)
    {
           if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
            //EL ORDEN ES EL MAXIMO ORDEN +1
            //$orden=DB::table('cntrl_proyec')->max('orden');
            //cuando se inserta, va regresar a esta misma ruta
                $ruta = "/cntrl_proyecto/ligar/".$request->get('fk_doc');
                $respuesta = DocumentoProyecto::firstOrCreate([
                    'fk_doc' => $request->get('fk_doc'),
                    'fk_proyec' => $request->get('fk_proyec')],
                    [
                    'fk_doc' => $request->get('fk_doc'),
                    'fk_proyec' => $request->get('fk_proyec')
                    ]);

                $proyecto =Proyecto::findOrFail($request->get('fk_proyec'));

                //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    1,
                    "cntrl_proyec",
                    "Se agregó el vínculo: ". json_encode($respuesta)
                );


                Session::flash('flash_message', 'Vinculación exitosa con el proyecto: ('.$proyecto->proyecto.')');
               // Session::flash('flash_message2', 'Ese vinculo ya existe!'.$respuesta);

                return Redirect::to($ruta);
    }

    //AQUI SE MUESTRAN LOS proyectos QUE SE PUEDEN VINCULAR CON EL DOCUMENTO
    public function ligarDocumento(Request $request,$id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
         $documento =Documento::findOrFail($id);
         $documentoClase =Documento::findOrFail($id);

         if ($request) {
            $query = trim($request->get('searchText'));

            $itemsDocumento =DB::table('cntrl_proyec as ca')
            ->join('catalogo_proyecto as a','a.id_proyecto','=','ca.fk_proyec')
            ->where('fk_doc',$documento->Id_doc)
            ->orderBy('a.id_proyecto', 'desc')
            ->get();

            $idLigados = DB::table('cntrl_proyec as ca')
                ->join('catalogo_proyecto as a', 'a.id_proyecto', '=', 'ca.fk_proyec')
                ->where('fk_doc', $documento->Id_doc)
                ->orderBy('a.id_proyecto', 'desc')
                ->pluck("a.id_proyecto")
                ->all();

            $numeroRegistros = DB::table('catalogo_proyecto')
                ->where('proyecto', 'LIKE', '%' . $query . '%')
                ->orwhere('id_proyecto', 'LIKE', '%' . $query . '%')
                ->orderBy('id_proyecto', 'desc')
                ->count() - count($idLigados);


            //Desde este momento se empieza a crear la MAGIA
            $items = "";
            $numeroItems = 10;
            $auxIdLigados = $idLigados;
            while (true) {
                $aux = $numeroItems;

                $idsItems = DB::table('catalogo_proyecto')
                    ->where('proyecto', 'LIKE', '%' . $query . '%')
                    ->orwhere('id_proyecto', 'LIKE', '%' . $query . '%')
                    ->orderBy('id_proyecto', 'desc')
                    ->paginate($numeroItems)
                    ->pluck("id_proyecto");


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
                    $items = DB::table('catalogo_proyecto')
                        ->where('proyecto', 'LIKE', '%' . $query . '%')
                        ->orwhere('id_proyecto', 'LIKE', '%' . $query . '%')
                        ->orderBy('id_proyecto', 'desc')
                        ->paginate($numeroItems);

                    $itemsFinal = array();
                    foreach ($items as $item) {
                        $bandera = true;
                        foreach ($idLigados as $idLigado) {
                            if ($item->id_proyecto == $idLigado) {
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
           $filtro = DB::table('catalogo_proyecto')
           ->where('proyecto', 'LIKE', '%' . $query . '%')
           ->orwhere('id_proyecto', 'LIKE', '%' . $query . '%')

           ->whereNotIn('id_proyecto', DB::table('cntrl_proyec')
           ->join('catalogo_proyecto', 'cntrl_proyec.fk_proyec', '=', 'catalogo_proyecto.id_proyecto')
           ->where('cntrl_proyec.fk_doc', $documento->Id_doc)
           ->pluck('catalogo_proyecto.id_proyecto')->values())->orderBy('id_proyecto', 'desc')
           ->paginate(10);
            */
            $page=$request->get('page');

            if($page==null){
                $page=1;
            }
            /*
            $numeroRegistros= DB::table('catalogo_proyecto')
                ->where('proyecto', 'LIKE', '%' . $query . '%')
                ->orwhere('id_proyecto', 'LIKE', '%' . $query . '%')
                ->orderBy('id_proyecto', 'desc')->count();
            */
            return view('documentoProyecto.index',
                [
                    'filtro'=> $itemsFinal,
                    'cntrl_proyecto'=>$documento->Id_doc,
                    'proyectos' => $itemsFinal,
                    'proyectosdelDocumento' => $itemsDocumento,
                    "searchText" => $query,
                    "totalRegistros"=> $numeroRegistros,
                    "page"=> $page,
                    'documento' =>$documento
                ]
            );
        }
    }


    //CUANDO UN proyecto NO EXISTE EN LA BASE DE DATOS
    //ESTA VISTA MANDA EL FORMULARIO PARA AGREGAR UN NUEVO proyecto

    public function nuevoProyectoDocumento(Request $request,$id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
         $documento =Documento::findOrFail($id);

        return view('documentoProyecto.createProyectoDocumento',
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



     //ESTE METODO CREA AL proyecto Y LO LIGA AUTOMATICAMENTE
    //CON ESE DOCUMENTO
    public function update(ProyectoFormRequest $request, $id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        //ID DEL DOCUMENTO
        $documento =Documento::findOrFail($id);
        //datos del proyecto
        $proyecto = new Proyecto;
        $idProyecto = Utilidad::getId("catalogo_proyecto","id_proyecto");
        $proyecto->id_proyecto=$idProyecto;
        $proyecto->proyecto = $request->get('proyecto');
        DB::connection()->enableQueryLog();
        //SE GUARDA EL proyecto
        $proyecto->save();
        //SE CREA LA LIGADURA
        $respuesta = DocumentoProyecto::firstOrCreate([
                    'fk_doc' => $documento->Id_doc,
                    'fk_proyec' => $idProyecto],
                    [
                    'fk_doc' =>  $documento->Id_doc,
                    'fk_proyec' => $idProyecto
                    ]);
        $proyecto =Proyecto::findOrFail($idProyecto);

        //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    1,
                    "cntrl_proyec",
                    "Se agregó el vínculo: ". json_encode($respuesta)
                );

                LogController::agregarLog(
                    1,
                    "proyectos",
                    "Se agregó el proyecto: ". json_encode($proyecto)
                );

        //REGRESA A LA VISTA ORIGINAL DONDE PODRAS VER LOS proyecto LIGADOS
            $ruta = "/cntrl_proyecto/ligar/".$documento->Id_doc.$respuesta;
            Session::flash('flash_message3', 'El documento se vinculó al Proyecto : ('.$proyecto->proyecto.') exitosamente');

            return Redirect::to($ruta);
    }

    //AQUI SE DESVINCULA EL DOCUMENTO DEL proyecto
    public function destroy($id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        //Buscar el vinculo en la tabla cntrl correspondiente
        $aux =DB::table('cntrl_proyec')
        ->where('fk_proyec',$id)
        ->where('fk_doc',$documento->Id_doc)
        ->get();

        //y se elimina
        $aux->delete();

        return Redirect::to("/cntrl_proyecto/ligar/".$documento->Id_doc);
    }

     public function destroy2($id,$id2)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        //Buscar el vinculo en la tabla cntrl correspondiente
        //y se elimina
        $borrar =DocumentoProyecto::where('fk_proyec', $id)->where('fk_doc', $id2)->delete();
        Session::flash('flash_message', ' ¡El Proyecto se desvinculó existosamente!');

        //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    3,
                    "cntrl_proyec",
                    "Se eliminó el vínculo: Proyecto(".$id.") del Doc (".$id2.")"
                );

        return Redirect::to("/cntrl_proyecto/ligar/".$id2);
    }
}
