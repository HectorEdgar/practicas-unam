<?php

namespace sistema\Http\Controllers;

use Illuminate\Http\Request;
use sistema\LugarEtnia;
use sistema\Lugar;
use sistema\Etnia;
use sistema\Http\Requests\LugarFormRequest;
use sistema\Http\Requests\LugarEtniaFormRequest;
use sistema\Http\Requests\StoreMultiple2FormRequest;
use Illuminate\Support\Facades\Redirect;
use DB;
use Session;
use sistema\Utilidad;
use sistema\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use sistema\DocumentoLugar;
use sistema\ObraLugar;

class LugarEtniaController extends Controller
{
     public function index(Request $request)
    {
    }
    public function create()
    {
    }
    /*
    private function validarRoles(){
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin', 'revisor'])) {return Redirect::to('/');}

    }
    */
    public function store(LugarEtniaFormRequest $request)
    {
                if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
                $ruta = "/lugar_etnia/ligar/".$request->get('fk_lugar');
                $respuesta = LugarEtnia::firstOrCreate([
                    'fk_lugar' => $request->get('fk_lugar'),
                    'fk_etnia' => $request->get('fk_etnia'),
                    'latitud'=> "",
                    'longitud'=> ""
                    ],
                    [
                    'fk_lugar' => $request->get('fk_lugar'),
                    'fk_etnia' => $request->get('fk_etnia'),
                    'latitud'=> "",
                    'longitud'=> ""
                    ]);

                //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    1,
                    "lugar_etnia",
                    "Se agregó el vínculo: ". json_encode($respuesta)
                );
                Session::flash('flash_message', '¡Vinculación exitosa!');
               // Session::flash('flash_message2', 'Ese vinculo ya existe!'.$respuesta);

