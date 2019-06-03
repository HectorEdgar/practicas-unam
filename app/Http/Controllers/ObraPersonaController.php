<?php

namespace sistema\Http\Controllers;

use Illuminate\Http\Request;
use sistema\ObraPersona;
use sistema\Persona;
use sistema\Obra;
use sistema\Http\Requests\PersonaFormRequest;
use sistema\Http\Requests\ObraPersonaFormRequest;
use Illuminate\Support\Facades\Redirect;
use DB;
use Session;
use sistema\Utilidad;
use sistema\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use sistema\DocumentoPersona;

class ObraPersonaController extends Controller
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
    public function store(ObraPersonaFormRequest $request)
    {
                if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
                $ruta = "/obra_persona/ligar/".$request->get('fk_obra');
                $respuesta = ObraPersona::firstOrCreate([
                    'fk_obra' => $request->get('fk_obra'),
                    'fk_persona' => $request->get('fk_persona')],
                    [
                    'fk_obra' => $request->get('fk_obra'),
                    'fk_persona' => $request->get('fk_persona')
                    ]);

                //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    1,
                    "obra_persona",
                    "Se agregó el vinculo: ". json_encode($respuesta)
                );
                Session::flash('flash_message', ' ¡Vinculación exitosa!');
               // Session::flash('flash_message2', 'Ese vinculo ya existe!'.$respuesta);

                return Redirect::to($ruta);
    }
    public static function eliminarObraCascada($idObra)
    {
        $items = ObraPersona::where("fk_obra", $idObra)->get();
        foreach ($items as $item) {
            $numItems = ObraPersona::where('fk_persona', $item->fk_persona)
                ->count();

            Log::warning('numItemsDocumentoLigado: ' . $numItems);

            if ($numItems == 1) {
                $itemControl = ObraPersona::where('fk_persona', $item->fk_persona)
                    ->where("fk_obra", $idObra)
                    ->first();
                //Log::warning('itemControl: ' . $itemControl);
                $itemIdControl = $itemControl->fk_persona;

               // Log::warning('itemIDControl: ' . $itemIdControl);

                $itemObjeto = Persona::where('Id_persona', $itemIdControl)
                    ->first();

                //Log::warning('itemControl: ' . $itemControl);
                //Log::warning('itemObjeto: ' . $itemObjeto);

                $itemControl = ObraPersona::where('fk_persona', $item->fk_persona)
                    ->where("fk_obra", $idObra)
                    ->delete();
                    if($itemObjeto){
                        $itemObjeto->delete();

                    }

               
            }
        }
        ObraPersona::where("fk_obra", $idObra)->delete();
    }
    //AQUI SE MUESTRAN LOS personas QUE SE PUEDEN VINCULAR CON EL DOCUMENTO
    public function ligarObra(Request $request,$id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
         $obra =Obra::findOrFail($id);
         $obraClase =Obra::findOrFail($id);

         if ($request) {
            $query = trim($request->get('searchText'));

            $personasdelaObra =DB::table('obra_persona as ca')
            ->join('persona as a','a.Id_persona','=','ca.fk_persona')
            ->where('fk_obra',$obra->id_obra)
            ->orderBy('Id_persona', 'desc')
            ->get();

            $idLigados = DB::table('obra_persona as ca')
                ->join('persona as a', 'a.Id_persona', '=', 'ca.fk_persona')
                ->where('fk_obra', $obra->id_obra)
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
            $personas="";
            $numeroElementos = 10;
            $auxIdLigados=$idLigados;
            while (true) {
                $aux = $numeroElementos;

                $idPersonas = DB::table('persona')
                ->where('nombre', 'LIKE', '%' . $query . '%')
                ->orwhere('apellidos', 'LIKE', '%' . $query . '%')
                ->orwhere('cargo', 'LIKE', '%' . $query . '%')
                ->orwhere('Id_persona', 'LIKE', '%' . $query . '%')
                ->orderBy('apellidos', 'asc')
                ->paginate($numeroElementos)
                ->pluck("Id_persona");


                for ($i=0; $i < count($idPersonas); $i++) {
                    for ($j=0; $j < count($auxIdLigados) ; $j++) {
                        if ($idPersonas[$i]== $auxIdLigados[$j]) {
                            $numeroElementos= $numeroElementos + 1;
                            $aux=$aux-1;
                            unset($auxIdLigados[$j]);
                            $auxIdLigados =array_values($auxIdLigados);
                            break;
                        }
                    }
                }
                if($aux>=10 || $numeroRegistros<10) {
                    $personas = DB::table('persona')
                        ->where('nombre', 'LIKE', '%' . $query . '%')
                        ->orwhere('apellidos', 'LIKE', '%' . $query . '%')
                        ->orwhere('cargo', 'LIKE', '%' . $query . '%')
                        ->orwhere('Id_persona', 'LIKE', '%' . $query . '%')
                        ->orderBy('apellidos', 'asc')
                        ->paginate($numeroElementos);

                    $personasFinal=array();
                    foreach ($personas as $persona) {
                        $bandera = true;
                        foreach ($idLigados as $idLigado) {
                            if($persona->Id_persona == $idLigado){
                                unset($idLigado);
                                $auxIdLigados = array_values($idLigados);
                                $bandera=false;
                                break;
                            }
                        }
                        if($bandera) {
                            array_push($personasFinal,$persona);
                        }
                    }
                    break;
                }
            }

            $page=$request->get('page');

            if($page==null){
                $page=1;
            }

            return view('obraPersona.index',
                [
                    'obra_persona'=>$obra->id_obra,
                    'personas' => $personasFinal,
                    'personasdelaObra' => $personasdelaObra,
                    "searchText" => $query,
                    "totalRegistros"=> $numeroRegistros,
                    "page"=> $page,
                    'obra' =>$obra,
                ]
            );
        }
    }


    //CUANDO UN persona NO EXISTE EN LA BASE DE DATOS
    //ESTA VISTA MANDA EL FORMULARIO PARA AGREGAR UN NUEVO persona

    public function nuevoObraPersona(Request $request,$id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
         $obra =Obra::findOrFail($id);


         
        return view('obraPersona.createObraPersona',
        [
            'obra'=> $obra,
        ]);

    }

    public function show()
    {
    }

    public function edit()
    {
        //
    }



     //ESTE METODO CREA el persona Y LO LIGA AUTOMATICAMENTE
    //CON ESta obra
    public function update(PersonaFormRequest $request, $id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}

        //ID DELa obra
        $obra =Obra::findOrFail($id);
        //datos del persona
        $persona = new Persona;
        $idPersona = Utilidad::getId("persona","Id_persona");
        $persona->Id_persona=$idPersona;
        $persona->cargo = $request->get('cargo');
        $persona->nombre = $request->get('nombre');
        $persona->apellidos = $request->get('apellidos');
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
        $respuesta = ObraPersona::firstOrCreate([
                    'fk_obra' => $obra->id_obra,
                    'fk_persona' => $idPersona],
                    [
                    'fk_obra' =>  $obra->id_obra,
                    'fk_persona' => $idPersona
                    ]);
        //REGRESA A LA VISTA ORIGINAL DONDE PODRAS VER LOS personas LIGADOS
            $ruta = "/obra_persona/ligar/".$obra->id_obra.$respuesta;
            Session::flash('flash_message3', 'La obra se vinculó al Persona con Id : ('.$idPersona.')');

            //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    1,
                    "obra_persona",
                    "Se agregó el vinculo: ". json_encode($respuesta)
                );

                LogController::agregarLog(
                    1,
                    "persona",
                    "Se agregó el persona: ". json_encode($persona)
                );

            return Redirect::to($ruta);
    }

    //AQUI SE DESVINCULA la obra DEL persona
    public function destroy($id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        //Buscar el vinculo en la tabla obra_ correspondiente
        $aux =DB::table('obra_persona')
        ->where('fk_persona',$id)
        ->where('fk_obra',$obraumento->id_obra)
        ->get();

        //y se elimina
        $aux->delete();

        return Redirect::to("/cntrl_persona/ligar/".$obra->id_obra);
    }

     public function destroy2($id,$id2)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        //Buscar el vinculo en la tabla cntrl correspondiente
        //y se elimina

        $numPersonas=ObraPersona::where('fk_persona', $id)->count();
        $numPersonasDocumento=DocumentoPersona::where('fk_persona',$id)->count();
        //activar el log de la base de datos
        DB::connection()->enableQueryLog();
        if($numPersonas>1 || $numPersonasDocumento>0){
            ObraPersona::where('fk_persona', $id)->where('fk_obra', $id2)->delete();
        }else{
            ObraPersona::where('fk_persona', $id)->where('fk_obra', $id2)->delete();
            Persona::where('Id_persona',$id)->delete();
        }


        Session::flash('flash_message4', ' ¡La persona se desvinculó existosamente!');

                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    3,
                    "obra_persona",
                    "Se eliminó el vinculo: ObraPersona(".$id.") del Doc (".$id2.")"
                );
        return Redirect::to("/obra_persona/ligar/".$id2);
    }
}
