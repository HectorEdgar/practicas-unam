<?php

namespace sistema\Http\Controllers;

use Illuminate\Http\Request;
use sistema\ObraInstitucion;
use sistema\Institucion;
use sistema\Obra;
use sistema\Http\Requests\InstitucionFormRequest;
use sistema\Http\Requests\ObraInstitucionFormRequest;
use Illuminate\Support\Facades\Redirect;
use DB;
use Session;
use sistema\Utilidad;
use sistema\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use sistema\DocumentoInstitucion;

class ObraInstitucionController extends Controller
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
    public function store(ObraInstitucionFormRequest $request)
    {
                if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
                $ruta = "/obra_institucion/ligar/".$request->get('fk_obra');
                $respuesta = ObraInstitucion::firstOrCreate([
                    'fk_obra' => $request->get('fk_obra'),
                    'fk_inst' => $request->get('fk_inst'),
                    'extra' => $request->input('relacion')],
                    [
                    'fk_obra' => $request->get('fk_obra'),
                    'fk_inst' => $request->get('fk_inst'),
                    'extra'=> $request->input('relacion')
                    ]);

                //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    1,
                    "obra_institucion",
                    "Se agregó el vínculo: ". json_encode($respuesta)
                );
                Session::flash('flash_message', ' ¡Vinculación exitosa!');
               // Session::flash('flash_message2', 'Ese vinculo ya existe!'.$respuesta);

                return Redirect::to($ruta);
    }
    public static function eliminarObraCascada($idObra)
    {
        $items = ObraInstitucion::where("fk_obra", $idObra)->get();
        foreach ($items as $item) {
            $numItems = ObraInstitucion::where('fk_inst', $item->fk_inst)
                ->count();

            //Log::warning('numItemsDocumentoLigado: ' . $numItems);

            if ($numItems == 1) {
                $itemControl = ObraInstitucion::where('fk_inst', $item->fk_inst)
                    ->where("fk_obra", $idObra)
                    ->first();
            //Log::warning('itemControl: ' . $itemControl);
                $itemIdControl = $itemControl->fk_inst;
                $itemObjeto = Institucion::where("Id_institucion", $itemIdControl)
                    ->first();
                Log::warning('itemControl: ' . $itemControl);
                Log::warning('itemObjeto: ' . $itemObjeto);
                $itemControl = ObraInstitucion::where('fk_inst', $item->fk_inst)
                    ->where("fk_obra", $idObra)
                    ->delete();

                $itemObjeto->delete();
            }
        }
        ObraInstitucion::where("fk_obra", $idObra)->delete();
    }
    //AQUI SE MUESTRAN LOS institucions QUE SE PUEDEN VINCULAR CON EL DOCUMENTO
    public function ligarObra(Request $request,$id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
         $obra =Obra::findOrFail($id);
         $obraClase =Obra::findOrFail($id);

         if ($request) {
            $query = trim($request->get('searchText'));

            $institucionsdelaObra =DB::table('obra_inst as ca')
            ->join('institucion as a','a.Id_institucion','=','ca.fk_inst')
            ->where('fk_obra',$obra->id_obra)
            ->select('ca.extra as relacion','a.Id_institucion as Id_institucion','a.nombre as nombre',
            'a.siglas as siglas','a.pais as pais','a.localidad as localidad')
            ->orderBy('Id_institucion', 'desc')
            ->get();

            $idLigados = DB::table('obra_inst as ca')
                ->join('institucion as a', 'a.Id_institucion', '=', 'ca.fk_inst')
                ->where('fk_obra', $obra->id_obra)
                ->orderBy('Id_institucion', 'desc')
                ->pluck("a.Id_institucion")
                ->all();

            $numeroRegistros = DB::table('institucion')
               ->where('nombre', 'LIKE', '%' . $query . '%')
                ->orwhere('siglas','LIKE','%' .$query . '%')
                ->orwhere('Id_institucion','LIKE','%' .$query . '%')
                ->count() - count($idLigados);


            //Desde este momento se empieza a crear la MAGIA
            $institucions="";
            $numeroElementos = 10;
            $auxIdLigados=$idLigados;
            while (true) {
                $aux = $numeroElementos;

                $idInstitucions = DB::table('institucion')
               ->where('nombre', 'LIKE', '%' . $query . '%')
                ->orwhere('siglas','LIKE','%' .$query . '%')
                ->orderBy('nombre', 'asc')
                ->paginate($numeroElementos)
                ->pluck("Id_institucion");


                for ($i=0; $i < count($idInstitucions); $i++) {
                    for ($j=0; $j < count($auxIdLigados) ; $j++) {
                        if ($idInstitucions[$i]== $auxIdLigados[$j]) {
                            $numeroElementos= $numeroElementos + 1;
                            $aux=$aux-1;
                            unset($auxIdLigados[$j]);
                            $auxIdLigados =array_values($auxIdLigados);
                            break;
                        }
                    }
                }
                if($aux>=10 || $numeroRegistros<10) {
                    $institucions = DB::table('institucion')
                        ->where('nombre', 'LIKE', '%' . $query . '%')
                        ->orwhere('siglas','LIKE','%' .$query . '%')
                        ->orderBy('nombre', 'asc')
                        ->paginate($numeroElementos);

                    $institucionsFinal=array();
                    foreach ($institucions as $institucion) {
                        $bandera = true;
                        foreach ($idLigados as $idLigado) {
                            if($institucion->Id_institucion == $idLigado){
                                unset($idLigado);
                                $auxIdLigados = array_values($idLigados);
                                $bandera=false;
                                break;
                            }
                        }
                        if($bandera) {
                            array_push($institucionsFinal,$institucion);
                        }
                    }
                    break;
                }
            }

            $page=$request->get('page');

            if($page==null){
                $page=1;
            }
            $paises= DB::table('paises')->orderBy('nombre', 'asc')->get();

            return view('obraInstitucion.index',
                [
                    'institucion_obra'=>$obra->id_obra,
                    'institucions' => $institucionsFinal,
                    'paises'=>$paises,
                    'institucionsdelaObra' => $institucionsdelaObra,
                    "searchText" => $query,
                    "totalRegistros"=> $numeroRegistros,
                    "page"=> $page,
                    'obra' =>$obra,
                ]
            );
        }
    }


    //CUANDO UN institucion NO EXISTE EN LA BASE DE DATOS
    //ESTA VISTA MANDA EL FORMULARIO PARA AGREGAR UN NUEVO institucion

    public function nuevoObraInstitucion(Request $request,$id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
         $obra =Obra::findOrFail($id);
        $paises= DB::table('paises')->orderBy('nombre', 'asc')->get();

        return view('obraInstitucion.createObraInstitucion',
        [
            'paises'=>$paises,
            'obra'=> $obra
        ]);

    }

    public function show()
    {
    }

    public function edit()
    {
        //
    }



     //ESTE METODO CREA el institucion Y LO LIGA AUTOMATICAMENTE
    //CON ESta obra
    public function update(InstitucionFormRequest $request, $id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}

        //ID DELa obra
        $obra =Obra::findOrFail($id);
        //datos del institucion
        $institucion = new Institucion;
        $idInstitucion = Utilidad::getId("institucion","Id_institucion");
        $institucion->Id_institucion=$idInstitucion;
        $institucion->nombre= $request->get('nombre');
		$institucion->siglas= $request->get('siglas');
 		$institucion->pais= $request->input('pais');
 		$institucion->localidad= $request->get('localidad');
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
        $respuesta = ObraInstitucion::firstOrCreate([
                    'fk_obra' => $obra->id_obra,
                    'fk_inst' => $idInstitucion,
                    'extra'=> $request->get('relacion')
                    ],
                    [
                    'fk_obra' =>  $obra->id_obra,
                    'fk_inst' => $idInstitucion,
                    'extra'=> $request->get('relacion')
                    ]);
        //REGRESA A LA VISTA ORIGINAL DONDE PODRAS VER LOS institucions LIGADOS
            $ruta = "/obra_institucion/ligar/".$obra->id_obra.$respuesta;
            Session::flash('flash_message3', 'La obra se vinculó al institución con Id : ('.$idInstitucion.')');

            //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    1,
                    "institucion_obra",
                    "Se agregó el vínculo: ". json_encode($respuesta)
                );

                LogController::agregarLog(
                    1,
                    "institucion",
                    "Se agregó la institución: ". json_encode($institucion)
                );

            return Redirect::to($ruta);
    }

    //AQUI SE DESVINCULA la obra DEL institucion
    public function destroy($id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        //Buscar el vinculo en la tabla obra_ correspondiente
        $aux =DB::table('institucion_obra')
        ->where('fk_inst',$id)
        ->where('fk_obra',$obra->id_obra)
        ->get();

        //y se elimina
        $aux->delete();

        return Redirect::to("/cntrl_institucion/ligar/".$obra->id_obra);
    }

     public function destroy2($id,$id2)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        //Buscar el vinculo en la tabla cntrl correspondiente
        //y se elimina

        $numInstituciones=ObraInstitucion::where('fk_inst', $id)->count();
        $numInstitucionesDocumento=DocumentoInstitucion::where('fk_instit',$id)->count();
        if($numInstituciones>1 || $numInstitucionesDocumento>0){
            ObraInstitucion::where('fk_inst', $id)->where('fk_obra', $id2)->delete();
        }else{
            ObraInstitucion::where('fk_inst', $id)->where('fk_obra', $id2)->delete();
            Institucion::where('Id_institucion',$id)->delete();
        }


        Session::flash('flash_message4', 'El institucion se desvinculó Existosamente!');
        //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    3,
                    "institucion_obra",
                    "Se eliminó el vinculo: ObraInstitución(".$id.") del Doc (".$id2.")"
                );
        return Redirect::to("/obra_institucion/ligar/".$id2);
    }

    //MODIFICAR LA RELACION DIRECTA O INDIRECTA
     public function destroy3($id,$id2)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        //Buscar el vinculo en la tabla cntrl correspondiente
        //y se elimina

        $vin =DB::table('obra_inst')
        ->where('fk_inst', $id)
        ->where('fk_obra',$id2)->first();

        $relacion = $vin->extra;
        $relacionCambiada=0;
        if($relacion==1){
            $relacionCambiada=2;
        }
        if($relacion==2){
            $relacionCambiada=1;
        }

         $vinculo = DB::table('obra_inst')
        ->where('fk_inst', $id)
        ->where('fk_obra',$id2)
        ->update(['extra' => $relacionCambiada]);

        Session::flash('flash_message4', ' ¡Se a modificado la relación de institución existosamente!');
        //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    3,
                    "institucion_obra",
                    "Se modificó la relación: Institución(".$id.") de la obra (".$id2.")"
                );
        return Redirect::to("/obra_institucion/ligar/".$id2);
    }
}
