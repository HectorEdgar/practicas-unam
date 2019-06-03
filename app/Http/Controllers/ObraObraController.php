<?php

namespace sistema\Http\Controllers;

use Illuminate\Http\Request;
use sistema\ObraObra;
use sistema\Obra;
use sistema\Http\Requests\ObraObraFormRequest;
use sistema\Http\Requests\ObraFormRequest;

use Illuminate\Support\Facades\Redirect;
use DB;
use Session;
use sistema\Utilidad;
use sistema\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ObraObraController extends Controller
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
    public function store(ObraObraFormRequest $request)
    {
                if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
                $ruta = "/obra_obra/ligar/".$request->get('fk_obra');

                if($request->get('fk_obra') !=$request->get('fk_obra2')){

                    $respuesta = ObraObra::firstOrCreate([
                        'fk_obra' => $request->get('fk_obra'),
                        'fk_obra2' => $request->get('fk_obra2')],
                        [
                        'fk_obra' => $request->get('fk_obra'),
                        'fk_obra2' => $request->get('fk_obra2')
                        ]);
    
                    //activar el log de la base de datos
                    DB::connection()->enableQueryLog();
                    //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                    LogController::agregarLog(
                        1,
                        "obra_obra",
                        "Se agregó el vinculo: ". json_encode($respuesta)
                    );
                    Session::flash('flash_message', '¡Vinculación exitosa!');
                   // Session::flash('flash_message2', 'Ese vinculo ya existe!'.$respuesta);


                }else{
                    Session::flash('flash_message2', 'No se puede vincular la obra con ella misma.');


                }
              

                return Redirect::to($ruta);
    }
    public static function eliminarObraCascada($idObra)
    {
        ObraObra::where("fk_obra", $idObra)->delete();
    }
    //AQUI SE MUESTRAN LAS OBRAS QUE SE PUEDEN VINCULAR CON EL DOCUMENTO
    public function ligarObra(Request $request,$id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
         $obraBase =Obra::findOrFail($id);
         $obraClase =Obra::findOrFail($id);

         if ($request) {
            $query = trim($request->get('searchText'));

            $obrasdelaObraAux =DB::table('obra_obra as ca')
            ->join('obras as a','a.id_obra','=','ca.fk_obra2')
            ->where('fk_obra',$obraBase->id_obra);

            $obrasdelaObra =DB::table('obra_obra as ca')
            ->join('obras as a','a.id_obra','=','ca.fk_obra')
            ->where('fk_obra2',$obraBase->id_obra)
            ->union($obrasdelaObraAux)// se realiza una unión con obrasdelaobraAux 
            ->orderBy('id_obra', 'desc')
            ->get();

            $idLigadosAux = DB::table('obra_obra as ca')
                ->join('obras as a', 'a.id_obra', '=', 'ca.fk_obra2')
                ->where('fk_obra', $obraBase->id_obra)
                ->orderBy('id_obra', 'desc')
                ->pluck("a.id_obra");
               
                

            $idLigados = DB::table('obra_obra as ca')
                ->join('obras as a', 'a.id_obra', '=', 'ca.fk_obra')
                ->where('fk_obra2', $obraBase->id_obra)
                ->orderBy('id_obra', 'desc')
                ->pluck("a.id_obra")
                ->union($idLigadosAux)// se realiza una unión con idLigadosAux 
                ->all();



            $numeroRegistros = DB::table('obras')
                ->where('nombre', 'LIKE', '%' . $query . '%')
                ->orwhere('extra', 'LIKE', '%' . $query . '%')
                ->orwhere('revisado', 'LIKE', '%' . $query . '%')
                ->orwhere('id_obra', 'LIKE', '%' . $query . '%')
                ->count() - count($idLigados);


            //Desde este momento se empieza a crear la MAGIA
            $obras="";
            $numeroElementos = 10;
            $auxIdLigados=$idLigados;
            while (true) {
                $aux = $numeroElementos;

                $idObras = DB::table('obras')
                ->where('nombre', 'LIKE', '%' . $query . '%')
                ->orwhere('extra', 'LIKE', '%' . $query . '%')
                ->orwhere('revisado', 'LIKE', '%' . $query . '%')
                ->orwhere('id_obra', 'LIKE', '%' . $query . '%')
                ->orderBy('nombre', 'asc')
                ->paginate($numeroElementos)
                ->pluck("id_obra");


                for ($i=0; $i < count($idObras); $i++) {
                    for ($j=0; $j < count($auxIdLigados) ; $j++) {
                        if ($idObras[$i]== $auxIdLigados[$j]) {
                            $numeroElementos= $numeroElementos + 1;
                            $aux=$aux-1;
                            unset($auxIdLigados[$j]);
                            $auxIdLigados =array_values($auxIdLigados);
                            break;
                        }
                    }
                }
                if($aux>=10 || $numeroRegistros<10) {
                    $obras = DB::table('obras')
                        ->where('nombre', 'LIKE', '%' . $query . '%')
                        ->orwhere('extra', 'LIKE', '%' . $query . '%')
                        ->orwhere('revisado', 'LIKE', '%' . $query . '%')
                        ->orwhere('id_obra', 'LIKE', '%' . $query . '%')
                        ->orderBy('nombre', 'asc')
                        ->paginate($numeroElementos);

                    $obrasFinal=array();
                    foreach ($obras as $obra) {
                        $bandera = true;
                        foreach ($idLigados as $idLigado ) {
                            if($obra->id_obra == $idLigado){
                                unset($idLigado);
                                $auxIdLigados = array_values($idLigados);
                                $bandera=false;
                                break;
                            }
                        }
                        if($bandera) {
                            array_push($obrasFinal,$obra);
                        }
                    }
                    break;
                }
            }

            $page=$request->get('page');

            if($page==null){
                $page=1;
            }

            return view('obraObra.index',
                [
                    'obra_obra'=>$obra->id_obra,
                    'obras' => $obrasFinal,
                    'obrasdelaObra' => $obrasdelaObra,
                    "searchText" => $query,
                    "totalRegistros"=> $numeroRegistros,
                    "page"=> $page,
                    'obra' =>$obraBase,
                ]
            );
        }
    }


    //CUANDO UN obra NO EXISTE EN LA BASE DE DATOS
    //ESTA VISTA MANDA EL FORMULARIO PARA AGREGAR UN NUEVO obra

    public function nuevoObraObra(Request $request,$id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
         $obra =Obra::findOrFail($id);

        return view('obraObra.createObraObra',
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



     //ESTE METODO CREA el obra Y LO LIGA AUTOMATICAMENTE
    //CON ESta obra
    public function update(ObraFormRequest $request, $id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}

        //ID DELa obra
        $obra =Obra::findOrFail($id);
        //datos de la otra obra
        $obra2 = new Obra;
        //Obtiene el Id 100 real
        $id=Utilidad::getId("obras","id_obra");
        $obra2->id_obra=$id;
        $obra2->nombre = $request->get('nombre');
        $tipo= $request->get('tipo');
        if($tipo=='obra'){
            $obra2->extra =1;
        }
        else if($tipo =='complejo'){
            $obra2->extra =2;
        }

        //REVISADO 1
        // NO REVISADO=0
        $obra2->revisado = 0;

        DB::connection()->enableQueryLog();
        //SE GUARDA EL obra
        $obra2->save();
        //SE CREA LA LIGADURA
        $respuesta = ObraObra::firstOrCreate([
                    'fk_obra' => $obra->id_obra,
                    'fk_obra2' => $id],
                    [
                    'fk_obra' =>  $obra->id_obra,
                    'fk_obra2' => $id
                    ]);
        //REGRESA A LA VISTA ORIGINAL DONDE PODRAS VER LOS obras LIGADOS
            $ruta = "/obra_obra/ligar/".$obra->id_obra;
            Session::flash('flash_message3', 'La obra se vinculó con la Obra');

            //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    1,
                    "obra_obra",
                    "Se agregó el vinculo: ". json_encode($respuesta)
                );

                LogController::agregarLog(
                    1,
                    "obra",
                    "Se agregó el obra: ". json_encode($obra)
                );

            return Redirect::to($ruta);
    }

    //AQUI SE DESVINCULA la obra DEL obra
    public function destroy($id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        //Buscar el vinculo en la tabla obra_ correspondiente
        $aux =DB::table('obra_obra')
        ->where('fk_obra2',$id)
        ->where('fk_obra',$obra->id_obra)
        ->get();

        //y se elimina
        $aux->delete();

        return Redirect::to("/obra_obra/ligar/".$obra->id_obra);
    }

     public function destroy2($id,$id2)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        //Buscar el vinculo en la tabla cntrl correspondiente
        //y se elimina
        $borrar =ObraObra::where('fk_obra2', $id)->where('fk_obra', $id2)->delete();
        Session::flash('flash_message4', '¡La obra se desvinculó existosamente!');
        //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    3,
                    "obra_obra",
                    "Se eliminó el vinculo: ObraObra(".$id.") del Doc (".$id2.")"
                );
        return Redirect::to("/obra_obra/ligar/".$id2);
    }
}
