<?php
namespace sistema\Http\Controllers;

use Illuminate\Http\Request;
use sistema\ObraProyecto;
use sistema\Proyecto;
use sistema\Obra;
use sistema\Http\Requests\ProyectoFormRequest;
use sistema\Http\Requests\ObraProyectoFormRequest;
use Illuminate\Support\Facades\Redirect;
use DB;
use Session;
use sistema\Utilidad;
use sistema\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class ObraProyectoController extends Controller
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
    public function store(ObraProyectoFormRequest $request)
    {
                if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
                $ruta = "/obra_proyecto/ligar/".$request->get('fk_obra');
                $respuesta = ObraProyecto::firstOrCreate([
                    'fk_obra' => $request->get('fk_obra'),
                    'fk_proyec' => $request->get('fk_proyec')],
                    [
                    'fk_obra' => $request->get('fk_obra'),
                    'fk_proyec' => $request->get('fk_proyec')
                    ]);

                //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    1,
                    "obra_proyecto",
                    "Se agregó el vinculo: ". json_encode($respuesta)
                );
                Session::flash('flash_message', ' ¡Vinculación exitosa!');
               // Session::flash('flash_message2', 'Ese vinculo ya existe!'.$respuesta);

                return Redirect::to($ruta);
    }
    public static function eliminarObraCascada($idObra)
    {
        ObraProyecto::where("fk_obra", $idObra)->delete();
    }
    //AQUI SE MUESTRAN LOS proyectos QUE SE PUEDEN VINCULAR CON EL DOCUMENTO
    public function ligarObra(Request $request,$id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
         $obra =Obra::findOrFail($id);
         $obraClase =Obra::findOrFail($id);

         if ($request) {
            $query = trim($request->get('searchText'));

            $proyectosdelaObra =DB::table('obra_proyec as ca')
            ->join('catalogo_proyecto as a','a.id_proyecto','=','ca.fk_proyec')
            ->where('fk_obra',$obra->id_obra)
            ->orderBy('id_proyecto', 'desc')
            ->get();

            $idLigados = DB::table('obra_proyec as ca')
                ->join('catalogo_proyecto as a', 'a.id_proyecto', '=', 'ca.fk_proyec')
                ->where('fk_obra', $obra->id_obra)
                ->orderBy('id_proyecto', 'desc')
                ->pluck("a.id_proyecto")
                ->all();

            $numeroRegistros = DB::table('catalogo_proyecto')
               ->where('proyecto', 'LIKE', '%' . $query . '%')
                ->orwhere('id_proyecto', 'LIKE', '%' . $query . '%')
                ->count() - count($idLigados);


            //Desde este momento se empieza a crear la MAGIA

            $proyectos="";

            $numeroElementos = 10;
            $auxIdLigados=$idLigados;
            while (true) {
                $aux = $numeroElementos;

                $idProyecto = DB::table('catalogo_proyecto')
                ->where('proyecto', 'LIKE', '%' . $query . '%')
                ->orwhere('id_proyecto', 'LIKE', '%' . $query . '%')
                ->orderBy('id_proyecto', 'desc')
                ->paginate($numeroElementos)
                ->pluck("id_proyecto");


                for ($i=0; $i < count($idProyecto); $i++) {
                    for ($j=0; $j < count($auxIdLigados) ; $j++) {
                        if ($idProyecto[$i]== $auxIdLigados[$j]) {
                            $numeroElementos= $numeroElementos + 1;
                            $aux=$aux-1;
                            unset($auxIdLigados[$j]);
                            $auxIdLigados =array_values($auxIdLigados);
                            break;
                        }
                    }
                }

                if($aux>=10 || $numeroRegistros<10) {
                    $proyectos = DB::table('catalogo_proyecto')
                        ->where('proyecto', 'LIKE', '%' . $query . '%')
                        ->orwhere('id_proyecto', 'LIKE', '%' . $query . '%')
                        ->orderBy('id_proyecto', 'desc')
                        ->paginate($numeroElementos);


                    $proyectosFinal=array();

                    foreach ($proyectos as $proyecto) {
                        $bandera = true;
                        foreach ($idLigados as $idLigado) {
                            if($proyecto->id_proyecto == $idLigado){
                                unset($idLigado);
                                $auxIdLigados = array_values($idLigados);
                                $bandera=false;
                                break;
                            }
                        }
                        if($bandera) {
                            array_push($proyectosFinal,$proyecto);
                        }
                    }
                    break;
                }
            }

            $page=$request->get('page');

            if($page==null){
                $page=1;
            }

            return view('obraProyecto.index',
                [
                    'proyecto_obra'=>$obra->id_obra,
                    'proyectos' => $proyectosFinal,
                    'proyectosdelaObra' => $proyectosdelaObra,
                    "searchText" => $query,
                    "totalRegistros"=> $numeroRegistros,
                    "page"=> $page,
                    'obra' =>$obra,
                ]
            );
        }
    }


    //CUANDO UN proyecto NO EXISTE EN LA BASE DE DATOS
    //ESTA VISTA MANDA EL FORMULARIO PARA AGREGAR UN NUEVO proyecto

    public function nuevoObraProyecto(Request $request,$id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
         $obra =Obra::findOrFail($id);

        return view('obraProyecto.createObraProyecto',
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



     //ESTE METODO CREA el proyecto Y LO LIGA AUTOMATICAMENTE
    //CON ESta obra
    public function update(ProyectoFormRequest $request, $id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}

        //ID DELa obra
        $obra =Obra::findOrFail($id);
        //datos del proyecto
        $proyecto = new Proyecto;
        $idProyecto = Utilidad::getId("catalogo_proyecto","id_proyecto");

        $proyecto->id_proyecto=$idProyecto;
        $proyecto->proyecto = $request->get('proyecto');


        DB::connection()->enableQueryLog();
        //SE GUARDA EL proyecto
        $proyecto->save();
        //SE CREA LA LIGADURA
        $respuesta = ObraProyecto::firstOrCreate([
                    'fk_obra' => $obra->id_obra,
                    'fk_proyec' => $idProyecto],
                    [
                    'fk_obra' =>  $obra->id_obra,
                    'fk_proyec' => $idProyecto
                    ]);
        //REGRESA A LA VISTA ORIGINAL DONDE PODRAS VER LOS proyectos LIGADOS
            $ruta = "/obra_proyecto/ligar/".$obra->id_obra.$respuesta;
            Session::flash('flash_message3', 'La obra se vinculó al proyecto con Id : ('.$idProyecto.')');

            //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    1,
                    "proyecto_obra",
                    "Se agregó el vinculo: ". json_encode($respuesta)
                );

                LogController::agregarLog(
                    1,
                    "proyecto",
                    "Se agregó el proyecto: ". json_encode($proyecto)
                );

            return Redirect::to($ruta);
    }

    //AQUI SE DESVINCULA la obra DEL proyecto
    public function destroy($id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        //Buscar el vinculo en la tabla obra_ correspondiente
        $aux =DB::table('obra_proyec')
        ->where('fk_proyec',$id)
        ->where('fk_obra',$obraumento->id_obra)
        ->get();

        //y se elimina
        $aux->delete();

        return Redirect::to("/obra_proyecto/ligar/".$obra->id_obra);
    }

     public function destroy2($id,$id2)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        //Buscar el vinculo en la tabla cntrl correspondiente
        //y se elimina
        $borrar =ObraProyecto::where('fk_proyec', $id)->where('fk_obra', $id2)->delete();
        Session::flash('flash_message4', '¡El proyecto se desvinculó existosamente!');
        //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    3,
                    "proyecto_obra",
                    "Se eliminó el vinculo: ObraProyecto(".$id.") del Doc (".$id2.")"
                );
        return Redirect::to("/obra_proyecto/ligar/".$id2);
    }

}
