<?php

namespace sistema\Http\Controllers;

use Illuminate\Http\Request;
use sistema\ObraEje;
use sistema\Eje;
use sistema\Obra;
use sistema\Http\Requests\EjeFormRequest;
use sistema\Http\Requests\ObraEjeFormRequest;
use Illuminate\Support\Facades\Redirect;
use DB;
use Session;
use sistema\Utilidad;
use sistema\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ObraEjeController extends Controller
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
    public function store(ObraEjeFormRequest $request)
    {
                if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
                $ruta = "/obra_eje/ligar/".$request->get('fk_obra');
                $respuesta = ObraEje::firstOrCreate([
                    'fk_obra' => $request->get('fk_obra'),
                    'fk_eje' => $request->get('fk_eje')],
                    [
                    'fk_obra' => $request->get('fk_obra'),
                    'fk_eje' => $request->get('fk_eje')
                    ]);

                //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    1,
                    "obra_eje",
                    "Se agregó el vínculo: ". json_encode($respuesta)
                );
                Session::flash('flash_message', ' ¡Vinculación exitosa!');
               // Session::flash('flash_message2', 'Ese vinculo ya existe!'.$respuesta);

                return Redirect::to($ruta);
    }
    public static function eliminarObraCascada($idObra){
        $items=ObraEje::where("fk_obra",$idObra)->get();
        foreach ($items as $item) {
            $numItems = ObraEje::where('fk_eje', $item->fk_eje)
                ->count();

            //Log::warning('numItemsDocumentoLigado: ' . $numItems);

            if ($numItems == 1) {
                $itemControl = ObraEje::where('fk_eje', $item->fk_eje)
                    ->where("fk_obra", $idObra)
                    ->first();
            //Log::warning('itemControl: ' . $itemControl);
                $itemIdControl = $itemControl->fk_eje;
                $itemObjeto = Eje::where("Id_eje", $itemIdControl)
                    ->first();
                Log::warning('itemControl: ' . $itemControl);
                Log::warning('itemObjeto: ' . $itemObjeto);
                $itemControl = ObraEje::where('fk_eje', $item->fk_eje)
                    ->where("fk_obra", $idObra)
                    ->delete();
                $itemObjeto->delete();
            }
        }
        ObraEje::where("fk_obra", $idObra)->delete();
    }
    //AQUI SE MUESTRAN LOS ejes QUE SE PUEDEN VINCULAR CON EL DOCUMENTO
    public function ligarObra(Request $request,$id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
         $obra =Obra::findOrFail($id);
         $obraClase =Obra::findOrFail($id);

         if ($request) {
            $query = trim($request->get('searchText'));

            $ejesdelaObra =DB::table('eje_obra as ca')
            ->join('eje as a','a.Id_eje','=','ca.fk_eje')
            ->where('fk_obra',$obra->id_obra)
            ->orderBy('Id_eje', 'desc')
            ->get();

            $idLigados = DB::table('eje_obra as ca')
                ->join('eje as a', 'a.Id_eje', '=', 'ca.fk_eje')
                ->where('fk_obra', $obra->id_obra)
                ->orderBy('Id_eje', 'desc')
                ->pluck("a.Id_eje")
                ->all();

            $numeroRegistros = DB::table('eje')
               ->where('nombre', 'LIKE', '%' . $query . '%')
                ->orwhere('descripcion', 'LIKE', '%' . $query . '%')
                ->orwhere('area', 'LIKE', '%' . $query . '%')
                ->orwhere('poblacion', 'LIKE', '%' . $query . '%')
                ->orwhere('Id_eje', 'LIKE', '%' . $query . '%')
                ->count() - count($idLigados);


            //Desde este momento se empieza a crear la MAGIA
            $ejes="";
            $numeroElementos = 10;
            $auxIdLigados=$idLigados;
            while (true) {
                $aux = $numeroElementos;

                $idEjes = DB::table('eje')
                ->where('nombre', 'LIKE', '%' . $query . '%')
                ->orwhere('descripcion', 'LIKE', '%' . $query . '%')
                ->orwhere('area', 'LIKE', '%' . $query . '%')
                ->orwhere('poblacion', 'LIKE', '%' . $query . '%')
                ->orwhere('Id_eje', 'LIKE', '%' . $query . '%')
                ->orderBy('Id_eje', 'desc')
                ->paginate($numeroElementos)
                ->pluck("Id_eje");


                for ($i=0; $i < count($idEjes); $i++) {
                    for ($j=0; $j < count($auxIdLigados) ; $j++) {
                        if ($idEjes[$i]== $auxIdLigados[$j]) {
                            $numeroElementos= $numeroElementos + 1;
                            $aux=$aux-1;
                            unset($auxIdLigados[$j]);
                            $auxIdLigados =array_values($auxIdLigados);
                            break;
                        }
                    }
                }
                if($aux>=10 || $numeroRegistros<10) {
                    $ejes = DB::table('eje')
                        ->where('nombre', 'LIKE', '%' . $query . '%')
                        ->orwhere('descripcion', 'LIKE', '%' . $query . '%')
                        ->orwhere('area', 'LIKE', '%' . $query . '%')
                        ->orwhere('poblacion', 'LIKE', '%' . $query . '%')
                        ->orwhere('Id_eje', 'LIKE', '%' . $query . '%')
                        ->orderBy('Id_eje', 'desc')
                        ->paginate($numeroElementos);

                    $ejesFinal=array();
                    foreach ($ejes as $eje) {
                        $bandera = true;
                        foreach ($idLigados as $idLigado) {
                            if($eje->Id_eje == $idLigado){
                                unset($idLigado);
                                $auxIdLigados = array_values($idLigados);
                                $bandera=false;
                                break;
                            }
                        }
                        if($bandera) {
                            array_push($ejesFinal,$eje);
                        }
                    }
                    break;
                }
            }

            $page=$request->get('page');

            if($page==null){
                $page=1;
            }

            return view('obraEje.index',
                [
                    'eje_obra'=>$obra->id_obra,
                    'ejes' => $ejesFinal,
                    'ejesdelaObra' => $ejesdelaObra,
                    "searchText" => $query,
                    "totalRegistros"=> $numeroRegistros,
                    "page"=> $page,
                    'obra' =>$obra,
                ]
            );
        }
    }


    //CUANDO UN eje NO EXISTE EN LA BASE DE DATOS
    //ESTA VISTA MANDA EL FORMULARIO PARA AGREGAR UN NUEVO eje

    public function nuevoObraEje(Request $request,$id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
         $obra =Obra::findOrFail($id);

        return view('obraEje.createObraEje',
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



     //ESTE METODO CREA el eje Y LO LIGA AUTOMATICAMENTE
    //CON ESta obra
    public function update(EjeFormRequest $request, $id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}

        //ID DELa obra
        $obra =Obra::findOrFail($id);
        //datos del eje
        $eje = new Eje;
        $idEje = Utilidad::getId("eje","Id_eje");
        $eje->Id_eje=$idEje;

        if($request->get('nombre')==null || $request->get('descripcion') ==null
             || $request->get('area')==null || $request->get('poblacion')==null){
                Session::flash('flash_message2', 'Verifica los campos!');
                return Redirect::to("/obra_eje/nuevoObra/".$obra->id_obra);
             }
        else{
        $eje->nombre = $request->get('nombre');
        $eje->descripcion = $request->get('descripcion');
        $eje->area = $request->get('area');
        $eje->poblacion = $request->get('poblacion');


        DB::connection()->enableQueryLog();
        //SE GUARDA EL eje
        $eje->save();
        //SE CREA LA LIGADURA
        $respuesta = ObraEje::firstOrCreate([
                    'fk_obra' => $obra->id_obra,
                    'fk_eje' => $idEje],
                    [
                    'fk_obra' =>  $obra->id_obra,
                    'fk_eje' => $idEje
                    ]);
        //REGRESA A LA VISTA ORIGINAL DONDE PODRAS VER LOS ejes LIGADOS
            $ruta = "/obra_eje/ligar/".$obra->id_obra.$respuesta;
            Session::flash('flash_message3', 'La obra se vinculó al eje con Id : ('.$idEje.')');

            //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    1,
                    "eje_obra",
                    "Se agregó el vínculo: ". json_encode($respuesta)
                );

                LogController::agregarLog(
                    1,
                    "eje",
                    "Se agregó el eje: ". json_encode($eje)
                );

            return Redirect::to($ruta);
        }
    }

    //AQUI SE DESVINCULA la obra DEL eje
    public function destroy($id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        //Buscar el vinculo en la tabla obra_ correspondiente
        $aux =DB::table('eje_obra')
        ->where('fk_eje',$id)
        ->where('fk_obra',$obraumento->id_obra)
        ->get();

        //y se elimina
        $aux->delete();

        return Redirect::to("/cntrl_eje/ligar/".$obra->id_obra);
    }

     public function destroy2($id,$id2)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        //Buscar el vinculo en la tabla cntrl correspondiente
        //y se elimina
        $borrar =ObraEje::where('fk_eje', $id)->where('fk_obra', $id2)->delete();
        Session::flash('flash_message4', ' ¡El eje se desvinculó existosamente!');
        //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    3,
                    "eje_obra",
                    "Se eliminó el vínculo: ObraEje(".$id.") del Doc (".$id2.")"
                );
        return Redirect::to("/obra_eje/ligar/".$id2);
    }
}
