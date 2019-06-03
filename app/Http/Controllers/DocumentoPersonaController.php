<?php

namespace sistema\Http\Controllers;

use Illuminate\Http\Request;
use sistema\DocumentoPersona;
use sistema\Documento;
use sistema\Persona;
use sistema\Http\Requests\PersonaFormRequest;
use sistema\Http\Requests\DocumentoPersonaFormRequest;
use Illuminate\Support\Facades\Redirect;
use DB;
use Session;
use sistema\Utilidad;
use sistema\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use sistema\ObraPersona;


class DocumentoPersonaController extends Controller
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
    public function store(DocumentoPersonaFormRequest $request)
    {
                if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
                $ruta = "/cntrl_persona/ligar/".$request->get('fk_doc');
                $respuesta = DocumentoPersona::firstOrCreate([
                    'fk_doc' => $request->get('fk_doc'),
                    'fk_persona' => $request->get('fk_persona')],
                    [
                    'fk_doc' => $request->get('fk_doc'),
                    'fk_persona' => $request->get('fk_persona')
                    ]);

                //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    1,
                    "cntrl_persona",
                    "Se agregó el vínculo: ". json_encode($respuesta)
                );
                Session::flash('flash_message', '¡Vinculación exitosa!');
               // Session::flash('flash_message2', 'Ese vinculo ya existe!'.$respuesta);

                return Redirect::to($ruta);
    }
    public static function eliminarDocumentoCascada($idDocumento)
    {

        $items= DocumentoPersona::where('fk_doc', $idDocumento)->get();
        foreach ($items as $item) {
            $numItems = DocumentoPersona::where('fk_persona', $item->fk_persona)
                ->count();

            //Log::warning('numItemsDocumentoLigado: ' . $numItems);

            if ($numItems == 1) {
                $itemControl = DocumentoPersona::where('fk_persona', $item->fk_persona)
                    ->where('fk_doc', $item->fk_doc)
                    ->first();
            //Log::warning('itemControl: ' . $itemControl);
                $itemIdControl = $itemControl->fk_persona;
                $itemObjeto = Persona::where("Id_persona", $itemIdControl)
                    ->first();
                Log::warning('itemControl: ' . $itemControl);
                Log::warning('itemObjeto: ' . $itemObjeto);
                $itemControl = DocumentoPersona::where('fk_persona', $item->fk_persona)
                    ->where('fk_doc', $item->fk_doc)
                    ->delete();
                $itemObjeto->delete();
            }
        }
        $itemControl = DocumentoPersona::where('fk_doc', $idDocumento)->delete();


    }

    //AQUI SE MUESTRAN LOS personas QUE SE PUEDEN VINCULAR CON EL DOCUMENTO
    public function ligarDocumento(Request $request,$id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
         $documento =Documento::findOrFail($id);
         $documentoClase =Documento::findOrFail($id);

         if ($request) {
            $query = trim($request->get('searchText'));

            $itemsDocumento =DB::table('cntrl_persona as ca')
            ->join('persona as a','a.Id_persona','=','ca.fk_persona')
            ->where('fk_doc',$documento->Id_doc)
            ->orderBy('Id_persona', 'desc')
            ->get();

            $idLigados = DB::table('cntrl_persona as ca')
                ->join('persona as a', 'a.Id_persona', '=', 'ca.fk_persona')
                ->where('fk_doc', $documento->Id_doc)
                ->orderBy('Id_persona', 'desc')
                ->pluck("a.Id_persona")
                ->all();

            $numeroRegistros = DB::table('persona')
                ->where('nombre', 'LIKE', '%' . $query . '%')
                ->orwhere('apellidos', 'LIKE', '%' . $query . '%')
                ->orwhere('cargo', 'LIKE', '%' . $query . '%')
                ->orwhere('Id_persona', 'LIKE', '%' . $query . '%')
                ->count() - count($idLigados);


            //Desde este momento se empieza a crear la MAGIA
            $items="";
            $numeroItems = 10;
            $auxIdLigados=$idLigados;
            while (true) {
                $aux = $numeroItems;

                $idsItems = DB::table('persona')
                    ->where('nombre', 'LIKE', '%' . $query . '%')
                    ->orwhere('apellidos', 'LIKE', '%' . $query . '%')
                    ->orwhere('cargo', 'LIKE', '%' . $query . '%')
                    ->orwhere('Id_persona', 'LIKE', '%' . $query . '%')
                    ->orderBy('apellidos', 'asc')
                    ->paginate($numeroItems)
                    ->pluck("Id_persona");


                for ($i=0; $i < count($idsItems); $i++) {
                    for ($j=0; $j < count($auxIdLigados) ; $j++) {
                        if ($idsItems[$i]== $auxIdLigados[$j]) {
                            $numeroItems = $numeroItems + 1;
                            $aux=$aux-1;
                            unset($auxIdLigados[$j]);
                            $auxIdLigados =array_values($auxIdLigados);
                            break;
                        }
                    }
                }
                if($aux>=10 || $numeroRegistros<10) {
                    $items = DB::table('persona')
                        ->where('nombre', 'LIKE', '%' . $query . '%')
                        ->orwhere('apellidos', 'LIKE', '%' . $query . '%')
                        ->orwhere('cargo', 'LIKE', '%' . $query . '%')
                        ->orwhere('Id_persona', 'LIKE', '%' . $query . '%')
                        ->orderBy('apellidos', 'asc')
                        ->paginate($numeroItems);

                    $itemsFinal=array();
                    foreach ($items as $item) {
                        $bandera = true;
                        foreach ($idLigados as $idLigado) {
                            if($item->Id_persona == $idLigado){
                                unset($idLigado);
                                $auxIdLigados = array_values($idLigados);
                                $bandera=false;
                                break;
                            }
                        }
                        if($bandera) {
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
           $filtro = DB::table('persona')
            ->whereNotIn(
                'Id_persona',
                DB::table('cntrl_persona')
                    ->join('persona', 'cntrl_persona.fk_persona', '=', 'persona.id_persona')
                    ->where('cntrl_persona.fk_doc', $documento->Id_doc)
                    ->pluck('persona.Id_persona')
                        ->values()
                )
            ->orderBy('Id_persona', 'desc')
            ->paginate(10);
          */
            $page=$request->get('page');

            if($page==null){
                $page=1;
            }

            return view('documentoPersona.index',
                [
                    'cntrl_persona'=>$documento->Id_doc,
                    'personas' => $itemsFinal,
                    'personasdelDocumento' => $itemsDocumento,
                    "searchText" => $query,
                    "totalRegistros"=> $numeroRegistros,
                    "page"=> $page,
                    'documento' =>$documento,
                ]
            );
        }
    }


    //CUANDO UN persona NO EXISTE EN LA BASE DE DATOS
    //ESTA VISTA MANDA EL FORMULARIO PARA AGREGAR UN NUEVO persona

    public function nuevoPersonaDocumento(Request $request,$id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
         $documento =Documento::findOrFail($id);

        return view('documentoPersona.createPersonaDocumento',
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



     //ESTE METODO CREA AL persona Y LO LIGA AUTOMATICAMENTE
    //CON ESE DOCUMENTO
    public function update(PersonaFormRequest $request, $id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}

        //ID DEL DOCUMENTO
        $documento = Documento::findOrFail($id);
        $auxNombre = $request->get('nombre')?$request->get('nombre'):'';
        $auxApellidos = $request->get('apellidos')?$request->get('apellidos'):'';
        


        $personaExistente = Persona::where('nombre',$auxNombre)->where('apellidos',$auxApellidos)->first(); 


        if($personaExistente){

            $vinculoPersona = DocumentoPersona::where('fk_doc',$documento->Id_doc)->where('fk_persona',$personaExistente->Id_persona)->first();


            if( $vinculoPersona == null){

                $respuesta = DocumentoPersona::firstOrCreate([
                    'fk_doc' => $documento->Id_doc,
                    'fk_persona' => $personaExistente->Id_persona],
                    [
                    'fk_doc' =>  $documento->Id_doc,
                    'fk_persona' => $personaExistente->Id_persona
                    ]);
                //REGRESA A LA VISTA ORIGINAL DONDE PODRAS VER LOS persona LIGADOS
                 $ruta = "/cntrl_persona/ligar/".$documento->Id_doc;
                 Session::flash('flash_message3', 'El documento se vinculó con la persona  Id : ('.$personaExistente->Id_persona.')');

                 //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    1,
                    "cntrl_persona",
                    "Se agregó el vínculo: ". json_encode($respuesta)
                );

                return Redirect::to($ruta);



            }else{

                Session::flash('flash_message2', 'El vínculo ya existe.');
                return Redirect::to( "/cntrl_persona/ligar/".$documento->Id_doc);


            }

        }else{
        //datos del persona
        $persona = new Persona;
        $idPersona = Utilidad::getId("persona","Id_persona");
        $persona->Id_persona=$idPersona;

       
       

        $persona->cargo =$request->get('cargo')?$request->get('cargo'):'';
        $persona->nombre = $auxNombre;
        $persona->apellidos = $auxApellidos;
        $sector = $request->input('extra');

        if($sector=='Sector Institucional/Gubernamental'){
             $persona->extra =2;
        }
        else if($sector=='Sector Social'){
        $persona->extra =1;
        }
        else {
             $persona->extra =0;
        }

        DB::connection()->enableQueryLog();
        //SE GUARDA EL persona
        $persona->save();
        //SE CREA LA LIGADURA
        $respuesta = DocumentoPersona::firstOrCreate([
                    'fk_doc' => $documento->Id_doc,
                    'fk_persona' => $idPersona],
                    [
                    'fk_doc' =>  $documento->Id_doc,
                    'fk_persona' => $idPersona
                    ]);
        //REGRESA A LA VISTA ORIGINAL DONDE PODRAS VER LOS persona LIGADOS
            $ruta = "/cntrl_persona/ligar/".$documento->Id_doc.$respuesta;
            Session::flash('flash_message3', 'El documento se vinculó con la persona  Id : ('.$idPersona.')');

            //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    1,
                    "cntrl_persona",
                    "Se agregó el vínculo: ". json_encode($respuesta)
                );

                LogController::agregarLog(
                    1,
                    "persona",
                    "Se agregó la persona: ". json_encode($persona)
                );

            return Redirect::to($ruta);

            }
    }

    //AQUI SE DESVINCULA EL DOCUMENTO DEL persona
    public function destroy($id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        //Buscar el vinculo en la tabla cntrl correspondiente
        $aux =DB::table('cntrl_persona')
        ->where('fk_persona',$id)
        ->where('fk_doc',$documento->Id_doc)
        ->get();

        //y se elimina
        $aux->delete();

        return Redirect::to("/cntrl_persona/ligar/".$documento->Id_doc);
    }

     public function destroy2($id,$id2)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        //Buscar el vinculo en la tabla cntrl correspondiente
        //y se elimina

        $numPersonas=DocumentoPersona::where('fk_persona', $id)->count();
        $numPersonasObra=ObraPersona::where('fk_persona',$id)->count();
        DB::connection()->enableQueryLog();
        if($numPersonas>1 || $numPersonasObra>0){
            DocumentoPersona::where('fk_persona', $id)->where('fk_doc', $id2)->delete();
        }else{
            DocumentoPersona::where('fk_persona', $id)->where('fk_doc', $id2)->delete();
            Persona::where('Id_persona',$id)->delete();
        }





        Session::flash('flash_message4', 'La persona se desvinculó Existosamente!');
        //activar el log de la base de datos

                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    3,
                    "cntrl_persona",
                    "Se eliminó el vinculo: Persona(".$id.") del Doc (".$id2.")"
                );
        return Redirect::to("/cntrl_persona/ligar/".$id2);
    }
}