                return Redirect::to($ruta);
    }

    //AQUI SE MUESTRAN LOS lugars QUE SE PUEDEN VINCULAR CON EL DOCUMENTO
    public function ligarLugar(Request $request,$id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
         $lugar =Lugar::findOrFail($id);
         $lugarClase =Lugar::findOrFail($id);

         if ($request) {
            $query = trim($request->get('searchText'));

            $etniadelLugar =DB::table('cntrl_etnia as ca')
            ->join('etnia as a','a.id_etnia','=','ca.fk_etnia')
            ->where('fk_lugar',$lugar->id_lugar)
            ->orderBy('id_etnia', 'desc')
            ->get();

            $idLigados = DB::table('cntrl_etnia as ca')
            ->join('etnia as a','a.id_etnia','=','ca.fk_etnia')
                ->where('fk_lugar', $lugar->id_lugar)
                ->orderBy('id_etnia', 'desc')
                ->pluck("a.id_etnia")
                ->all();

            $numeroRegistros = DB::table('etnia')
               ->where('nombre', 'LIKE', '%' . $query . '%')
                ->orwhere('nombre2', 'LIKE', '%' . $query . '%')
                ->orwhere('territorio', 'LIKE', '%' . $query . '%')
                ->orwhere('familia', 'LIKE', '%' . $query . '%')
                ->orwhere('id_etnia', 'LIKE', '%' . $query . '%')
                ->count() - count($idLigados);


            //Desde este momento se empieza a crear la MAGIA
            $etnia="";
            $numeroElementos = 10;
            $auxIdLigados=$idLigados;
            while (true) {
                $aux = $numeroElementos;

                $idEtnias = DB::table('etnia')
                ->where('nombre', 'LIKE', '%' . $query . '%')
                ->orwhere('nombre2', 'LIKE', '%' . $query . '%')
                ->orwhere('territorio', 'LIKE', '%' . $query . '%')
                ->orwhere('familia', 'LIKE', '%' . $query . '%')
                ->orwhere('id_etnia', 'LIKE', '%' . $query . '%')
                ->orderBy('id_etnia', 'desc')
                ->paginate($numeroElementos)
                ->pluck("id_etnia");


                for ($i=0; $i < count($idEtnias); $i++) {
                    for ($j=0; $j < count($auxIdLigados) ; $j++) {
                        if ($idEtnias[$i]== $auxIdLigados[$j]) {
                            $numeroElementos= $numeroElementos + 1;
                            $aux=$aux-1;
                            unset($auxIdLigados[$j]);
                            $auxIdLigados =array_values($auxIdLigados);
                            break;
                        }
                    }
                }
                if($aux>=10 || $numeroRegistros<10) {
                    $etnia = DB::table('etnia')
                        ->where('nombre', 'LIKE', '%' . $query . '%')
                        ->orwhere('nombre2', 'LIKE', '%' . $query . '%')
                        ->orwhere('territorio', 'LIKE', '%' . $query . '%')
                        ->orwhere('familia', 'LIKE', '%' . $query . '%')
                        ->orwhere('id_etnia', 'LIKE', '%' . $query . '%')
                        ->orderBy('id_etnia', 'desc')
                        ->paginate($numeroElementos);

                    $etniaFinal=array();
                    foreach ($etnia as $item) {
                        $bandera = true;
                        foreach ($idLigados as $idLigado) {
                            if($item->id_etnia == $idLigado){
                                unset($idLigado);
                                $auxIdLigados = array_values($idLigados);
                                $bandera=false;
                                break;
                            }
                        }
                        if($bandera) {
                            array_push($etniaFinal,$item);
                        }
                    }
                    break;
                }
            }

            $page=$request->get('page');

            if($page==null){
                $page=1;
            }

            return view('lugarEtnia.index',
                [
                    'lugar_etnia'=>$lugar->id_lugar,
                    'etnias' => $etniaFinal,
                    'etniasdelLugar' => $etniadelLugar,
                    "searchText" => $query,
                    "totalRegistros"=> $numeroRegistros,
                    "page"=> $page,
                    'lugar' =>$lugar,
                ]
            );
        }
    }

    public function nuevoLugarEtnia(Request $request,$id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
         $lugar =Lugar::findOrFail($id);

        return view('lugarEtnia.createLugarEtnia',
        [
            'lugar'=> $lugar

        ]);

    }

    public function editar($id,$id2)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        $lugar =Lugar::findOrFail($id);
        $etnia =Etnia::findOrFail($id2);

        $vinculo = LugarEtnia::where('fk_etnia', $id2)->where('fk_lugar', $id)->first();

        return view('lugarEtnia.editar',
        [
            'lugar'=> $lugar,
            "etnia" => $etnia,
            'vinculo'=>$vinculo

        ]);
    }


    public function editarVinculo(StoreMultiple2FormRequest $request)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}

        //ID DEL LUGAR
        $lugar =Lugar::findOrFail($request->get('fk_lugar'));
        //datos del lugar
        $etnia = Etnia::findOrFail($request->get('fk_etnia'));

        $etnia->nombre = $request->get('nombre')?  $request->get('nombre'):'';
        $etnia->nombre2= $request->get('nombre2')?$request->get('nombre2'):'';
        $etnia->territorio = $request->get('territorio')?$request->get('territorio'):'';
        $etnia->familia = $request->get('familia')?$request->get('familia'):'';

        $vinculo = DB::table('cntrl_etnia')
        ->where('fk_etnia', $request->get('fk_etnia'))
        ->where('fk_lugar',$request->get('fk_lugar'))
        ->update(['latitud' => $request->get('latitud'),
                  'longitud'=>$request->get('longitud')]);

        DB::connection()->enableQueryLog();
        //SE GUARDA LA ETNIA
        $etnia->update();
        //SE guarda los datos de vinculo
        //$vinculo->update();


        //REGRESA A LA VISTA ORIGINAL DONDE PODRAS VER LOS lugars LIGADOS
            $ruta = "/lugar_etnia/ligar/".$lugar->id_lugar;
            Session::flash('flash_message3', ' ¡El vínculo se modificó exitosamente!');

            //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    1,
                    "etnia",
                    "Se modificó la etnia: ". json_encode($lugar)
                );

                LogController::agregarLog(
                    1,
                    "lugar_etnia",
                    "Se modificó el vínculo: ". json_encode($vinculo)
                );

            return Redirect::to($ruta);

    }




    public function vincular($id,$id2)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        $lugar =Lugar::findOrFail($id);
        $etnia =Etnia::findOrFail($id2);


        return view('lugarEtnia.vincular',
        [
            'lugar'=> $lugar,
            'etnia'=>$etnia,

        ]);

    }


    public function show()
    {
    }

    public function edit()
    {
        //
    }


    //StoreMultipleFormRequest es una clase compuesta de varios form request
    //en este caso tiene incluido el de la tabla cntrl_etnia y el de etnia a la vez
    public function update(StoreMultiple2FormRequest $request, $id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}

        $lugar =Lugar::findOrFail($id);
        //datos de la etnia
        $etnia = new Etnia;
        $idEtnia = Utilidad::getId("etnia","id_etnia");
        $etnia->id_etnia=$idEtnia;
        $etnia->nombre = $request->get('nombre')?$request->get('nombre'):'';
        $etnia->nombre2= $request->get('nombre2')?$request->get('nombre2'):'';
        $etnia->territorio = $request->get('territorio')?$request->get('territorio'):'';
        $etnia->familia = $request->get('familia')?$request->get('familia'):'';

        DB::connection()->enableQueryLog();
        //SE GUARDA EL lugar
        $etnia->save();
        //SE CREA LA LIGADURA
        $respuesta = LugarEtnia::firstOrCreate([
                    'fk_lugar' => $lugar->id_lugar,
                    'fk_etnia' => $idEtnia,
                    'latitud'=> $request->get('latitud'),
                    'longitud'=>$request->get('longitud')
                    ],
                    [
                    'fk_lugar' => $lugar->id_lugar,
                    'fk_etnia' => $idEtnia,
                    'latitud'=> $request->get('latitud'),
                    'longitud'=>$request->get('longitud')
                    ]);
        //REGRESA A LA VISTA ORIGINAL DONDE PODRAS VER LOS lugars LIGADOS
            $ruta = "/lugar_etnia/ligar/".$lugar->id_lugar;
            Session::flash('flash_message3', 'El lugar se vinculó con la Etnia con Id : ('.$idEtnia.')');

            //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    1,
                    "lugar_etnia",
                    "Se agregó el vínculo: ". json_encode($respuesta)
                );

                LogController::agregarLog(
                    1,
                    "etnia",
                    "Se agregó la etnia: ". json_encode($lugar)
                );

            return Redirect::to($ruta);

    }

    public function destroy($id)
    {
      //nada
    }

     public function destroy2($id,$id2)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        //Buscar el vinculo en la tabla cntrl correspondiente
        //y se elimina
        $numEtnia =LugarEtnia::where('fk_etnia', $id)->count();
        $numLugar=DocumentoLugar::where('fk_lugar', $id)->count();
        $numLugarObra=ObraLugar::where('fk_lugar',$id)->count();

        if($numEtnia>1 || $numLugar>0 || $numLugarObra>0){
            LugarEtnia::where('fk_etnia', $id)->where('fk_lugar', $id2)->delete();
        }else{
            LugarEtnia::where('fk_etnia', $id)->where('fk_lugar', $id2)->delete();
            Etnia::where('id_etnia',$id)->delete();
        }

        Session::flash('flash_message4', ' ¡La etnia se desvinculó existosamente!');
        //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    3,
                    "lugar_etnia",
                    "Se eliminó el vínculo: LugarEtnia(".$id.") del Lugar (".$id2.")"
                );
        return Redirect::to("/lugar_etnia/ligar/".$id2);
    }
}
