<?php

namespace sistema\Http\Controllers;
use DB;
use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use sistema\Http\Controllers\Controller;
use sistema\Http\Controllers\DocumentoController;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;


class ConsultasController extends Controller
{
    /*
    private function validarRoles(){
            if (!Auth::user()->authorizeRoles(['catalogador', 'admin'])) {return Redirect::to('/');}
    }
    */
    public function index(Request $request) {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin', 'revisor'])) {return Redirect::to('/');}

            $categorias=DB::table('catalogo_docu')->get();
          

            // funcion para sección de status de revisión
            if ($request->get('estatus')!=null && $request->get('busqueda')==null && $request->get('tipo')==null){
                    $query =$request->get('estatus');
                    $query2 =$request->get('proyecto');
                    $query3 =$request->get('searchText');

                    //Estatus : SI    Proyecto: SI   Busqueda:  SI
                    if($query !=null && $query2 !=null && $query3 !=null){


                        $documentos =DB::table('documento as doc')
                        ->join('cntrl_proyec as ctp','doc.Id_doc','=','ctp.fk_doc')

                          ->orwhere('doc.Id_doc', 'LIKE', '%' . $query3 . '%')
                        ->where('fk_proyec',$query2)
                        ->where('revisado',$query)
                        ->orwhere('doc.titulo', 'LIKE', '%' . $query3 . '%')


                        ->where('fk_proyec',$query2)
                        ->where('revisado',$query)

                        ->orderBy('doc.Id_doc', 'asc')
                        ->paginate(10);



                        $numeroRegistros= DB::table('documento as doc')
                        ->join('cntrl_proyec as ctp','doc.Id_doc','=','ctp.fk_doc')

                        ->orwhere('doc.Id_doc', 'LIKE', '%' . $query3 . '%')
                        ->where('fk_proyec',$query2)
                        ->where('revisado',$query)
                        ->orwhere('doc.titulo', 'LIKE', '%' . $query3 . '%')


                        ->where('fk_proyec',$query2)
                        ->where('revisado',$query)

                        ->orderBy('doc.Id_doc', 'asc')

                        ->count();
                    }


                     //Estatus : SI    Proyecto: NO   Busqueda:  NO
                     if($query !=null && $query2==0 && $query3 ==null){

                        $documentos = DB::table('documento')
                        ->where('revisado', '=', $query)
                        ->orderBy('Id_doc', 'asc')
                        ->paginate(10);


                        //DE LOS DOCUMENTOS SACAR UNICAMENTE AQUELLOS QUE TENGAN EL PROYECTO (QUERY2)
                        /*$documentos = DB::table('documento')
                        ->where('revisado', '=', $query)
                        ->paginate(10);
                        */




                        $numeroRegistros= DB::table('documento')
                        ->where('revisado', '=', $query)
                        ->count();
                    }
                //Estatus : SI    Proyecto: NO   Busqueda:  SI
                    if($query !=null && $query2==0 && $query3 !=null){

                        $documentos = DB::table('documento')
                        ->where('revisado', '=', $query)
                        ->where('Id_doc', 'LIKE', '%' . $query3 . '%')
                        ->orwhere('titulo', 'LIKE', '%' . $query3 . '%')
                        ->orderBy('Id_doc', 'asc')
                        ->paginate(10);


                        //DE LOS DOCUMENTOS SACAR UNICAMENTE AQUELLOS QUE TENGAN EL PROYECTO (QUERY2)
                        /*$documentos = DB::table('documento')
                        ->where('revisado', '=', $query)
                        ->paginate(10);
                        */




                        $numeroRegistros=  DB::table('documento')
                        ->where('revisado', '=', $query)
                        ->where('Id_doc', 'LIKE', '%' . $query3 . '%')
                        ->orwhere('titulo', 'LIKE', '%' . $query3 . '%')
                        ->orderBy('Id_doc', 'asc')
                        ->count();
                    }

                     //Estatus : SI    Proyecto: SI   Busqueda:  NO
                     if($query !=null && $query2 !=0 && $query3 ==null){



                        $documentos =DB::table('cntrl_proyec as ctp')
                        ->join('documento as doc','doc.Id_doc','=','ctp.fk_doc')
                        ->where('fk_proyec',$query2)
                        ->where('revisado',$query)
                        ->orderBy('doc.Id_doc', 'asc')

                        ->paginate(10);

                        $numeroRegistros= DB::table('cntrl_proyec as ctp')
                        ->join('documento as doc','doc.Id_doc','=','ctp.fk_doc')
                        ->where('fk_proyec',$query2)
                        ->where('revisado',$query)
                        ->orderBy('doc.Id_doc', 'asc')

                        ->count();
                    }







                    $page=$request->get('page')!=null? $request->get('page'):1;
                    return view('consultas.documentoPorEstatus',
                    [
                        "documentos"=>$documentos,
                        "totalRegistros"=> $numeroRegistros,
                        "page"=> $page,
                        "status"=>$query,
                        "searchText"=>$query3,
                        "proyecto"=>$query2

                    ]
                );
            }

            $proyectos = DB::table('catalogo_proyecto')
            ->orderBy('proyecto', 'asc')
            ->get();

            if ($request->get('busqueda')!=null && $request->get('tipo')==null){
                $query =$request->get('busqueda');
                $documento = DB::table('documento')
                ->where('Id_doc', '=', $query)->first();




                if($documento !=null){
                    $referencia = "";
                    $controller  = new DocumentoController();
                    $documento->fecha_consulta = Carbon::parse($documento->fecha_consulta)->format('d/m/Y'); // Cambio de formato a la fecha de consulta con ayuda del componente CARBON Laravel

                    $referencia = $controller->getReferencia($documento->Id_doc);
                    return view('consultas.documentoPorId',
                    [
                        "documento"=>$documento,
                        "referencia" =>$referencia
                    ]
                );
                }
                else {
                    Session::flash('message','No se encontró un Documento con ese Id');
                    return view('consultas.area',
                    [
                        'categorias'=> $categorias,
                        "proyectos"=>$proyectos
                    ]

                );
                }


            }



            if($request->get('tipo')!=null){

                $query =$request->get('tipo');
                $query2 =$request->get('searchText');

                if($query !=null && $query2 == null){
                    $documentos = DB::table('documento as d')
                    ->where('tipo', '=', $query)
                    ->paginate(10);

                    $numeroRegistros= DB::table('documento')
                    ->where('tipo', '=', $query)
                    ->count();
                }
                else if ($query !=null && $query2 != null){
                    $documentos = DB::table('documento as d')
                    ->where('tipo', '=', $query)
                    ->where('titulo', 'LIKE', '%' . $query2 . '%')
                    ->orwhere('Id_doc', 'LIKE', '%' . $query2 . '%')
                    ->orderBy('Id_doc', 'desc')
                    ->paginate(10);

                    $numeroRegistros= DB::table('documento')
                    ->where('tipo', '=', $query)
                    ->where('titulo', 'LIKE', '%' . $query2 . '%')
                    ->orwhere('Id_doc', 'LIKE', '%' . $query2 . '%')

                    ->count();

                }




                //TIPO DE DOCUMENTO
                if ($request->tipo == 1){
                    $tipoConsulta = 'Artículo';
                }
                else if ($request->tipo == 2){
                    $tipoConsulta = 'Boletines';
                }
                else if ($request->tipo == 3){
                    $tipoConsulta = 'Cartas y Oficios';
                }
                else if ($request->tipo == 4){
                    $tipoConsulta = 'Crónicas';
                }
                else if ($request->tipo == 5){
                    $tipoConsulta = 'Declaraciones y Comunicados';
                }
                else if ($request->tipo == 6)
                {
                    $tipoConsulta = 'Discursos';
                }

                else if ($request->tipo == 7){
                    $tipoConsulta = 'Informes';
                }
                else if ($request->tipo == 8){
                    $tipoConsulta = 'Libros';

                }

                else if ($request->tipo == 9){
                    $tipoConsulta = 'Notas';
                }
                else if ($request->tipo == 10){
                    $tipoConsulta = 'Ponencias';

                }

                else if ($request->tipo == 11){
                    $tipoConsulta = 'Proyectos';
                }
                else if ($request->tipo == 12){
                    $tipoConsulta = 'Otros';

                }

                else if ($request->tipo == 13){
                    $tipoConsulta = 'Tesis';
                }
                else if ($request->tipo == 14){
                    $tipoConsulta = 'Artículo de Revista';

                }

                else if ($request->tipo == 15){
                    $tipoConsulta = 'Capítulo de Libros';
                }
                else if ($request->tipo == 16)
                    $tipoConsulta = 'Videos';
                else if ($request->tipo == 17){
                    $tipoConsulta = 'Revistas';

                }

                else if ($request->tipo == 18){
                    $tipoConsulta = 'Artículos de Boletín';
                }

                else if ($request == null)
                    $tipoConsulta = '- - - - - - -';






                     //lista de referencias
                    $referencia = [];
                    $controller  = new DocumentoController();
                    for ($i = 0; $i < count($documentos); $i++) {

                        $referencia[$i] = $controller->getReferencia($documentos[$i]->Id_doc);
                    }

                    //borrar la query para el buscador
                   if($query==$request->get('tipo'))
                   {
                    $query ="";
                   }

                    $page=$request->get('page')!=null? $request->get('page'):1;



                            return view('consultas.resultados',
                                [
                                    'documentos'=>$documentos,
                                    'tipoConsulta' =>$tipoConsulta,
                                    "searchText" => $query2,
                                    "totalRegistros"=> $numeroRegistros,
                                    "page"=> $page,
                                    "tipo"=>$request->tipo,
                                    "referencia" =>$referencia

                                ]
                            );
                }







            return view('consultas.area',
            [
                'categorias'=> $categorias,
                "proyectos"=>$proyectos
            ]
        );
        }

        public function store(Request $request){
            return view('consultas.resultados',
                [

                ]
            );
        }
}
