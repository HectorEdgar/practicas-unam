<?php

namespace sistema\Http\Controllers;

use Illuminate\Http\Request;
use sistema\DocumentoInstitucion;
use sistema\Documento;
use sistema\Institucion;
use sistema\Http\Requests\InstitucionFormRequest;
use sistema\Http\Requests\DocumentoInstitucionFormRequest;
use Illuminate\Support\Facades\Redirect;
use DB;
use Session;
use sistema\Utilidad;
use sistema\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PheRum\BBCode\Facades\BBCode;
use sistema\ObraInstitucion;

class DocumentoInstitucionController extends Controller
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
    public function store(DocumentoInstitucionFormRequest $request)
    {
                if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
                $ruta = "/cntrl_institucion/ligar/".$request->get('fk_doc');
                $respuesta = DocumentoInstitucion::firstOrCreate([
                    'fk_doc' => $request->get('fk_doc'),
                    'fk_instit' => $request->get('fk_instit')],
                    [
                    'fk_doc' => $request->get('fk_doc'),
                    'fk_instit' => $request->get('fk_instit')
                    ]);

                //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    1,
                    "cntrl_instit",
                    "Se agregó el vinculo: ". json_encode($respuesta)
                );
                Session::flash('flash_message', '¡Vinculación exitosa!');
               // Session::flash('flash_message2', 'Ese vinculo ya existe!'.$respuesta);

                return Redirect::to($ruta);
    }

    public static function eliminarDocumentoCascada($idDocumento)
    {

        $items=DocumentoInstitucion::where('fk_doc', $idDocumento)->get();

        foreach ($items as $item) {
            $numItems = DocumentoInstitucion::where('fk_instit', $item->fk_instit)
                ->count();

            //Log::warning('numItemsDocumentoLigado: ' . $numItems);

            if ($numItems == 1) {
                $itemControl = DocumentoInstitucion::where('fk_instit', $item->fk_instit)
                    ->where('fk_doc', $item->fk_doc)
                    ->first();
            //Log::warning('itemControl: ' . $itemControl);
                $itemIdControl = $itemControl->fk_instit;
                $itemObjeto = Institucion::where("Id_institucion", $itemIdControl)
                    ->first();
                Log::warning('itemControl: ' . $itemControl);
                Log::warning('itemObjeto: ' . $itemObjeto);
                $itemControl = DocumentoInstitucion::where('fk_instit', $item->fk_instit)
                    ->where('fk_doc', $item->fk_doc)
                    ->delete();
                    if($itemObjeto){
                        $itemObjeto->delete();

                    }
              
            }
        }
        $itemControl = DocumentoInstitucion::where('fk_doc', $idDocumento)->delete();
    }
    //AQUI SE MUESTRAN LOS institucions QUE SE PUEDEN VINCULAR CON EL DOCUMENTO
    public function ligarDocumento(Request $request,$id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
         $documento =Documento::findOrFail($id);
         $documentoClase =Documento::findOrFail($id);

         if ($request) {
            $query = trim($request->get('searchText'));

            $itemsDocumento =DB::table('cntrl_instit as ca')
            ->join('institucion as a','a.Id_institucion','=','ca.fk_instit')
            ->where('fk_doc',$documento->Id_doc)
            ->get();

            $idLigados = DB::table('cntrl_instit as ca')
                ->join('institucion as a', 'a.Id_institucion', '=', 'ca.fk_instit')
                ->where('fk_doc', $documento->Id_doc)
                ->pluck("a.Id_institucion")
                ->all();

            $numeroRegistros = DB::table('institucion')
                ->where('nombre', 'LIKE', '%' . $query . '%')
                ->orwhere('siglas', 'LIKE', '%' . $query . '%')
                ->orwhere('Id_institucion', 'LIKE', '%' . $query . '%')
                ->orderBy('nombre', 'asc')
                ->count() - count($idLigados);


            //Desde este momento se empieza a crear la MAGIA
            $items = "";
            $numeroItems = 10;
            $auxIdLigados = $idLigados;
            while (true) {
                $aux = $numeroItems;

                $idsItems = DB::table('institucion')
                    ->where('nombre', 'LIKE', '%' . $query . '%')
                    ->orwhere('siglas', 'LIKE', '%' . $query . '%')
                    ->orwhere('Id_institucion', 'LIKE', '%' . $query . '%')
                    ->orderBy('nombre', 'asc')
                    ->paginate($numeroItems)
                    ->pluck("Id_institucion");


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
                    $items = DB::table('institucion')
                        ->where('nombre', 'LIKE', '%' . $query . '%')
                        ->orwhere('siglas', 'LIKE', '%' . $query . '%')
                        ->orwhere('Id_institucion', 'LIKE', '%' . $query . '%')
                        ->orderBy('nombre', 'asc')
                        ->paginate($numeroItems);

                    $itemsFinal = array();
                    foreach ($items as $item) {
                        $bandera = true;
                        foreach ($idLigados as $idLigado) {
                            if ($item->Id_institucion == $idLigado) {
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
           $filtro = DB::table('institucion')
           ->whereNotIn('Id_institucion', DB::table('cntrl_instit')
           ->join('institucion', 'cntrl_instit.fk_instit', '=', 'institucion.Id_institucion')
           ->where('cntrl_instit.fk_doc', $documento->Id_doc)
           ->pluck('institucion.Id_institucion')->values())->orderBy('Id_institucion', 'desc')
           ->paginate(10);
        */
            $page=$request->get('page');

            if($page==null){
                $page=1;
            }
            /*
            $numeroRegistros= DB::table('institucion')
            ->whereNotIn('Id_institucion', DB::table('cntrl_instit')
           ->join('institucion', 'cntrl_instit.fk_instit', '=', 'institucion.Id_institucion')
           ->where('cntrl_instit.fk_doc', $documento->Id_doc)
           ->pluck('institucion.Id_institucion')
           ->values())
           ->orderBy('Id_institucion', 'desc')
           ->count();
            */

            return view('documentoInstitucion.index',
                [
                    'filtro'=> $itemsFinal,
                    'cntrl_instit'=>$documento->Id_doc,
                    'instituciones' => $itemsFinal,
                    'institucionesdelDocumento' => $itemsDocumento,
                    "searchText" => $query,
                    "totalRegistros"=> $numeroRegistros,
                    "page"=> $page,
                    'documento' =>$documento
                ]
            );
        }
    }


    //CUANDO UN institucion NO EXISTE EN LA BASE DE DATOS
    //ESTA VISTA MANDA EL FORMULARIO PARA AGREGAR UN NUEVO institucion

    public function nuevoInstitucionDocumento(Request $request,$id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
         $documento =Documento::findOrFail($id);
        $paises= DB::table('paises')->orderBy('nombre', 'asc')->get();

        return view('documentoInstitucion.createInstitucionDocumento',
        [
            'paises'=> $paises,
            'documento'=> $documento,
        ]);

    }

    public function show()
    {
        //
    }

    public function edit()
    {

    }



     //ESTE METODO CREA AL institucion Y LO LIGA AUTOMATICAMENTE
    //CON ESE DOCUMENTO
    public function update(InstitucionFormRequest $request, $id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}

        //ID DEL DOCUMENTO
        $documento = Documento::findOrFail($id);
        $auxNombre = $request->get('nombre')?$request->get('nombre'):'';
        $auxPais = $request->get('pais')?$request->get('pais'):'';
        $auxSiglas = $request->get('siglas')?$request->get('siglas'):'';

        $institucionExistente = Institucion::where('pais',$auxPais)->where('nombre',$auxNombre)->where('siglas',$auxSiglas)->first(); //Obtengo si existe la institucion con esos datos en la base

        if($institucionExistente){

            $vinculoInstitucion = DocumentoInstitucion::where('fk_doc',$documento->Id_doc)->where('fk_instit',$institucionExistente->Id_institucion)->first();

            if($vinculoInstitucion==null){

                $respuesta = DocumentoInstitucion::firstOrCreate([
                    'fk_doc' => $documento->Id_doc,
                    'fk_instit' => $institucionExistente->Id_institucion],
                    [
                    'fk_doc' =>  $documento->Id_doc,
                    'fk_instit' => $institucionExistente->Id_institucion
                    ]);
        //REGRESA A LA VISTA ORIGINAL DONDE PODRAS VER LOS institucion LIGADOS
            $ruta = "/cntrl_institucion/ligar/".$documento->Id_doc;
            Session::flash('flash_message3', 'El documento se vinculó con la institución  Id : ('.$institucionExistente->Id_institucion.')');

            //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    1,
                    "cntrl_institución",
                    "Se agregó el vínculo: ". json_encode($respuesta)
                );

                return Redirect::to($ruta);
                


            }else{

                Session::flash('flash_message2', 'El vínculo ya existe.');
                return Redirect::to("/cntrl_institucion/ligar/".$documento->Id_doc);



            }
        }else{


        //datos del institucion
        $institucion = new Institucion;
        $idInstitucion =Utilidad::getId("institucion","Id_institucion");

        $institucion->Id_institucion=$idInstitucion;
        $institucion->nombre= $request->get('nombre');

  

		$institucion->siglas=$auxSiglas;
 		$institucion->pais= $auxPais;
 		$institucion->localidad= "";
 		$sector = $request->input('extra');

        if($sector=='Sector Institucional/Gubernamental'){
             $institucion->extra =2;
        }
        else if($sector=='Sector Social'){
        $institucion->extra =1;
        }
        else {
             $institucion->extra =0;
        }
        DB::connection()->enableQueryLog();
        //SE GUARDA EL institucion
        $institucion->save();
        //SE CREA LA LIGADURA
        $respuesta = DocumentoInstitucion::firstOrCreate([
                    'fk_doc' => $documento->Id_doc,
                    'fk_instit' => $idInstitucion],
                    [
                    'fk_doc' =>  $documento->Id_doc,
                    'fk_instit' => $idInstitucion
                    ]);
        //REGRESA A LA VISTA ORIGINAL DONDE PODRAS VER LOS institucion LIGADOS
            $ruta = "/cntrl_institucion/ligar/".$documento->Id_doc.$respuesta;
            Session::flash('flash_message3', 'El documento se vinculó con la institución  Id : ('.$idInstitucion.')');

            //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    1,
                    "cntrl_institución",
                    "Se agregó el vínculo: ". json_encode($respuesta)
                );

                LogController::agregarLog(
                    1,
                    "institucion",
                    "Se agregó la Institución: ". json_encode($institucion)
                );

            return Redirect::to($ruta);
            }
    }

    //AQUI SE DESVINCULA EL DOCUMENTO DEL institucion
    public function destroy($id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        //Buscar el vinculo en la tabla cntrl correspondiente
        $aux =DB::table('cntrl_instit')
        ->where('fk_instit',$id)
        ->where('fk_doc',$documento->Id_doc)
        ->get();

        //y se elimina
        $aux->delete();

        return Redirect::to("/cntrl_institucion/ligar/".$documento->Id_doc);
    }

     public function destroy2($id,$id2)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        //Buscar el vinculo en la tabla cntrl correspondiente
        //y se elimina

        $numInstituciones=DocumentoInstitucion::where('fk_instit', $id)->count();
        $numInstitucionesObra=ObraInstitucion::where('fk_inst',$id)->count();                //activar el log de la base de datos
        DB::connection()->enableQueryLog();
        if($numInstituciones>1 || $numInstitucionesObra>0){
            DocumentoInstitucion::where('fk_instit', $id)->where('fk_doc', $id2)->delete();
        }else{
            DocumentoInstitucion::where('fk_instit', $id)->where('fk_doc', $id2)->delete();
            Institucion::where('Id_institucion',$id)->delete();
        }

        Session::flash('flash_message4', ' ¡La institución se desvinculó existosamente!');

                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    3,
                    "cntrl_institución",
                    "Se eliminó el vínculo: Institucion(".$id.") del Doc (".$id2.")"
                );
        return Redirect::to("/cntrl_institucion/ligar/".$id2);
    }
}
