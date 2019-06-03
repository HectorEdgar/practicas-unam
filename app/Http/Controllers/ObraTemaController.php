<?php
namespace sistema\Http\Controllers;

use Illuminate\Http\Request;
use sistema\ObraTema;
use sistema\Tema;
use sistema\Obra;
use sistema\Http\Requests\TemaFormRequest;
use sistema\Http\Requests\ObraTemaFormRequest;
use Illuminate\Support\Facades\Redirect;
use DB;
use Session;
use sistema\Utilidad;
use sistema\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class ObraTemaController extends Controller
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
    public function store(ObraTemaFormRequest $request)
    {
                if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
                $ruta = "/obra_tema/ligar/".$request->get('fk_obra');
                $respuesta = ObraTema::firstOrCreate([
                    'fk_obra' => $request->get('fk_obra'),
                    'fk_tema' => $request->get('fk_tema')],
                    [
                    'fk_obra' => $request->get('fk_obra'),
                    'fk_tema' => $request->get('fk_tema')
                    ]);

                //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    1,
                    "obra_tema",
                    "Se agregó el vinculo: ". json_encode($respuesta)
                );
                Session::flash('flash_message', ' ¡Vinculación Exitosa!');
               // Session::flash('flash_message2', 'Ese vinculo ya existe!'.$respuesta);

                return Redirect::to($ruta);
    }
    public static function eliminarObraCascada($idObra)
    {
        ObraTema::where("fk_obra", $idObra)->delete();
    }

    //AQUI SE MUESTRAN LOS temas QUE SE PUEDEN VINCULAR CON EL DOCUMENTO
    public function ligarObra(Request $request,$id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
         $obra =Obra::findOrFail($id);
         $obraClase =Obra::findOrFail($id);

         if ($request) {
            $query = trim($request->get('searchText'));

            $temasdelaObra =DB::table('obra_tema as ca')
            ->join('temas as a','a.id_tema','=','ca.fk_tema')
            ->where('fk_obra',$obra->id_obra)
            ->orderBy('id_tema', 'desc')
            ->get();

            $idLigados = DB::table('obra_tema as ca')
                ->join('temas as a','a.id_tema','=','ca.fk_tema')
                ->where('fk_obra', $obra->id_obra)
                ->orderBy('id_tema', 'desc')
                ->pluck("a.id_tema")
                ->all();

            $numeroRegistros = DB::table('temas')
                ->where('descripcion', 'LIKE', '%' . $query . '%')
                ->orwhere('id_tema', 'LIKE', '%' . $query . '%')
                ->count() - count($idLigados);


            //Desde este momento se empieza a crear la MAGIA

            $temas="";

            $numeroElementos = 10;
            $auxIdLigados=$idLigados;
            while (true) {
                $aux = $numeroElementos;

                $idTema = DB::table('temas')
               ->where('descripcion', 'LIKE', '%' . $query . '%')
                ->orwhere('id_tema', 'LIKE', '%' . $query . '%')
                ->orderBy('id_tema', 'desc')
                ->paginate($numeroElementos)
                ->pluck("id_tema");


                for ($i=0; $i < count($idTema); $i++) {
                    for ($j=0; $j < count($auxIdLigados) ; $j++) {
                        if ($idTema[$i]== $auxIdLigados[$j]) {
                            $numeroElementos= $numeroElementos + 1;
                            $aux=$aux-1;
                            unset($auxIdLigados[$j]);
                            $auxIdLigados =array_values($auxIdLigados);
                            break;
                        }
                    }
                }

                if($aux>=10 || $numeroRegistros<10) {
                    $temas = DB::table('temas')
                        ->where('descripcion', 'LIKE', '%' . $query . '%')
                        ->orwhere('id_tema', 'LIKE', '%' . $query . '%')
                        ->orderBy('id_tema', 'desc')
                        ->paginate($numeroElementos);


                    $temasFinal=array();

                    foreach ($temas as $tema) {
                        $bandera = true;
                        foreach ($idLigados as $idLigado) {
                            if($tema->id_tema == $idLigado){
                                unset($idLigado);
                                $auxIdLigados = array_values($idLigados);
                                $bandera=false;
                                break;
                            }
                        }
                        if($bandera) {
                            array_push($temasFinal,$tema);
                        }
                    }
                    break;
                }
            }

            $page=$request->get('page');

            if($page==null){
                $page=1;
            }

            return view('obraTema.index',
                [
                    'obra_tema'=>$obra->id_obra,
                    'temas' => $temasFinal,
                    'temasdelaObra' => $temasdelaObra,
                    "searchText" => $query,
                    "totalRegistros"=> $numeroRegistros,
                    "page"=> $page,
                    'obra' =>$obra,
                ]
            );
        }
    }


    //CUANDO UN tema NO EXISTE EN LA BASE DE DATOS
    //ESTA VISTA MANDA EL FORMULARIO PARA AGREGAR UN NUEVO tema

    public function nuevoObraTema(Request $request,$id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
         $obra =Obra::findOrFail($id);

        return view('obraTema.createObraTema',
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



     //ESTE METODO CREA el tema Y LO LIGA AUTOMATICAMENTE
    //CON ESta obra
    public function update(TemaFormRequest $request, $id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}

        //ID DELa obra
        $obra =Obra::findOrFail($id);
        //datos del tema
        $tema = new Tema;
        $idTema = Utilidad::getId("temas","id_tema");

        $tema->id_tema=$idTema;
        $tema->descripcion = $request->get('descripcion');


        DB::connection()->enableQueryLog();
        //SE GUARDA EL tema
        $tema->save();
        //SE CREA LA LIGADURA
        $respuesta = ObraTema::firstOrCreate([
                    'fk_obra' => $obra->id_obra,
                    'fk_tema' => $idTema],
                    [
                    'fk_obra' =>  $obra->id_obra,
                    'fk_tema' => $idTema
                    ]);
        //REGRESA A LA VISTA ORIGINAL DONDE PODRAS VER LOS temas LIGADOS
            $ruta = "/obra_tema/ligar/".$obra->id_obra.$respuesta;
            Session::flash('flash_message3', ' La obra se vinculó al tema con Id : ('.$idTema.')');

            //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    1,
                    "obra_tema",
                    "Se agregó el vinculo: ". json_encode($respuesta)
                );

                LogController::agregarLog(
                    1,
                    "tema",
                    "Se agregó el tema: ". json_encode($tema)
                );

            return Redirect::to($ruta);
    }

    //AQUI SE DESVINCULA la obra DEL tema
    public function destroy($id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        //Buscar el vinculo en la tabla obra_ correspondiente
        $aux =DB::table('obra_tema')
        ->where('fk_tema',$id)
        ->where('fk_obra',$obraumento->id_obra)
        ->get();

        //y se elimina
        $aux->delete();

        return Redirect::to("/obra_tema/ligar/".$obra->id_obra);
    }

     public function destroy2($id,$id2)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        //Buscar el vinculo en la tabla cntrl correspondiente
        //y se elimina
        $borrar =ObraTema::where('fk_tema', $id)->where('fk_obra', $id2)->delete();
        Session::flash('flash_message4', ' ¡El tema se desvinculó Existosamente!');
        //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    3,
                    "obra_tema",
                    "Se eliminó el vinculo: ObraTema(".$id.") del Doc (".$id2.")"
                );
        return Redirect::to("/obra_tema/ligar/".$id2);
    }

}
