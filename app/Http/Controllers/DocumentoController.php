<?php

namespace sistema\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use sistema\Http\Requests;
use sistema\Documento;
use sistema\Ponencia;
use sistema\FechaNormal;
use sistema\FechaExtra;
use sistema\Video;
use Illuminate\Support\Facades\Redirect;
use sistema\Http\Requests\DocumentoFormRequest;

use DB;
use Session;
use sistema\Http\Controllers\Controller;

use sistema\Http\Controllers\CapituloLibroController;
use sistema\Http\Controllers\LibroController;
use sistema\Http\Controllers\PonenciaController;
use sistema\Http\Controllers\RevistaBoletinController;
use sistema\Http\Controllers\TesisController;

use sistema\Http\Controllers\FechaExtraController;
use sistema\Http\Controllers\FechaNormalController;
use sistema\Utilidad;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Auth;
use BBCode;
use sistema\DocumentoInstitucion;
use sistema\DocumentoObra;
use sistema\Autor;

class DocumentoController extends Controller
{
     public function __construct()
    {

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        if ($request) {

            $query = trim($request->get('searchText'));

            if($request->get('role')==3 ){


                /* Clausula Where avanzanda, para poder ordenar mejor la la query. 
                En este caso esta función representa una consula de la siguiente manera:
                   select *  from documento where investigador = 'NombreInvestigador' and ( (titulo like 'some%') or (Id_doc = '%'));
                */


                $documento = DB::table('documento')           
                ->where(function ($q) use($query) {   
                    $q->where('investigador', Auth::user()->name);   // sección  where investigador = 'NombreInvestigador'
                })->Where(function($q) use($query) { // equivalente al AND 
                    $q ->where('titulo', 'LIKE', '%' . $query . '%')  // sección ( (titulo like 'some%') 
                        ->orwhere('Id_doc', 'LIKE', '%' . $query . '%');	//or (Id_doc = '%'));
                })->orderBy('Id_doc', 'desc')
                ->paginate(10);
                $numeroRegistros = DB::table('documento')
                ->where(function ($q) use($query) {
                    $q->where('investigador', Auth::user()->name); 
                })->Where(function($q) use($query) {
                    $q ->where('titulo', 'LIKE', '%' . $query . '%')
                        ->orwhere('Id_doc', 'LIKE', '%' . $query . '%');	
                })
                    ->orderBy('Id_doc', 'desc')->count();


            }else{
                $documento = DB::table('documento')
                    ->where('titulo', 'LIKE', '%' . $query . '%')
                    ->orwhere('Id_doc', 'LIKE', '%' . $query . '%')
                    ->orderBy('Id_doc', 'desc')
                    ->paginate(10);
                $numeroRegistros = DB::table('documento')
                    ->where('titulo', 'LIKE', '%' . $query . '%')
                    ->orwhere('Id_doc', 'LIKE', '%' . $query . '%')
                    ->orderBy('Id_doc', 'desc')->count();
            }






            $page=$request->get('page')!=null? $request->get('page'):1;
            $role = $request->get('role') != null ? $request->get('role') : null;





            return view('documento.index',
             ['documento' => $documento,
             "searchText" => $query,
             "totalRegistros"=> $numeroRegistros,
            "page"=> $page,
            "role"=>$role
             ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
          $categorias=DB::table('catalogo_docu')->get();

          $categorias = $categorias->filter(function($item) { //funcion que quita elemnto con id 18 (Videos)
            return $item->id_cata_doc != 16;
          });

          $mesesFecha = array('nombre'=>'Enero', 'Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
          return view('documento.create',['categorias' => $categorias,'mesesFecha'=> $mesesFecha]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DocumentoFormRequest $request)
    {
      $documento = new Documento;

      $documento->Id_doc= Utilidad::getId('documento','Id_doc');
      $documento->titulo = $request->get('titulo');
      $documento->lugar_public_pais = $request->get('lugar_public_pais')?$request->get('lugar_public_pais') :'';
      $documento->lugar_public_edo = $request->get('lugar_public_edo')?$request->get('lugar_public_edo'):'';
      $documento->derecho_autor = $request->get('derecho_autor');
      $documento->fecha_publi = $request->get('fecha_publi');
      $documento->url = $request->get('url')?$request->get('url'):'';
      $documento->investigador = Auth::user()->name;
      $documento->fecha_consulta = $request->get('fecha_consulta');
      $documento->poblacion = $request->get('poblacion');
      $documento->tipo = $request->get('tipo');
      $documento->notas = $request->get('notas')?$request->get('notas'):'';
      $documento->fecha_registro =  Carbon::now();;
      $documento->revisado = '0';
      $documento->linea = '0';

      

      $consultaTitulo = DB::table('documento')
      ->where('titulo', '=', $request->get('titulo'))->get();

      $consultaUrl = DB::table('documento')
      ->where('url', '=', $request->get('url'))->get();



    ///
    $idDocumento =  Utilidad::getId('documento','Id_doc');


      if(!$consultaTitulo->isEmpty()  && !$consultaUrl->isEmpty()){

        return Redirect::to('documento/create')->with('status', 'El documento ya se encuentra registrado!');

      }else{
        DB::beginTransaction();

        $fechaNormal = null;
        $fechaExtra = null;

        try{

        if($documento->save()){


            Log::warning($idDocumento);

            if($documento->fecha_publi==1){

                $fechaNormal = new FechaNormalController();
                $fechaNormal->agregarFechaNormal($idDocumento,$request);
            }else{
                $fechaExtra = new FechaExtraController();
                $fechaExtra->agregarFechaExtra($idDocumento,$request);

            }
            // if($fechaNormal->save() || $fechaExtra->save() ){
            //     DB::commit(); //se realiza el commit a la base. Guarda cambios
            //    return Redirect::to('documento');
            // }else{
            //     DB::rollback(); // rollback si no se guarda documento
            //     return Redirect::to('documento/create')->with('status', 'Error al registrar documento');
            // }


            // Registro segun tipo de documento.

            self::agregarTipoDocumento($idDocumento,$request->get('tipo'),$request);  ///llamada al metodo agregartipo documento




            //activar el log de la base de datos
             DB::connection()->enableQueryLog();

             DB::commit(); //se realiza el commit a la base. Guarda cambios

        //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
        LogController::agregarLog(
            1,
            "Documento",
            "Se agregó el Documento: ". json_encode($documento)
        );
            return Redirect::to('documento');




        }else{


            return Redirect::to('documento/create')->with('status', 'Error al registrar documento');
        }


    } catch (\Throwable  $e) {
        DB::rollback();

    }

        //$documento->save();

      }


    }


    function construirReferenciaTemas($temas) {


        if(count($temas)==0){
            return "";
        }else{
            $referenciaTema="Temas: ";
            for ($i=0; $i <count($temas)-1 ; $i++) {
                $referenciaTema = $referenciaTema. $temas[$i]->descripcion .", ";
            }
            $referenciaTema= $referenciaTema.$temas[count($temas)-1]->descripcion;
            return $referenciaTema;
        }

    }
    function construirReferenciaPoblacion($poblacion)
    {
        if (count($poblacion) == 0) {
            return "";
        } else {
            $referenciaPoblacion = "Población: ";
            return $referenciaPoblacion. $poblacion;
        }
    }


    function normaliza($cadena)
    {
        $originales = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';
        $modificadas = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
        $cadena = utf8_decode($cadena);
        $cadena = strtr($cadena, utf8_decode($originales), $modificadas);
        $cadena = strtolower($cadena);
        return utf8_encode($cadena);
    }
    public function construirReferenciaAutor($autor)
    {


        $referenciaAutor = "";

        if ($autor->nombre != "" && $autor->pseudonimo == "" && $autor->apellidos != "") {
             $referenciaAutor = $autor->apellidos . ", " . $autor->nombre .'';
        } else {
            if ($autor->nombre == "" && $autor->pseudonimo == "" && $autor->apellidos != "") {
                $referenciaAutor = $autor->apellidos;
            } else {
                if ($autor->nombre != "" && $autor->pseudonimo == "" && $autor->apellidos == "") {
                    $referenciaAutor = $autor->nombre."";
                } else {
                    if (($autor->nombre != "" || $autor->apellidos != "") && $autor->pseudonimo != "") {
                        $referenciaAutor =  $autor->pseudonimo . " [" . $autor->nombre . " " . $autor->apellidos . "]";
                    } else {
                        $referenciaAutor =  $autor->pseudonimo;
                    }
                }
            }
        }

        return $referenciaAutor;
    }

    public function construirReferenciaAutores($autores, $documento)
    {
        $numAutores = count($autores);
        //print($numAutores);
        $referenciaAutores = "";
        if ($numAutores == 0) {
            return "";
        } else {
            if ($numAutores == 1) {
                $referenciaAutores = self::construirReferenciaAutor($autores[0]);
                Log::warning("".$autores[0]->extra);
                if ($autores[0]->extra != "") {
                    $referenciaAutores = $referenciaAutores . ' (' . $autores[0]->extra . '.)';
                }
                return $referenciaAutores;
            } else {
                if ($numAutores == 2) {
                    $separador = " y ";
                    if ($autores[1]->nombre != "") {
                        //$aux = self::normaliza(substr($autores[1]->nombre, -strlen($autores[1]->nombre)));
                        $aux = trim($autores[1]->nombre);
                        $aux = substr($aux, 0, 1);

                        Log::warning($aux);
                        if ($aux == "i" || $aux == "I") {
                            $separador = " e ";
                        }
                    }

                    if ($autores[0]->extra == $autores[1]->extra && ($autores[0]->extra != "" && $autores[1]->extra != "")) {
                        $referenciaAutores = self::construirReferenciaAutor($autores[0]) . $separador . $autores[1]->nombre . " " . $autores[1]->apellidos;
                        $referenciaAutores = $referenciaAutores . ' (' . $autores[0]->extra . 's.)';

                    } else {
                        if ($autores[0]->extra != "" && $autores[1]->extra != "") {
                          //  $referenciaAutores = self::construirReferenciaAutor($autores[0]) . " " . ' (' . $autores[0]->extra . '.) ';
                          Log::warning("aquiiiiiiii");
                            if ($autores[0]->nombre != "" && $autores[0]->apellidos != "") {
                                $referenciaAutores = $autores[0]->apellidos . ", " . $autores[0]->nombre . ' (' . $autores[0]->extra . ')'.$separador ;
                            } else {
                                if ($autores[0]->nombre != "" && $autores[0]->pseudonimo == "" && $autores[0]->apellidos == "") {
                                    $referenciaAutores =  $autores[0]->nombre . ' (' . $autores[0]->extra . ')'.$separador ;
                                } else {
                                    if ($autores[0]->nombre == "" && $autores[0]->pseudonimo != "" && $autores[0]->apellidos == "") {
                                        $referenciaAutores =  $autores[0]->pseudonimo . ' (' . $autores[0]->extra . ')'.$separador ;
                                    }
                                }
                            }
                                                        // $referenciaAutores = $referenciaAutores . $autores[1]->nombre . " " . $autores[1]->apellidos . ' (' . $autores[1]->extra . '.)';
                            if ($autores[1]->nombre != "" && $autores[1]->apellidos != "") {
                                $referenciaAutores = $referenciaAutores. $autores[1]->apellidos .", " . $autores[1]->nombre . ' (' . $autores[1]->extra . ')' ;
                            } else {
                                if ($autores[1]->nombre != "" && $autores[1]->pseudonimo == "" && $autores[1]->apellidos == "") {
                                    $referenciaAutores = $referenciaAutores. $autores[1]->nombre . '(' . $autores[1]->extra . ')' ;
                                } else {
                                    if ($autores[1]->nombre == "" && $autores[1]->pseudonimo != "" && $autores[1]->apellidos == "") {
                                        $referenciaAutores = $referenciaAutores. $autores[1]->pseudonimo . '(' . $autores[1]->extra . ')' ;
                                    }
                                }
                            }



                        } else {
                           // $referenciaAutores = self::construirReferenciaAutor($autores[0]) . $separador;
                            if($autores[0]->extra!=""){
                                if ($autores[0]->nombre != "" && $autores[0]->apellidos != "") {
                                    $referenciaAutores = $autores[0]->apellidos . ", " . $autores[0]->nombre . ' (' . $autores[0]->extra . ')' ;
                                } else {
                                    if ($autores[0]->nombre != "" && $autores[0]->pseudonimo == "" && $autores[0]->apellidos == "") {
                                        $referenciaAutores =  $autores[0]->nombre . ' (' . $autores[0]->extra . ')' ;
                                    } else {
                                        if ($autores[0]->nombre == "" && $autores[0]->pseudonimo != "" && $autores[0]->apellidos == "") {
                                            $referenciaAutores =  $autores[0]->pseudonimo . ' (' . $autores[0]->extra . ')';
                                        }
                                    }
                                }
                            }else{
                                if ($autores[1]->extra != "") {
                                    if ($autores[1]->nombre != "" && $autores[1]->apellidos != "") {
                                        $referenciaAutores = $referenciaAutores. $autores[1]->apellidos .", " . $autores[1]->nombre . ' (' . $autores[1]->extra . ')' ;
                                    } else {
                                        if ($autores[1]->nombre != "" && $autores[1]->pseudonimo == "" && $autores[1]->apellidos == "") {
                                            $referenciaAutores = $referenciaAutores. $autores[1]->nombre . '(' . $autores[1]->extra . ')' ;
                                        } else {
                                            if ($autores[1]->nombre == "" && $autores[1]->pseudonimo != "" && $autores[1]->apellidos == "") {
                                                $referenciaAutores = $referenciaAutores. $autores[1]->pseudonimo . '(' . $autores[1]->extra . ')' ;
                                            }
                                        }
                                    }
                                } else {
                                // $referenciaAutores = $referenciaAutores . $autores[1]->nombre . " " . $autores[1]->apellidos;
                                if ($autores[0]->nombre != "" && $autores[0]->apellidos != "") {
                                    $referenciaAutores = $autores[0]->apellidos . ", " . $autores[0]->nombre.$separador ;
                                } else {
                                    if ($autores[0]->nombre != "" && $autores[0]->pseudonimo == "" && $autores[0]->apellidos == "") {
                                        $referenciaAutores =  $autores[0]->nombre .$separador ;
                                    } else {
                                        if ($autores[0]->nombre == "" && $autores[0]->pseudonimo != "" && $autores[0]->apellidos == "") {
                                            $referenciaAutores =  $autores[0]->pseudonimo .$separador ;
                                        }
                                    }
                                }
                                    if ($autores[1]->nombre != "" && $autores[1]->apellidos != "") {
                                        $referenciaAutores =$referenciaAutores. $autores[1]->nombre . " " . $autores[1]->apellidos ;
                                    } else {
                                        if ($autores[1]->nombre != "" && $autores[1]->pseudonimo == "" && $autores[1]->apellidos == "") {
                                            $referenciaAutores = $referenciaAutores. $autores[1]->nombre ;
                                        } else {
                                            if ($autores[1]->nombre == "" && $autores[1]->pseudonimo != "" && $autores[1]->apellidos == "") {
                                                $referenciaAutores = $referenciaAutores. $autores[1]->pseudonimo ;
                                            }
                                        }
                                    }
                                }

                            }

                        }

                    }
                    return $referenciaAutores;

                } else {
                    if ($numAutores >= 3) {

                        $bandera = true;
                        for ($i = 0; $i < $numAutores - 1; $i++) {
                            if ($autores[$i]->extra == "" || $autores[$i]->extra != $autores[$i + 1]->extra) {
                                $bandera = false;
                                break;
                            }
                        }
                        if ($bandera) {
                            Log::warning("Entro bandera");
                            if ($autores[0]->nombre != "" && $autores[0]->apellidos != "") {
                                $referenciaAutores = $autores[0]->apellidos . ", " . $autores[0]->nombre . ". [et al.], " . '(' . $autores[0]->extra . 's.)';
                            } else {
                                if ($autores[0]->nombre != "" && $autores[0]->pseudonimo == "" && $autores[0]->apellidos == "") {
                                    return $autores[0]->nombre . ". [et al.] " . '(' . $autores[0]->extra . 's.)';
                                } else {
                                    if ($autores[0]->nombre == "" && $autores[0]->pseudonimo != "" && $autores[0]->apellidos == "") {
                                        return $autores[0]->pseudonimo . ". [et al.] " . '(' . $autores[0]->extra . 's.)';
                                    }
                                }
                            }
                          //  $referenciaAutores = $autores[0]->apellidos . ", " . $autores[0]->nombre . " [et al.], " . '(' . $autores[0]->extra . '.s)';
                        } else {
                            $numCargos = 0;
                            for ($i = 0; $i < $numAutores; $i++) {
                                if ($autores[$i]->extra != "") {
                                    $numCargos = $numCargos + 1;
                                }
                            }

                            if ($numCargos == 1) {
                                $autor = null;
                                for ($i = 0; $i < $numAutores; $i++) {
                                    if ($autores[$i]->extra != "") {
                                        $autor = $autores[$i];
                                    }
                                }

                                if ($autor->nombre != "" && $autor->apellidos != "") {
                                    $referenciaAutores = $autor->apellidos . ", " . $autor->nombre . ". [et al.], " . '(' . $autor->extra . '.)';
                                } else {
                                    if ($autor->nombre != "" && $autor->pseudonimo == "" && $autor->apellidos == "") {
                                        $referenciaAutores = $autor->nombre . ". [et al.]" . '(' . $autor->extra . '.)';
                                    } else {
                                        if ($autor->nombre == "" && $autor->pseudonimo != "" && $autor->apellidos == "") {
                                            $referenciaAutores = $autor->pseudonimo . ". [et al.]" . '(' . $autor->extra . '.)';
                                        }
                                    }
                                }
                               // $referenciaAutores = $autor->apellidos . ", " . $autor->nombre . " [et al.], " . '(' . $autor->extra . '.)';
                            } else {
                                $contEd = 0;
                                for ($i = 0; $i < $numAutores; $i++) {
                                    if ($autores[$i]->extra == "ed") {
                                        $contEd = $contEd + 1;
                                        Log::warning("ContEd ".$contEd."- ". $numAutores);
                                    }
                                }
                                $contComp = 0;
                                for ($i = 0; $i < $numAutores; $i++) {
                                    if ($autores[$i]->extra == "comp") {
                                        $contComp = $contComp + 1;
                                    }
                                }
                                $contCoord = 0;
                                for ($i = 0; $i < $numAutores; $i++) {
                                    if ($autores[$i]->extra == "coord") {
                                        $contCoord = $contCoord + 1;
                                    }
                                }

                                if ($contEd == 1 && $contComp == 1 && $contCoord == 1) {
                                    Log::warning("Entro if 1-1");
                                    $autor = null;
                                    for ($i = 0; $i < $numAutores; $i++) {
                                        if ($autores[$i]->extra != "") {
                                            $autor = $autores[$i];
                                            break;
                                        }
                                    }

                                    if ($autor->nombre != "" && $autor->apellidos != "") {
                                        $referenciaAutores = $autor->apellidos . ", " . $autor->nombre . ". [et al.], " . '(' . $autor->extra . '.)';
                                    } else {
                                        if ($autor->nombre != "" && $autor->pseudonimo == "" && $autor->apellidos == "") {
                                            $referenciaAutores = $autor->nombre . ". [et al.], " . '(' . $autor->extra . '.)';
                                        } else {
                                            if ($autor->nombre == "" && $autor->pseudonimo != "" && $autor->apellidos == "") {
                                                $referenciaAutores = $autor->pseudonimo . ". [et al.], " . '(' . $autor->extra . '.)';
                                            }
                                        }
                                    }
                                    //$referenciaAutores = $autor->apellidos . ", " . $autor->nombre . " [et al.], " . '(' . $autores[0]->extra . '.)';
                                } else {
                                    if ($contEd == 0 && $contComp == 1 && $contCoord == 1) {
                                        Log::warning("Entro if 1-2");
                                        $autor = null;
                                        for ($i = 0; $i < $numAutores; $i++) {
                                            if ($autores[$i]->extra != "") {
                                                $autor = $autores[$i];
                                                break;
                                            }
                                        }
                                        if ($autor->nombre != "" && $autor->apellidos != "") {
                                            $referenciaAutores = $autor->apellidos . ", " . $autor->nombre . ". [et al.], " . '(' . $autor->extra . '.)';
                                        } else {
                                            if ($autor->nombre != "" && $autor->pseudonimo == "" && $autor->apellidos == "") {
                                                $referenciaAutores = $autor->nombre . ". [et al.], " . '(' . $autor->extra . '.)';
                                            } else {
                                                if ($autor->nombre == "" && $autor->pseudonimo != "" && $autor->apellidos == "") {
                                                    $referenciaAutores = $autor->pseudonimo . ". [et al.], " . '(' . $autor->extra . '.)';
                                                }
                                            }
                                        }
                                       // $referenciaAutores = $autor->apellidos . ", " . $autor->nombre . " [et al.], " . '(' . $autores[0]->extra . '.)';
                                    } else {
                                        if ($contEd == 1 && $contComp == 0 && $contCoord == 1) {
                                            Log::warning("Entro if 1-3");
                                            $autor = null;
                                            for ($i = 0; $i < $numAutores; $i++) {
                                                if ($autores[$i]->extra != "") {
                                                    $autor = $autores[$i];
                                                    break;
                                                }
                                            }
                                            if ($autor->nombre != "" && $autor->apellidos != "") {
                                                $referenciaAutores = $autor->apellidos . ", " . $autor->nombre . ". [et al.], " . '(' . $autor->extra . '.)';
                                            } else {
                                                if ($autor->nombre != "" && $autor->pseudonimo == "" && $autor->apellidos == "") {
                                                    $referenciaAutores = $autor->nombre . ". [et al.], " . '(' . $autor->extra . '.)';
                                                } else {
                                                    if ($autor->nombre == "" && $autor->pseudonimo != "" && $autor->apellidos == "") {
                                                        $referenciaAutores = $autor->pseudonimo . ". [et al.], " . '(' . $autor->extra . '.)';
                                                    }
                                                }
                                            }
                                           // $referenciaAutores = $autor->apellidos . ", " . $autor->nombre . " [et al.], " . '(' . $autores[0]->extra . '.)';
                                        } else {


                                            if ($contEd == 1 && $contComp == 1 && $contCoord == 0) {
                                                Log::warning("Entro if 1-4");
                                                $autor = null;
                                                for ($i = 0; $i < $numAutores; $i++) {
                                                    if ($autores[$i]->extra != "") {
                                                        $autor = $autores[$i];
                                                        break;
                                                    }
                                                }
                                                if ($autor->nombre != "" && $autor->apellidos != "") {
                                                    $referenciaAutores = $autor->apellidos . ", " . $autor->nombre . ". [et al.], " . '(' . $autor->extra . '.)';
                                                } else {
                                                    if ($autor->nombre != "" && $autor->pseudonimo == "" && $autor->apellidos == "") {
                                                        $referenciaAutores = $autor->nombre . ". [et al.], " . '(' . $autor->extra . '.)';
                                                    } else {
                                                        if ($autor->nombre == "" && $autor->pseudonimo != "" && $autor->apellidos == "") {
                                                            $referenciaAutores = $autor->pseudonimo . ". [et al.], " . '(' . $autor->extra . '.)';
                                                        }
                                                    }
                                                }
                                                  //  $referenciaAutores = $autor->apellidos . ", " . $autor->nombre . " [et al.], " . '(' . $autores[0]->extra . '.)';
                                            } else {
                                                if ($contEd == 0 && $contComp == 0 && $contCoord == 0) {
                                                    Log::warning("Entro if 1-0");
                                                    if ($autores[0]->nombre != "" && $autores[0]->apellidos != "") {
                                                        $referenciaAutores = $autores[0]->apellidos . ", " . $autores[0]->nombre . ". [et al.]";
                                                    } else {
                                                        if ($autores[0]->nombre != "" && $autores[0]->pseudonimo == "" && $autores[0]->apellidos == "") {
                                                            $referenciaAutores = $autores[0]->nombre . ". [et al.]";
                                                        } else {
                                                            if ($autores[0]->nombre == "" && $autores[0]->pseudonimo != "" && $autores[0]->apellidos == "") {
                                                                $referenciaAutores = $autores[0]->pseudonimo . ". [et al.]";
                                                            }
                                                        }
                                                    }
                                                    //$referenciaAutores = $autor->apellidos . ", " . $autor->nombre . " [et al.], ";
                                                } else {
                                                    if ($contEd < $contComp) {
                                                        Log::warning("---------------------------------Entro if 1");
                                                        Log::warning("---------------------------------Entro if ".$contEd."<".$contComp);
                                                        if ($contComp > $contCoord) {
                                                            $autor = null;
                                                            for ($i = 0; $i < $numAutores; $i++) {
                                                                if ($autores[$i]->extra == "comp") {
                                                                    $autor = $autores[$i];
                                                                    break;
                                                                }
                                                            }
                                                            if ($autor->nombre != "" && $autor->apellidos != "") {
                                                                $referenciaAutores = $autor->apellidos . ", " . $autor->nombre . ". [et al.], " . '(' . $autor->extra . 's.)';
                                                            } else {
                                                                if ($autor->nombre != "" && $autor->pseudonimo == "" && $autor->apellidos == "") {
                                                                    $referenciaAutores = $autor->nombre . ". [et al.], " . '(' . $autor->extra . 's.)';
                                                                } else {
                                                                    if ($autor->nombre == "" && $autor->pseudonimo != "" && $autor->apellidos == "") {
                                                                        $referenciaAutores = $autor->pseudonimo . ". [et al.], " . '(' . $autor->extra . 's.)';
                                                                    }
                                                                }
                                                            }
                                                           // $referenciaAutores = $autor->apellidos . ", " . $autor->nombre . " [et al.], " . '(' . $autores[0]->extra . '.s)';
                                                        } else {
                                                            Log::warning("---------------------------------Entro if 2");
                                                            $autor = null;
                                                            for ($i = 0; $i < $numAutores; $i++) {
                                                                if ($autores[$i]->extra == "coord") {
                                                                    $autor = $autores[$i];
                                                                    break;
                                                                }
                                                            }

                                                            if ($autor->nombre != "" && $autor->apellidos != "") {
                                                                $referenciaAutores = $autor->apellidos . ", " . $autor->nombre . ". [et al.], " . '(' . $autor->extra . 's.)';
                                                            } else {
                                                                if ($autor->nombre != "" && $autor->pseudonimo == "" && $autor->apellidos == "") {
                                                                    $referenciaAutores = $autor->nombre . ". [et al.], " . '(' . $autor->extra . 's.)';
                                                                } else {
                                                                    if ($autor->nombre == "" && $autor->pseudonimo != "" && $autor->apellidos == "") {
                                                                        $referenciaAutores = $autor->pseudonimo . ". [et al.], " . '(' . $autor->extra . 's.)';
                                                                    }
                                                                }
                                                            }
                                                            //$referenciaAutores = $autor->apellidos . ", " . $autor->nombre . " [et al.], " . '(' . $autores[0]->extra . '.s)';
                                                        }
                                                    } else {
                                                        if ($contEd > $contCoord) {
                                                            Log::warning("-----------------Entro if 3");
                                                            $autor = null;
                                                            for ($i = 0; $i < $numAutores; $i++) {
                                                                if ($autores[$i]->extra == "ed") {
                                                                    $autor = $autores[$i];
                                                                    break;
                                                                }
                                                            }
                                                            if ($autor->nombre != "" && $autor->apellidos != "") {
                                                                $referenciaAutores = $autor->apellidos . ", " . $autor->nombre . ". [et al.], " . '(' . $autor->extra . 's.)';
                                                            } else {
                                                                if ($autor->nombre != "" && $autor->pseudonimo == "" && $autor->apellidos == "") {
                                                                    $referenciaAutores = $autor->nombre . ". [et al.], " . '(' . $autor->extra . 's.)';
                                                                } else {
                                                                    if ($autor->nombre == "" && $autor->pseudonimo != "" && $autor->apellidos == "") {
                                                                        $referenciaAutores = $autor->pseudonimo . ". [et al.], " . '(' . $autor->extra . 's.)';
                                                                    }
                                                                }
                                                            }
                                                            //$referenciaAutores = $autor->apellidos . ", " . $autor->nombre . " [et al.], " . '(' . $autores[0]->extra . '.s)';
                                                        } else {
                                                            Log::warning("Entro if 4");
                                                            $autor = null;
                                                            for ($i = 0; $i < $numAutores; $i++) {
                                                                if ($autores[$i]->extra == "coord") {
                                                                    $autor = $autores[$i];
                                                                    break;
                                                                }
                                                            }

                                                            if ($autor->nombre != "" && $autor->apellidos != "") {
                                                                $referenciaAutores = $autor->apellidos . ", " . $autor->nombre . ". [et al.], " . '(' . $autor->extra . 's.)';
                                                            } else {
                                                                if ($autor->nombre != "" && $autor->pseudonimo == "" && $autor->apellidos == "") {
                                                                    $referenciaAutores = $autor->nombre . ". [et al.], " . '(' . $autor->extra . 's.)';
                                                                } else {
                                                                    if ($autor->nombre == "" && $autor->pseudonimo != "" && $autor->apellidos == "") {
                                                                        $referenciaAutores = $autor->pseudonimo . ". [et al.], " . '(' . $autor->extra . 's.)';
                                                                    }
                                                                }
                                                            }
                                                           // $referenciaAutores = $autor->apellidos . ", " . $autor->nombre . " [et al.], " . '(' . $autores[0]->extra . '.s)';
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        return $referenciaAutores;
                    }
                }
            }
        }
    }
        //Obtiene todos los datos y retorna la referencia ya construida como cadena
        public function construirReferenciaArticulo($documento, $autores, $editores)
        {
            $referenciaReal = "";

            $referenciaTitulo = "\"" . $documento->titulo . "\"";

            $referenciaAutor= self::construirReferenciaAutores($autores,$documento);
            $referenciaEditorial=self::obtenerEditoresReferencia($editores);

            $referenciaFechPublicacion=self::obtenerFechaReferencia($documento);
            $referenciaLugarPublicacion=self::obtenerLugarPublicacionReferenia($documento);

            if($referenciaAutor!=""){
                $referenciaReal= $referenciaReal . $referenciaAutor . ", ";
            }
            if ($referenciaTitulo != "") {
                $referenciaReal = $referenciaReal . $referenciaTitulo . ", ";
            }
            if ($referenciaEditorial != "") {
                $referenciaReal = $referenciaReal . $referenciaEditorial . ", ";
            }
            if ($referenciaLugarPublicacion != "") {
                $referenciaReal = $referenciaReal . $referenciaLugarPublicacion . " ";
            }
            if ($referenciaFechPublicacion != "") {
                $referenciaReal = $referenciaReal . $referenciaFechPublicacion . "";
            }
            //$referenciaReal =$referenciaAutor.", ".$referenciaTitulo.", ".$referenciaEditorial.", ".$referenciaLugarPublicacion.", ".$referenciaFechPublicacion;
            Log::warning($referenciaReal);
            return $referenciaReal.".";
        }
    //Obtiene todos los datos y retorna la referencia ya construida como cadena
    public function construirReferenciaArticulo1($documento, $autores, $editores)
    {
        $referenciaReal = "";
        $referenciaAutor= self::construirReferenciaAutores($autores,$documento);
        if($referenciaAutor!=""){
            $referenciaReal= $referenciaReal . $referenciaAutor . ", ";
        }
        //$referenciaReal =$referenciaAutor.", ".$referenciaTitulo.", ".$referenciaEditorial.", ".$referenciaLugarPublicacion.", ".$referenciaFechPublicacion;
        Log::warning($referenciaReal);
        return $referenciaReal;
    }
    //Obtiene todos los datos y retorna la referencia ya construida como cadena
    public function construirReferenciaArticulo2($documento, $autores, $editores)
    {
        $referenciaReal = "";
        $referenciaTitulo = "\"" . $documento->titulo . "\"";
        if ($referenciaTitulo != "") {
            $referenciaReal = $referenciaReal . $referenciaTitulo . ", ";
        }
        //$referenciaReal =$referenciaAutor.", ".$referenciaTitulo.", ".$referenciaEditorial.", ".$referenciaLugarPublicacion.", ".$referenciaFechPublicacion;
        Log::warning($referenciaReal);
        return $referenciaReal;
    }

    //Obtiene todos los datos y retorna la referencia ya construida como cadena
    public function construirReferenciaArticulo3($documento, $autores, $editores)
    {
        $referenciaReal = "";


        $referenciaEditorial=self::obtenerEditoresReferencia($editores);

        if ($referenciaEditorial != "") {
            $referenciaReal = $referenciaReal . $referenciaEditorial . ", ";
        }
        //$referenciaReal =$referenciaAutor.", ".$referenciaTitulo.", ".$referenciaEditorial.", ".$referenciaLugarPublicacion.", ".$referenciaFechPublicacion;
        Log::warning($referenciaReal);
        return $referenciaReal;
    }
    //Obtiene todos los datos y retorna la referencia ya construida como cadena
    public function construirReferenciaArticulo4($documento, $autores, $editores)
    {
        $referenciaReal = "";
        $referenciaFechPublicacion=self::obtenerFechaReferencia($documento);
        $referenciaLugarPublicacion=self::obtenerLugarPublicacionReferenia($documento);

        if ($referenciaLugarPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaLugarPublicacion . " ";
        }
        if ($referenciaFechPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaFechPublicacion . "";
        }
        //$referenciaReal =$referenciaAutor.", ".$referenciaTitulo.", ".$referenciaEditorial.", ".$referenciaLugarPublicacion.", ".$referenciaFechPublicacion;
        Log::warning($referenciaReal);
        return $referenciaReal.".";
    }
///

    public function construirReferenciaOtros($documento, $autores, $editores) {
        $referenciaReal = "";

        $referenciaTitulo = "\"" . $documento->titulo . "\"";

        $referenciaAutor = self::construirReferenciaAutores($autores,$documento);
        $referenciaEditorial = self::obtenerEditoresReferencia($editores);

        $referenciaFechPublicacion = self::obtenerFechaReferencia($documento);
        $referenciaLugarPublicacion = self::obtenerLugarPublicacionReferenia($documento);

        if ($referenciaAutor != "") {
            $referenciaReal = $referenciaReal . $referenciaAutor . ", ";
        }
        if ($referenciaTitulo != "") {
            $referenciaReal = $referenciaReal . $referenciaTitulo . ", ";
        }
        if ($referenciaEditorial != "") {
            $referenciaReal = $referenciaReal . $referenciaEditorial . ", ";
        }
        if ($referenciaLugarPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaLugarPublicacion . " ";
        }
        if ($referenciaFechPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaFechPublicacion . "";
        }
        //$referenciaReal =$referenciaAutor.", ".$referenciaTitulo.", ".$referenciaEditorial.", ".$referenciaLugarPublicacion.", ".$referenciaFechPublicacion;
        Log::warning($referenciaReal);
        return $referenciaReal.".";
    }
    public function construirReferenciaOtros1($documento, $autores, $editores) {
        $referenciaReal = "";
        $referenciaTitulo = "\"" . $documento->titulo . "\"";
        $referenciaAutor = self::construirReferenciaAutores($autores,$documento);

        if ($referenciaAutor != "") {
            $referenciaReal = $referenciaReal . $referenciaAutor . ", ";
        }
        if ($referenciaTitulo != "") {
            $referenciaReal = $referenciaReal . $referenciaTitulo . ", ";
        }
        //$referenciaReal =$referenciaAutor.", ".$referenciaTitulo.", ".$referenciaEditorial.", ".$referenciaLugarPublicacion.", ".$referenciaFechPublicacion;
        Log::warning($referenciaReal);
        return $referenciaReal;
    }

    public function construirReferenciaOtros2($documento, $autores, $editores) {
        $referenciaReal = "";
        $referenciaEditorial = self::obtenerEditoresReferencia($editores);

        if ($referenciaEditorial != "" && $referenciaEditorial != "[s.e.]") {
            $referenciaReal =  BBCode::parse('[i]'.$referenciaReal . $referenciaEditorial.'[/i]') . ", ";
        }else if($referenciaEditorial == "[s.e.]"){
            $referenciaReal = $referenciaReal . $referenciaEditorial . ", ";

        }


        //$referenciaReal =$referenciaAutor.", ".$referenciaTitulo.", ".$referenciaEditorial.", ".$referenciaLugarPublicacion.", ".$referenciaFechPublicacion;
        Log::warning($referenciaReal);
        return $referenciaReal;
    }
    public function construirReferenciaOtros3($documento, $autores, $editores) {
        $referenciaReal = "";
        $referenciaFechPublicacion = self::obtenerFechaReferencia($documento);
        $referenciaLugarPublicacion = self::obtenerLugarPublicacionReferenia($documento);

        if ($referenciaLugarPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaLugarPublicacion . " ";
        }
        if ($referenciaFechPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaFechPublicacion . "";
        }
        //$referenciaReal =$referenciaAutor.", ".$referenciaTitulo.", ".$referenciaEditorial.", ".$referenciaLugarPublicacion.", ".$referenciaFechPublicacion;
        Log::warning($referenciaReal);
        return $referenciaReal.".";
    }


///

    public function construirReferenciaCronica($documento, $autores, $editores)
    {
        $referenciaReal = "";

        $referenciaTitulo = "\"" . $documento->titulo . "\"";

        $referenciaAutor= self::construirReferenciaAutores($autores,$documento);
        $referenciaEditorial=self::obtenerEditoresReferencia($editores);

        $referenciaFechPublicacion=self::obtenerFechaReferencia($documento);
        $referenciaLugarPublicacion=self::obtenerLugarPublicacionReferenia($documento);

        if($referenciaAutor!=""){
            $referenciaReal= $referenciaReal . $referenciaAutor . ", ";
        }
        if ($referenciaTitulo != "") {
            $referenciaReal = $referenciaReal . $referenciaTitulo . ", ";
        }
        if ($referenciaEditorial != "") {
            $referenciaReal = $referenciaReal . $referenciaEditorial . ", ";
        }
        if ($referenciaLugarPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaLugarPublicacion . " ";
        }
        if ($referenciaFechPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaFechPublicacion . "";
        }
        //$referenciaReal =$referenciaAutor.", ".$referenciaTitulo.", ".$referenciaEditorial.", ".$referenciaLugarPublicacion.", ".$referenciaFechPublicacion;
        Log::warning($referenciaReal);
        return $referenciaReal.".";
    }
    public function construirReferenciaCronica1($documento, $autores, $editores)
    {
        $referenciaReal = "";

        $referenciaTitulo = "\"" . $documento->titulo . "\"";

        $referenciaAutor= self::construirReferenciaAutores($autores,$documento);
        if($referenciaAutor!=""){
            $referenciaReal= $referenciaReal . $referenciaAutor . ", ";
        }
        if ($referenciaTitulo != "") {
            $referenciaReal = $referenciaReal . $referenciaTitulo . ", ";
        }

        //$referenciaReal =$referenciaAutor.", ".$referenciaTitulo.", ".$referenciaEditorial.", ".$referenciaLugarPublicacion.", ".$referenciaFechPublicacion;
        Log::warning($referenciaReal);
        return $referenciaReal;
    }
    public function construirReferenciaCronica2($documento, $autores, $editores)
    {
        $referenciaReal = "";

        $referenciaEditorial=self::obtenerEditoresReferencia($editores);

        if ($referenciaEditorial != "") {
            $referenciaReal = $referenciaReal . $referenciaEditorial . ", ";
        }

        //$referenciaReal =$referenciaAutor.", ".$referenciaTitulo.", ".$referenciaEditorial.", ".$referenciaLugarPublicacion.", ".$referenciaFechPublicacion;
        Log::warning($referenciaReal);
        return $referenciaReal;
    }
    public function construirReferenciaCronica3($documento, $autores, $editores)
    {
        $referenciaReal = "";

        $referenciaFechPublicacion=self::obtenerFechaReferencia($documento);
        $referenciaLugarPublicacion=self::obtenerLugarPublicacionReferenia($documento);


        if ($referenciaLugarPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaLugarPublicacion . " ";
        }
        if ($referenciaFechPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaFechPublicacion . "";
        }
        //$referenciaReal =$referenciaAutor.", ".$referenciaTitulo.", ".$referenciaEditorial.", ".$referenciaLugarPublicacion.", ".$referenciaFechPublicacion;
        Log::warning($referenciaReal);
        return $referenciaReal.".";
    }

    //Obtiene todos los datos y retorna la referencia ya construida como cadena
    public function construirReferenciaCartasOficios($documento, $autores, $editores)
    {
        $referenciaReal = "";

        $referenciaTitulo = "\"" . $documento->titulo . "\"";

        $referenciaAutor = self::construirReferenciaAutores($autores,$documento);
        $referenciaEditorial = self::obtenerEditoresReferencia($editores);

        $referenciaFechPublicacion = self::obtenerFechaReferencia($documento);
        $referenciaLugarPublicacion = self::obtenerLugarPublicacionReferenia($documento);

        if ($referenciaAutor != "") {
            $referenciaReal = $referenciaReal . $referenciaAutor . ", ";
        }
        if ($referenciaTitulo != "") {
            $referenciaReal = $referenciaReal . $referenciaTitulo . ", ";
        }
        if ($referenciaLugarPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaLugarPublicacion . " ";
        }
        if ($referenciaEditorial != "") {
            $referenciaReal = $referenciaReal . $referenciaEditorial . ", ";
        }

        if ($referenciaFechPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaFechPublicacion . "";
        }

        //$referenciaReal = $referenciaAutor . ", " . $referenciaTitulo . ", " . $referenciaLugarPublicacion . ", " . $referenciaEditorial . ", " . $referenciaFechPublicacion;
        Log::warning($referenciaReal);
        return $referenciaReal.".";
    }
    //Obtiene todos los datos y retorna la referencia ya construida como cadena
    public function construirReferenciaCartasOficios1($documento, $autores, $editores) {
        $referenciaReal = "";

        $referenciaAutor = self::construirReferenciaAutores($autores,$documento);

        if ($referenciaAutor != "") {
            $referenciaReal = $referenciaReal . $referenciaAutor . ", ";
        }
        //$referenciaReal = $referenciaAutor . ", " . $referenciaTitulo . ", " . $referenciaLugarPublicacion . ", " . $referenciaEditorial . ", " . $referenciaFechPublicacion;
        Log::warning($referenciaReal);
        return $referenciaReal;
    }
        //Obtiene todos los datos y retorna la referencia ya construida como cadena
    public function construirReferenciaCartasOficios2($documento, $autores, $editores)
    {
        $referenciaReal = "";

        $referenciaTitulo = "" . $documento->titulo . "";

        if ($referenciaTitulo != "") {
            $referenciaReal = $referenciaReal . $referenciaTitulo . ", ";
        }

        //$referenciaReal = $referenciaAutor . ", " . $referenciaTitulo . ", " . $referenciaLugarPublicacion . ", " . $referenciaEditorial . ", " . $referenciaFechPublicacion;
        Log::warning($referenciaReal);
        return $referenciaReal;
    }
        //Obtiene todos los datos y retorna la referencia ya construida como cadena
        public function construirReferenciaCartasOficios3($documento, $autores, $editores)
        {
            $referenciaReal = "";

            $referenciaEditorial = self::obtenerEditoresReferencia($editores);

            $referenciaFechPublicacion = self::obtenerFechaReferencia($documento);
            $referenciaLugarPublicacion = self::obtenerLugarPublicacionReferenia($documento);

            if ($referenciaLugarPublicacion != "") {
                $referenciaReal = $referenciaReal . $referenciaLugarPublicacion . " ";
            }
            if ($referenciaEditorial != "") {
                $referenciaReal = $referenciaReal . $referenciaEditorial . ", ";
            }

            if ($referenciaFechPublicacion != "") {
                $referenciaReal = $referenciaReal . $referenciaFechPublicacion . "";
            }

            //$referenciaReal = $referenciaAutor . ", " . $referenciaTitulo . ", " . $referenciaLugarPublicacion . ", " . $referenciaEditorial . ", " . $referenciaFechPublicacion;
            Log::warning($referenciaReal);
            return $referenciaReal.".";
        }

    //Obtiene todos los datos y retorna la referencia ya construida como cadena
    public function construirReferenciaDeclaracionesComunicados($documento, $autores, $editores)
    {
        $referenciaReal = "";

        $referenciaTitulo = "\"" . $documento->titulo . "\"";

        $referenciaAutor = self::construirReferenciaAutores($autores,$documento);
        $referenciaEditorial = self::obtenerEditoresReferencia($editores);

        $referenciaFechPublicacion = self::obtenerFechaReferencia($documento);
        $referenciaLugarPublicacion = self::obtenerLugarPublicacionReferenia($documento);

        if ($referenciaAutor != "") {
            $referenciaReal = $referenciaReal . $referenciaAutor . ", ";
        }
        if ($referenciaTitulo != "") {
            $referenciaReal = $referenciaReal . $referenciaTitulo . ", ";
        }
        if ($referenciaLugarPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaLugarPublicacion . " ";
        }
        if ($referenciaEditorial != "") {
            $referenciaReal = $referenciaReal . $referenciaEditorial . ", ";
        }

        if ($referenciaFechPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaFechPublicacion . "";
        }

        //$referenciaReal = $referenciaAutor . ", " . $referenciaTitulo . ", " . $referenciaLugarPublicacion . ", " . $referenciaEditorial . ", " . $referenciaFechPublicacion;
        Log::warning($referenciaReal);
        return $referenciaReal.".";
    }

        //Obtiene todos los datos y retorna la referencia ya construida como cadena
    public function construirReferenciaDeclaracionesComunicados1($documento, $autores, $editores) {
        $referenciaReal = "";

        $referenciaAutor = self::construirReferenciaAutores($autores,$documento);

        if ($referenciaAutor != "") {
            $referenciaReal = $referenciaReal . $referenciaAutor . ", ";
        }

        //$referenciaReal = $referenciaAutor . ", " . $referenciaTitulo . ", " . $referenciaLugarPublicacion . ", " . $referenciaEditorial . ", " . $referenciaFechPublicacion;
        Log::warning($referenciaReal);
        return $referenciaReal;
    }
        //Obtiene todos los datos y retorna la referencia ya construida como cadena
    public function construirReferenciaDeclaracionesComunicados2($documento, $autores, $editores)
    {
        $referenciaReal = "";

        $referenciaTitulo = "" . $documento->titulo . "";

        if ($referenciaTitulo != "") {
            $referenciaReal = $referenciaReal . $referenciaTitulo . ", ";
        }
        //$referenciaReal = $referenciaAutor . ", " . $referenciaTitulo . ", " . $referenciaLugarPublicacion . ", " . $referenciaEditorial . ", " . $referenciaFechPublicacion;
        Log::warning($referenciaReal);
        return $referenciaReal;
    }
        //Obtiene todos los datos y retorna la referencia ya construida como cadena
        public function construirReferenciaDeclaracionesComunicados3($documento, $autores, $editores)
        {
            $referenciaReal = "";

            $referenciaEditorial = self::obtenerEditoresReferencia($editores);

            $referenciaFechPublicacion = self::obtenerFechaReferencia($documento);
            $referenciaLugarPublicacion = self::obtenerLugarPublicacionReferenia($documento);

            if ($referenciaLugarPublicacion != "") {
                $referenciaReal = $referenciaReal . $referenciaLugarPublicacion . " ";
            }
            if ($referenciaEditorial != "") {
                $referenciaReal = $referenciaReal . $referenciaEditorial . ", ";
            }

            if ($referenciaFechPublicacion != "") {
                $referenciaReal = $referenciaReal . $referenciaFechPublicacion . "";
            }

            //$referenciaReal = $referenciaAutor . ", " . $referenciaTitulo . ", " . $referenciaLugarPublicacion . ", " . $referenciaEditorial . ", " . $referenciaFechPublicacion;
            Log::warning($referenciaReal);
            return $referenciaReal.".";
        }


    //

    public function construirReferenciaDiscurso($documento, $autores, $editores)
    {
        $referenciaReal = "";

        $referenciaTitulo = "\"" . $documento->titulo . "\"";

        $referenciaAutor = self::construirReferenciaAutores($autores,$documento);
        $referenciaEditorial = self::obtenerEditoresReferencia($editores);

        $referenciaFechPublicacion = self::obtenerFechaReferencia($documento);
        $referenciaLugarPublicacion = self::obtenerLugarPublicacionReferenia($documento);

        if ($referenciaAutor != "") {
            $referenciaReal = $referenciaReal . $referenciaAutor . ", ";
        }
        if ($referenciaTitulo != "") {
            $referenciaReal = $referenciaReal . $referenciaTitulo . ", ";
        }
        if ($referenciaLugarPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaLugarPublicacion . " ";
        }
        if ($referenciaEditorial != "") {
            $referenciaReal = $referenciaReal . $referenciaEditorial . ", ";
        }

        if ($referenciaFechPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaFechPublicacion . "";
        }

        //$referenciaReal = $referenciaAutor . ", " . $referenciaTitulo . ", " . $referenciaLugarPublicacion . ", " . $referenciaEditorial . ", " . $referenciaFechPublicacion;
        Log::warning($referenciaReal);
        return $referenciaReal.".";
    }
    public function construirReferenciaDiscurso1($documento, $autores, $editores)
    {
        $referenciaReal = "";
        $referenciaAutor = self::construirReferenciaAutores($autores,$documento);
        if ($referenciaAutor != "") {
            $referenciaReal = $referenciaReal . $referenciaAutor . ", ";
        }
        //$referenciaReal = $referenciaAutor . ", " . $referenciaTitulo . ", " . $referenciaLugarPublicacion . ", " . $referenciaEditorial . ", " . $referenciaFechPublicacion;
        Log::warning($referenciaReal);
        return $referenciaReal;
    }
    public function construirReferenciaDiscurso2($documento, $autores, $editores)
    {
        $referenciaReal = "";
        $referenciaTitulo = "" . $documento->titulo . "";

        if ($referenciaTitulo != "") {
            $referenciaReal = $referenciaReal . $referenciaTitulo . ", ";
        }
        //$referenciaReal = $referenciaAutor . ", " . $referenciaTitulo . ", " . $referenciaLugarPublicacion . ", " . $referenciaEditorial . ", " . $referenciaFechPublicacion;
        Log::warning($referenciaReal);
        return $referenciaReal;
    }
    public function construirReferenciaDiscurso3($documento, $autores, $editores)
    {
        $referenciaReal = "";
        $referenciaEditorial = self::obtenerEditoresReferencia($editores);

        $referenciaFechPublicacion = self::obtenerFechaReferencia($documento);
        $referenciaLugarPublicacion = self::obtenerLugarPublicacionReferenia($documento);

        if ($referenciaLugarPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaLugarPublicacion . " ";
        }
        if ($referenciaEditorial != "") {
            $referenciaReal = $referenciaReal . $referenciaEditorial . ", ";
        }

        if ($referenciaFechPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaFechPublicacion . "";
        }

        //$referenciaReal = $referenciaAutor . ", " . $referenciaTitulo . ", " . $referenciaLugarPublicacion . ", " . $referenciaEditorial . ", " . $referenciaFechPublicacion;
        Log::warning($referenciaReal);
        return $referenciaReal.".";
    }
    //Obtiene todos los datos y retorna la referencia ya construida como cadena
    public function construirReferenciaInformes($documento, $autores, $editores)
    {
        $referenciaReal = "";

        $referenciaTitulo = "\"" . $documento->titulo . "\"";

        $referenciaAutor = self::construirReferenciaAutores($autores,$documento);
        $referenciaEditorial = self::obtenerEditoresReferencia($editores);

        $referenciaFechPublicacion = self::obtenerFechaReferencia($documento);
        $referenciaLugarPublicacion = self::obtenerLugarPublicacionReferenia($documento);

        if ($referenciaAutor != "") {
            $referenciaReal = $referenciaReal . $referenciaAutor . ", ";
        }
        if ($referenciaTitulo != "") {
            $referenciaReal = $referenciaReal . $referenciaTitulo . ", ";
        }
        if ($referenciaLugarPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaLugarPublicacion . " ";
        }
        if ($referenciaEditorial != "") {
            $referenciaReal = $referenciaReal . $referenciaEditorial . ", ";
        }

        if ($referenciaFechPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaFechPublicacion . "";
        }

        //$referenciaReal = $referenciaAutor . ", " . $referenciaTitulo . ", " . $referenciaLugarPublicacion . ", " . $referenciaEditorial . ", " . $referenciaFechPublicacion;
        Log::warning($referenciaReal);
        return $referenciaReal.".";
    }
        //Obtiene todos los datos y retorna la referencia ya construida como cadena
    public function construirReferenciaInformes1($documento, $autores, $editores)
    {
        $referenciaReal = "";
        $referenciaAutor = self::construirReferenciaAutores($autores,$documento);
        if ($referenciaAutor != "") {
            $referenciaReal = $referenciaReal . $referenciaAutor . ", ";
        }
        //$referenciaReal = $referenciaAutor . ", " . $referenciaTitulo . ", " . $referenciaLugarPublicacion . ", " . $referenciaEditorial . ", " . $referenciaFechPublicacion;
        Log::warning($referenciaReal);
        return $referenciaReal;
    }
        //Obtiene todos los datos y retorna la referencia ya construida como cadena
    public function construirReferenciaInformes2($documento, $autores, $editores)
    {
        $referenciaReal = "";
        $referenciaTitulo = "" . $documento->titulo . "";
        if ($referenciaTitulo != "") {
            $referenciaReal = $referenciaReal . $referenciaTitulo . ", ";
        }
        //$referenciaReal = $referenciaAutor . ", " . $referenciaTitulo . ", " . $referenciaLugarPublicacion . ", " . $referenciaEditorial . ", " . $referenciaFechPublicacion;
        Log::warning($referenciaReal);
        return $referenciaReal;
    }
        //Obtiene todos los datos y retorna la referencia ya construida como cadena
        public function construirReferenciaInformes3($documento, $autores, $editores)
        {
            $referenciaReal = "";
            $referenciaEditorial = self::obtenerEditoresReferencia($editores);

            $referenciaFechPublicacion = self::obtenerFechaReferencia($documento);
            $referenciaLugarPublicacion = self::obtenerLugarPublicacionReferenia($documento);

            if ($referenciaLugarPublicacion != "") {
                $referenciaReal = $referenciaReal . $referenciaLugarPublicacion . " ";
            }
            if ($referenciaEditorial != "") {
                $referenciaReal = $referenciaReal . $referenciaEditorial . ", ";
            }

            if ($referenciaFechPublicacion != "") {
                $referenciaReal = $referenciaReal . $referenciaFechPublicacion . "";
            }

            //$referenciaReal = $referenciaAutor . ", " . $referenciaTitulo . ", " . $referenciaLugarPublicacion . ", " . $referenciaEditorial . ", " . $referenciaFechPublicacion;
            Log::warning($referenciaReal);
            return $referenciaReal.".";
        }
    //Obtiene todos los datos y retorna la referencia ya construida como cadena
    public function construirReferenciaNotas($documento, $autores, $editores)
    {
        $referenciaReal = "";

        $referenciaTitulo = "\"" . $documento->titulo . "\"";

        $referenciaAutor = self::construirReferenciaAutores($autores,$documento);
        $referenciaEditorial = self::obtenerEditoresReferencia($editores);

        $referenciaFechPublicacion = self::obtenerFechaReferencia($documento);
        $referenciaLugarPublicacion = self::obtenerLugarPublicacionReferenia($documento);

        if ($referenciaAutor != "") {
            $referenciaReal = $referenciaReal . $referenciaAutor . ", ";
        }
        if ($referenciaTitulo != "") {
            $referenciaReal = $referenciaReal . $referenciaTitulo . ", ";
        }
        if ($referenciaEditorial != "") {
            $referenciaReal = $referenciaReal . $referenciaEditorial . ", ";
        }
        if ($referenciaLugarPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaLugarPublicacion . " ";
        }
        if ($referenciaFechPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaFechPublicacion . "";
        }

        Log::warning($referenciaReal);
        return $referenciaReal.".";
    }
    public function construirReferenciaNotas1($documento, $autores, $editores)
    {
        $referenciaReal = "";
        $referenciaTitulo = "\"" . $documento->titulo . "\"";
        $referenciaAutor = self::construirReferenciaAutores($autores,$documento);

        if ($referenciaAutor != "") {
            $referenciaReal = $referenciaReal . $referenciaAutor . ", ";
        }
        if ($referenciaTitulo != "") {
            $referenciaReal = $referenciaReal . $referenciaTitulo . ", ";
        }
        Log::warning($referenciaReal);
        return $referenciaReal;
    }
    public function construirReferenciaNotas2($documento, $autores, $editores)
    {
        $referenciaReal = "";
        $referenciaEditorial = self::obtenerEditoresReferencia($editores);
        if ($referenciaEditorial != "" && $referenciaEditorial != "[s.e.]") {

            $referenciaReal = BBCode::parse('[i]'.$referenciaReal . $referenciaEditorial .'[/i]'). ", ";
        }else if($referenciaEditorial == "[s.e.]"){
            $referenciaReal = $referenciaReal . $referenciaEditorial .", ";
        }
        Log::warning($referenciaReal);
        return $referenciaReal;
    }
    public function construirReferenciaNotas3($documento, $autores, $editores)
    {
        $referenciaReal = "";
        $referenciaFechPublicacion = self::obtenerFechaReferencia($documento);
        $referenciaLugarPublicacion = self::obtenerLugarPublicacionReferenia($documento);

        if ($referenciaLugarPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaLugarPublicacion . " ";
        }
        if ($referenciaFechPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaFechPublicacion . "";
        }

        Log::warning($referenciaReal);
        return $referenciaReal.".";
    }
    //Obtiene todos los datos y retorna la referencia ya construida como cadena
    public function construirReferenciaProyectos($documento, $autores, $editores)
    {
        $referenciaReal = "";

        $referenciaTitulo = "\"" . $documento->titulo . "\"";

        $referenciaAutor = self::construirReferenciaAutores($autores,$documento);
        $referenciaEditorial = self::obtenerEditoresReferencia($editores);

        $referenciaFechPublicacion = self::obtenerFechaReferencia($documento);
        $referenciaLugarPublicacion = self::obtenerLugarPublicacionReferenia($documento);

        if ($referenciaAutor != "") {
            $referenciaReal = $referenciaReal . $referenciaAutor . ", ";
        }
        if ($referenciaTitulo != "") {
            $referenciaReal = $referenciaReal . $referenciaTitulo . ", ";
        }
        if ($referenciaLugarPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaLugarPublicacion . " ";
        }
        if ($referenciaEditorial != "") {
            $referenciaReal = $referenciaReal . $referenciaEditorial . ", ";
        }
        if ($referenciaFechPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaFechPublicacion . "";
        }

        Log::warning($referenciaReal);
        return $referenciaReal.".";
    }
    public function construirReferenciaProyectos1($documento, $autores, $editores)
    {
        $referenciaReal = "";
        $referenciaAutor = self::construirReferenciaAutores($autores,$documento);

        if ($referenciaAutor != "") {
            $referenciaReal = $referenciaReal . $referenciaAutor . ", ";
        }
        Log::warning($referenciaReal);
        return $referenciaReal;
    }
    public function construirReferenciaProyectos2($documento, $autores, $editores)
    {
        $referenciaReal = "";
        $referenciaTitulo = "" . $documento->titulo . "";

        if ($referenciaTitulo != "") {
            $referenciaReal = $referenciaReal . $referenciaTitulo . ", ";
        }
        Log::warning($referenciaReal);
        return $referenciaReal;
    }
    public function construirReferenciaProyectos3($documento, $autores, $editores)
    {
        $referenciaReal = "";

        $referenciaEditorial = self::obtenerEditoresReferencia($editores);

        $referenciaFechPublicacion = self::obtenerFechaReferencia($documento);
        $referenciaLugarPublicacion = self::obtenerLugarPublicacionReferenia($documento);

        if ($referenciaLugarPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaLugarPublicacion . " ";
        }
        if ($referenciaEditorial != "") {
            $referenciaReal = $referenciaReal . $referenciaEditorial . ", ";
        }
        if ($referenciaFechPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaFechPublicacion . "";
        }

        Log::warning($referenciaReal);
        return $referenciaReal.'.';
    }

    //Obtiene todos los datos y retorna la referencia ya construida como cadena
    public function construirReferenciaTesis($documento, $autores, $editores, $tesis)
    {
        $referenciaReal = "";

        $referenciaTitulo = "\"" . $documento->titulo . "\"";

        $referenciaAutor = self::construirReferenciaAutores($autores,$documento);
        $referenciaEditorial = self::obtenerEditoresReferencia($editores);

        $referenciaFechPublicacion = self::obtenerFechaReferencia($documento);
        $referenciaLugarPublicacion = self::obtenerLugarPublicacionReferenia($documento);

        if ($referenciaAutor != "") {
            $referenciaReal = $referenciaReal . $referenciaAutor . ", ";
        }
        if ($referenciaTitulo != "") {
            if($tesis->grado!=""){
                $referenciaReal = $referenciaReal . $referenciaTitulo ."(".$tesis->grado.")". ", ";
            }else{
                $referenciaReal = $referenciaReal . $referenciaTitulo . ", ";
            }

        }
        if ($referenciaLugarPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaLugarPublicacion . " ";
        }
        if ($referenciaEditorial != "") {
            $referenciaReal = $referenciaReal . $referenciaEditorial . ", ";
        }
        if ($referenciaFechPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaFechPublicacion . ", ";
        }
        $referenciaReal = $referenciaReal . $tesis->num_paginas . "";

        Log::warning($referenciaReal);
        return $referenciaReal.".";
    }
    public function construirReferenciaTesis1($documento, $autores, $editores, $tesis)
    {
        $referenciaReal = "";

        $referenciaAutor = self::construirReferenciaAutores($autores,$documento);
        if ($referenciaAutor != "") {
            $referenciaReal = $referenciaReal . $referenciaAutor . ", ";
        }
        Log::warning($referenciaReal);
        return $referenciaReal;
    }
    public function construirReferenciaTesis2($documento, $autores, $editores, $tesis)
    {
        $referenciaReal = "";
        $referenciaTitulo = "" . $documento->titulo . "";

        if ($referenciaTitulo != "") {
            if($tesis->grado!=""){
                $referenciaReal = $referenciaReal . BBCode::parse('[i]'.$referenciaTitulo."[/i]") ." (".$tesis->grado.")". ", ";
            }else{
                $referenciaReal = $referenciaReal .  BBCode::parse('[i]'.$referenciaTitulo ."[/i]"). ", ";
            }

        }
        Log::warning($referenciaReal);
        return $referenciaReal;
    }
    public function construirReferenciaTesis3($documento, $autores, $editores, $tesis)
    {
        $referenciaReal = "";
        $referenciaEditorial = self::obtenerEditoresReferencia($editores);
        $referenciaFechPublicacion = self::obtenerFechaReferencia($documento);
        $referenciaLugarPublicacion = self::obtenerLugarPublicacionReferenia($documento);

        if ($referenciaLugarPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaLugarPublicacion . " ";
        }
        if ($referenciaEditorial != "") {
            $referenciaReal = $referenciaReal . $referenciaEditorial . ", ";
        }
        if ($referenciaFechPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaFechPublicacion . ", ";
        }
        $referenciaReal = $referenciaReal . $tesis->num_paginas . "";

        Log::warning($referenciaReal);
        return $referenciaReal.".";
    }
    //Obtiene todos los datos y retorna la referencia ya construida como cadena
    public function construirReferenciaCapituloLibro($documento, $autores, $editores, $capituloLibro)
    {
        $referenciaReal = "";

        $referenciaTitulo = "\"" . $documento->titulo . "\"";

        $referenciaAutor = self::construirReferenciaAutores($autores,$documento);
        $referenciaEditorial = self::obtenerEditoresReferencia($editores);

        $referenciaFechPublicacion = self::obtenerFechaReferencia($documento);
        $referenciaLugarPublicacion = self::obtenerLugarPublicacionReferenia($documento);

        if ($referenciaAutor != "") {
            $referenciaReal = $referenciaReal . $referenciaAutor . ", ";
        }
        if ($referenciaTitulo != "") {
            $referenciaReal = $referenciaReal . $referenciaTitulo . ", ";
        }

        if ($capituloLibro->autorgral != "") {
            $referenciaReal = $referenciaReal . "en " . $capituloLibro->autorgral . ", ";
        }
        if ($capituloLibro->nombre_libro != "") {
            $referenciaReal = $referenciaReal . "" . $capituloLibro->nombre_libro . ", ";
        }
        if ($capituloLibro->edicion != "") {
            $referenciaReal = $referenciaReal . "" . $capituloLibro->edicion . ", ";
        }
        if ($capituloLibro->traductor != "") {
            $referenciaReal = $referenciaReal . "Trad. " . $capituloLibro->traductor . ", ";
        }
        if ($referenciaLugarPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaLugarPublicacion . " ";
        }
        if ($referenciaEditorial != "") {
            $referenciaReal = $referenciaReal . $referenciaEditorial . ", ";
        }
        if ($referenciaFechPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaFechPublicacion . "";
        }
        if ($capituloLibro->tomos != "") {
            $referenciaReal = $referenciaReal . ", ". $capituloLibro->tomos  . "";
        }
        if ($capituloLibro->volumen != "") {
            $referenciaReal = $referenciaReal . ", " . $capituloLibro->volumen . "";
        }
        if ($capituloLibro->paginas != "") {
            $referenciaReal = $referenciaReal . ", " . $capituloLibro->paginas . "";
        }

        $coleccion= $capituloLibro->coleccion;
        $noCol = $capituloLibro->nocol;
        $serie = $capituloLibro->serie;
        $noSerie = $capituloLibro->noserie;
        //COleccion y Serie
        if ($coleccion != "" && $noCol != "" && $serie != "" && $noSerie != "") {// Coleccion y Serie 4 tienen datos
            $referenciaReal = $referenciaReal . '(Col.' . $coleccion . ', ' . $noCol . ', Serie ' . $serie . ', ' . $noSerie . ')';
        } else if ($coleccion != "" && $noCol != "" && $serie == "" && $noSerie == "") {//solo colección y no. coleccion tiene datos
            $referenciaReal = $referenciaReal . '(Col. ' . $coleccion . ', ' . $noCol . ')';
        } else if ($coleccion == "" && $noCol == "" && $serie != "" && $noSerie != "") {//Solo seríe y no. de serie tiene datos
            $referenciaReal = $referenciaReal . '(Serie ' . $serie . ', ' . $noSerie . ')';
        } else if ($coleccion != "" && $noCol == "" && $serie != "" && $noSerie == "") {//solo coleccion y serie tiene datos
            $referenciaReal = $referenciaReal . '(Col. ' . $coleccion . ', Serie ' . $serie . ')';
        } else if ($coleccion != "" && $noCol == "" && $serie == "" && $noSerie == "") {//Solo colección
            $referenciaReal = $referenciaReal . '(Col. ' . $coleccion . ')';
        } else if ($coleccion == "" && $noCol == "" && $serie != "" && $noSerie == "") {//Solo Serie
            $referenciaReal = $referenciaReal . '(Serie ' . $serie . ')';
        }

        Log::warning($referenciaReal);
        return $referenciaReal.".";
    }
    public function construirReferenciaCapituloLibro1($documento, $autores, $editores, $capituloLibro)
    {
        $referenciaReal = "";
        $referenciaTitulo = "\"" . $documento->titulo . "\"";
        $referenciaAutor = self::construirReferenciaAutores($autores,$documento);

        if ($referenciaAutor != "") {
            $referenciaReal = $referenciaReal . $referenciaAutor . ", ";
        }
        if ($referenciaTitulo != "") {
            $referenciaReal = $referenciaReal . $referenciaTitulo . ", en ";
        }
        if ($capituloLibro->autorgral != "") {
            $referenciaReal = $referenciaReal  . $capituloLibro->autorgral . ", ";
        }

        Log::warning($referenciaReal);
        return $referenciaReal;
    }
    public function construirReferenciaCapituloLibro2($documento, $autores, $editores, $capituloLibro)
    {
        $referenciaReal = "";
        if ($capituloLibro->nombre_libro != "") {
            $referenciaReal = $referenciaReal . "" . $capituloLibro->nombre_libro . ", ";
        }
        Log::warning($referenciaReal);
        return $referenciaReal;
    }
    public function construirReferenciaCapituloLibro3($documento, $autores, $editores, $capituloLibro)
    {
        $referenciaReal = "";

        $referenciaEditorial = self::obtenerEditoresReferencia($editores);

        $referenciaFechPublicacion = self::obtenerFechaReferencia($documento);
        $referenciaLugarPublicacion = self::obtenerLugarPublicacionReferenia($documento);

        if ($capituloLibro->edicion != "") {
            $referenciaReal = $referenciaReal . "" . $capituloLibro->edicion . ", ";
        }
        if ($capituloLibro->traductor != "") {
            $referenciaReal = $referenciaReal . "Trad. " . $capituloLibro->traductor . ", ";
        }
        if ($referenciaLugarPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaLugarPublicacion . " ";
        }
        if ($referenciaEditorial != "") {
            $referenciaReal = $referenciaReal . $referenciaEditorial . ", ";
        }
        if ($referenciaFechPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaFechPublicacion . "";
        }
        if ($capituloLibro->tomos != "") {
            $referenciaReal = $referenciaReal . ", ". $capituloLibro->tomos  . "";
        }
        if ($capituloLibro->volumen != "") {
            $referenciaReal = $referenciaReal . ", " . $capituloLibro->volumen . "";
        }
        if ($capituloLibro->paginas != "") {
            $referenciaReal = $referenciaReal . ", " . $capituloLibro->paginas . "";
        }

        $coleccion= $capituloLibro->coleccion;
        $noCol = $capituloLibro->nocol;
        $serie = $capituloLibro->serie;
        $noSerie = $capituloLibro->noserie;
        //COleccion y Serie
        if ($coleccion != "" && $noCol != "" && $serie != "" && $noSerie != "") {// Coleccion y Serie 4 tienen datos
            $referenciaReal = $referenciaReal . ' (Col.' . $coleccion . ', ' . $noCol . ', Serie ' . $serie . ', ' . $noSerie . ')';
        } else if ($coleccion != "" && $noCol != "" && $serie == "" && $noSerie == "") {//solo colección y no. coleccion tiene datos
            $referenciaReal = $referenciaReal . ' (Col. ' . $coleccion . ', ' . $noCol . ')';
        } else if ($coleccion == "" && $noCol == "" && $serie != "" && $noSerie != "") {//Solo seríe y no. de serie tiene datos
            $referenciaReal = $referenciaReal . ' (Serie ' . $serie . ', ' . $noSerie . ')';
        } else if ($coleccion != "" && $noCol == "" && $serie != "" && $noSerie == "") {//solo coleccion y serie tiene datos
            $referenciaReal = $referenciaReal . ' (Col. ' . $coleccion . ', Serie ' . $serie . ')';
        } else if ($coleccion != "" && $noCol == "" && $serie == "" && $noSerie == "") {//Solo colección
            $referenciaReal = $referenciaReal . ' (Col. ' . $coleccion . ')';
        } else if ($coleccion == "" && $noCol == "" && $serie != "" && $noSerie == "") {//Solo Serie
            $referenciaReal = $referenciaReal . ' (Serie ' . $serie . ')';
        }else if($coleccion == "" && $noCol == "" && $serie == "" && $noSerie == ""){
            $referenciaReal = $referenciaReal . '.';
        }

        
        return $referenciaReal."";
    }

    //Obtiene todos los datos y retorna la referencia ya construida como cadena
    public function construirReferenciaArticulosBoletines($documento, $autores, $editores, $revistaBoletin)
    {
        $referenciaReal = "";

        $referenciaTitulo = "\"" . $documento->titulo . "\"";

        $referenciaAutor = self::construirReferenciaAutores($autores,$documento);
        $referenciaEditorial = self::obtenerEditoresReferencia($editores);

        $referenciaFechPublicacion = self::obtenerFechaReferencia($documento);
        $referenciaLugarPublicacion = self::obtenerLugarPublicacionReferenia($documento);

        if ($referenciaAutor != "") {
            $referenciaReal = $referenciaReal . $referenciaAutor . ", ";
        }
        if ($referenciaTitulo != "") {
            $referenciaReal = $referenciaReal . $referenciaTitulo . ", ";
        }
        if ($revistaBoletin->nombre_revista != "") {
            $referenciaReal = $referenciaReal . $revistaBoletin->nombre_revista . ", ";
        }
        if ($revistaBoletin->anio != "") {
            $referenciaReal = $referenciaReal . $revistaBoletin->anio . ", ";
        }
        if ($revistaBoletin->volumen != "") {
            $referenciaReal = $referenciaReal . $revistaBoletin->volumen . ", ";
        }
        if ($revistaBoletin->num_revista != "") {
            $referenciaReal = $referenciaReal . $revistaBoletin->num_revista . ", ";
        }
        if ($referenciaLugarPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaLugarPublicacion . " ";
        }
        if ($referenciaEditorial != "") {
            $referenciaReal = $referenciaReal . $referenciaEditorial . ", ";
        }
        if ($referenciaFechPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaFechPublicacion . "";
        }
        if ($revistaBoletin->pag != "") {
            $referenciaReal = $referenciaReal . $revistaBoletin->pag . ", ";
        }
        //$referenciaReal =$referenciaAutor.", ".$referenciaTitulo.", ".$referenciaEditorial.", ".$referenciaLugarPublicacion.", ".$referenciaFechPublicacion;
        Log::warning($referenciaReal);
        return $referenciaReal;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $documento=Documento::findOrFail($id);


        $documento->fecha_consulta = Carbon::parse($documento->fecha_consulta)->format('d/m/Y'); // Cambio de formato a la fecha de consulta con ayuda del componente CARBON Laravel



        //Log::warning(self::obtenerFechaReferencia($documento)); //// fecha extraida...... ver log

        //Log::warning(self::obtenerFechaConsulta($documento)); //// fecha extraida...... ver log




        //LUGAR DE PUBLICACION
        $lugarPublicacion=$documento->lugar_public_edo . " " . $documento->lugar_public_pais;

        //DERECHOS DE AUTOR
        if($documento->derecho_autor==0)
            $derecho_autorConsulta = 'No';
        else if ($documento->derecho_autor==1)
            $derecho_autorConsulta = 'Sí';

        //NOTAS
        if($documento->notas=='')
            $notas = '- - - - - - - -';
        else
            $notas = $documento->notas;


        //POBLACION
        if ($documento->poblacion==1)
        $poblacion = 'Afrodescendiente';
        else if ($documento->poblacion==2)
        $poblacion = 'Indígena';
        else if ($documento->poblacion==3)
        $poblacion = 'Afrodescendiente e Indígena';
        else if ($documento->poblacion==0)
        $poblacion = '- - - - - - - -';

        //ESTADO DE LA REVISION
        if ($documento->revisado==0)
        $revisado= ' Referencia Pendiente de revisión';
        else if ($documento->revisado==1)
        $revisado= 'Referencia Revisada';

        //PUBLICADO

        if($documento->linea==1)
        $linea='En Linea';
        else
        $linea='No establecido';




        $actoresSociales = DB::table('persona as p')
        ->join('cntrl_persona as cp','cp.fk_persona',"=",'p.Id_persona')
        ->join('documento as d','d.Id_doc',"=",'cp.fk_doc')
        ->where('fk_doc',$documento->Id_doc)
        ->get();

        $instituciones =DB::table('institucion as i')
        ->join('cntrl_instit as cp','cp.fk_instit',"=",'i.Id_institucion')
        ->join('documento as d','d.Id_doc',"=",'cp.fk_doc')
        ->where('fk_doc',$documento->Id_doc)
        ->get();

        $temas =DB::table('temas as t')
        ->join('cntrl_tema as cp','cp.fk_tema',"=",'t.id_tema')
        ->join('documento as d','d.Id_doc',"=",'cp.fk_doc')
        ->where('fk_doc',$documento->Id_doc)
        ->get();

        $lugares =DB::table('lugar as l')
        ->join('cntrl_lugar as cp','cp.fk_lugar',"=",'l.id_lugar')
        ->join('paises as p','p.id_pais',"=",'l.pais')
        ->join('region as r','r.id_region',"=",'l.region_geografica')
        ->join('documento as d','d.Id_doc',"=",'cp.fk_doc')
        ->select('l.id_lugar as id','l.ubicacion as ubicacion','p.nombre as pais' ,'r.nombrereg as region')
        ->where('fk_doc',$documento->Id_doc)
        ->get();

        $subtemas =DB::table('subtema as sub')
        ->join('cntrl_sub as cp','cp.fk_sub',"=",'sub.id_sub')
        ->join('documento as d','d.Id_doc',"=",'cp.fk_doc')
        ->where('fk_doc',$documento->Id_doc)
        ->get();

        $obras =DB::table('obras as obra')
        ->join('obra_doc as cp','cp.fk_obra',"=",'obra.id_obra')
        ->join('documento as d','d.Id_doc',"=",'cp.fk_doc')
        ->where('fk_doc',$documento->Id_doc)
        ->get();

        $autores =DB::table('autor as a')
        ->join('cntrl_autor as ca','ca.fk_autor',"=",'a.Id_autor')
        ->join('documento as d','d.Id_doc',"=",'ca.fk_doc')
        ->where('fk_doc',$documento->Id_doc)
        ->orderBy('orden', 'asc')
        ->get();
        Log::warning($autores);


        $editores =DB::table('editor as e')
        ->join('cntrl_editor as ce','ce.fk_editor',"=",'e.Id_editor')
        ->join('documento as d','d.Id_doc',"=",'ce.fk_doc')
        ->where('fk_doc',$documento->Id_doc)
        ->get();

        //FECHA

        $fecha=null;
        $fechaExtra=null;

        $documento->fecha_publi==1?$fecha=FechaNormalController::obtenerFechaNormal($id): $fechaExtra=FechaExtraController::obtenerFechaExtra($id);


    


        $proyectos =DB::table('catalogo_proyecto as cat')
        ->join('cntrl_proyec as cp','cp.fk_proyec',"=",'cat.id_proyecto')
        ->join('documento as d','d.Id_doc',"=",'cp.fk_doc')
        ->select('cat.proyecto as proyecto','cat.id_proyecto as id')
        ->where('fk_doc',$documento->Id_doc)
        ->get();
        //Obetner tipo de documento
        $tipoDocumento = self::obtenerTipoDocumento($documento->tipo, $id);

        $referencia="";




         //TIPO DE DOCUMENTO
        if ($documento->tipo == 1){
            $tipoConsulta = 'Artículo';
            //Aqui se contruye la referencia
            $referencia = self::construirReferenciaArticulo1($documento, $autores, $editores).
            self::construirReferenciaArticulo2($documento, $autores, $editores).
            BBCode::parse('[i]'.self::construirReferenciaArticulo3($documento, $autores, $editores).'[/i]'. // seccion que coloca el texto en italic
             self::construirReferenciaArticulo4($documento, $autores, $editores));

        }
        else if ($documento->tipo == 2){
            $tipoConsulta = 'Boletines';
           // $referencia = self::construirReferenciaBoletinRevista($documento,$autores,$editores);

            $referencia = self::construirReferenciaBoletinRevista1($documento, $autores, $editores).
            BBCode::parse('[i]'.self::construirReferenciaBoletinRevista2($documento, $autores, $editores).'[/i]'. // seccion que coloca el texto en italic
            self::construirReferenciaBoletinRevista3($documento, $autores, $editores).
            self::construirReferenciaBoletinRevista4($documento, $autores, $editores).
            self::construirReferenciaBoletinRevista5($documento, $autores, $editores));
           Log::warning($referencia);
        }
        else if ($documento->tipo == 3){
            $tipoConsulta = 'Cartas y Oficios';


            $referencia = self::construirReferenciaCartasOficios1($documento, $autores, $editores).
            BBCode::parse('[i]'.self::construirReferenciaCartasOficios2($documento, $autores, $editores).'[/i]'. // seccion que coloca el texto en italic
            self::construirReferenciaCartasOficios3($documento, $autores, $editores));
        }
        else if ($documento->tipo == 4){
            $tipoConsulta = 'Crónicas';

            $referencia = self::construirReferenciaCronica1($documento, $autores, $editores).
            BBCode::parse(''.self::construirReferenciaCronica2($documento, $autores, $editores).''.// seccion que coloca el texto en italic
            self::construirReferenciaCronica3($documento, $autores, $editores));
        }
        else if ($documento->tipo == 5){
            $tipoConsulta = 'Declaraciones y Comunicados';


            $referencia = self::construirReferenciaDeclaracionesComunicados1($documento, $autores, $editores).
            BBCode::parse('[i]'.self::construirReferenciaDeclaracionesComunicados2($documento, $autores, $editores).'[/i]'.// seccion que coloca el texto en italic
            self::construirReferenciaDeclaracionesComunicados3($documento, $autores, $editores));
        }
        else if ($documento->tipo == 6)
        {
            $tipoConsulta = 'Discursos';


            $referencia = self::construirReferenciaDiscurso1($documento, $autores, $editores).
            BBCode::parse('[i]'.self::construirReferenciaDiscurso2($documento, $autores, $editores).'[/i]'.// seccion que coloca el texto en italic
            self::construirReferenciaDiscurso3($documento, $autores, $editores));
        }

        else if ($documento->tipo == 7){
            $tipoConsulta = 'Informes';


            $referencia = self::construirReferenciaInformes1($documento, $autores, $editores).
            BBCode::parse('[i]'.self::construirReferenciaInformes2($documento, $autores, $editores).'[/i]'.// seccion que coloca el texto en italic
            self::construirReferenciaInformes3($documento, $autores, $editores));
        }
        else if ($documento->tipo == 8){
            $tipoConsulta = 'Libros';


            $referencia = self::construirReferenciaLibro1($documento, $autores, $editores).
            BBCode::parse('[i]'.self::construirReferenciaLibro2($documento, $autores, $editores).'[/i]'.// seccion que coloca el texto en italic
            self::construirReferenciaLibro3($documento, $autores, $editores));


        }

        else if ($documento->tipo == 9){
            $tipoConsulta = 'Notas';


            $referencia = self::construirReferenciaNotas1($documento, $autores, $editores).
            self::construirReferenciaNotas2($documento, $autores, $editores).
            self::construirReferenciaNotas3($documento, $autores, $editores);

        }
        else if ($documento->tipo == 10){
            $tipoConsulta = 'Ponencias';


            $referencia = self::construirReferenciaPonencia1($documento, $autores, $editores).
            BBCode::parse('[i]'.self::construirReferenciaPonencia2($documento, $autores, $editores).'[/i]'.// seccion que coloca el texto en italic
            self::construirReferenciaPonencia3($documento, $autores, $editores));
        }

        else if ($documento->tipo == 11){
            $tipoConsulta = 'Proyectos';


            $referencia = self::construirReferenciaProyectos1($documento, $autores, $editores).
            BBCode::parse('[i]'.self::construirReferenciaProyectos2($documento, $autores, $editores).'[/i]'.// seccion que coloca el texto en italic
            self::construirReferenciaProyectos3($documento, $autores, $editores));
        }
        else if ($documento->tipo == 12){
            $tipoConsulta = 'Otros';


            $referencia = self::construirReferenciaOtros1($documento, $autores, $editores).
            self::construirReferenciaOtros2($documento, $autores, $editores).// seccion que coloca el texto en italic
            self::construirReferenciaOtros3($documento, $autores, $editores);

        }

        else if ($documento->tipo == 13){
            $tipoConsulta = 'Tesis';


            $referencia = self::construirReferenciaTesis1($documento, $autores, $editores,$tipoDocumento).
            self::construirReferenciaTesis2($documento, $autores, $editores,$tipoDocumento).// seccion que coloca el texto en italic
            self::construirReferenciaTesis3($documento, $autores, $editores,$tipoDocumento);
        }
        else if ($documento->tipo == 14){
            $tipoConsulta = 'Artículo de Revista';


            $referencia = self::construirReferenciaArticuloBoletinRevista1($documento, $autores, $editores,$tipoDocumento).
            self::construirReferenciaArticuloBoletinRevista2($documento, $autores, $editores,$tipoDocumento).
            BBCode::parse('[i]'.self::construirReferenciaArticuloBoletinRevista3($documento, $autores, $editores,$tipoDocumento).'[/i]'.// seccion que coloca el texto en italic
            self::construirReferenciaArticuloBoletinRevista4($documento, $autores, $editores,$tipoDocumento));

        }

        else if ($documento->tipo == 15){
            $tipoConsulta = 'Capítulo de Libros';


            $referencia = self::construirReferenciaCapituloLibro1($documento, $autores, $editores,$tipoDocumento).
            BBCode::parse('[i]'.self::construirReferenciaCapituloLibro2($documento, $autores, $editores,$tipoDocumento).'[/i]'.
            self::construirReferenciaCapituloLibro3($documento, $autores, $editores,$tipoDocumento));
        }
        else if ($documento->tipo == 16){
            $tipoConsulta = 'Videos';

            $tituloAux  = preg_replace("/[\r\n|\n|\r]+/", " ", $documento->titulo);
        
            $referencia = "Título y Subtítulo: ".
            BBCode::parse('[b][i]'.$tituloAux.'<br><br>'.'[/b][/i]'.self::construirReferenciaVideo($documento,$editores));
        
        }
            
        else if ($documento->tipo == 17){
            $tipoConsulta = 'Revistas';
            //:´v why?
            //$referencia = self::construirReferenciaArticulosBoletines($documento, $autores, $editores,$tipoDocumento);
            //$referencia = self::construirReferenciaBoletinRevista($documento,$autores,$editores);

            $referencia = self::construirReferenciaBoletinRevista1($documento, $autores, $editores).
            BBCode::parse('[i]'.self::construirReferenciaBoletinRevista2($documento, $autores, $editores).'[/i]'.// seccion que coloca el texto en italic
            self::construirReferenciaBoletinRevista3($documento, $autores, $editores).
            self::construirReferenciaBoletinRevista4($documento, $autores, $editores).
            self::construirReferenciaBoletinRevista5($documento, $autores, $editores));

            Log::warning($referencia);
        }

        else if ($documento->tipo == 18){
            //$referencia = self::construirReferenciaArticuloBoletinRevista($documento, $autores, $editores,$tipoDocumento);

            $referencia = self::construirReferenciaArticuloBoletinRevista1($documento, $autores, $editores,$tipoDocumento).
            self::construirReferenciaArticuloBoletinRevista2($documento, $autores, $editores,$tipoDocumento).
            BBCode::parse('[i]'.self::construirReferenciaArticuloBoletinRevista3($documento, $autores, $editores,$tipoDocumento).'[/i]'.
            self::construirReferenciaArticuloBoletinRevista4($documento, $autores, $editores,$tipoDocumento));
            $tipoConsulta = 'Artículos de Boletín';


        }

        else if ($documento == null)
            $tipoConsulta = '- - - - - - -';






       //Log::warning(self::construirReferenciaTemas($temas));

        /*
        Log::warning(self::construirReferenciaAutores($autores,$documento));
        Log::warning(self::construirReferenciaTemas($temas));
        Log::warning(self::construirReferenciaPoblacion($poblacion));
        Log::warning(self::construirReferenciaArticulo($documento, $autores, $editores));

        Log::warning(self::obtenerEditoresReferencia($editores));
         */


       return view('documento.verDetalle', [
           "documento" => $documento,
           "referencia" => $referencia,
           "tipo"=>$tipoConsulta,
           "derechos" => $derecho_autorConsulta,
           "lugarPublicacion"=>$lugarPublicacion,
           "notas"=>$notas,
           "poblacion"=>$poblacion,
           "revisado"=>$revisado,
           "linea"=>$linea,
           "actoresSociales"=>$actoresSociales,
           "instituciones"=>$instituciones,
           "temas"=>$temas,
           "lugares"=>$lugares,
           "subtemas"=>$subtemas,
           "obras"=>$obras,
           "autores"=>$autores,
           "editores"=>$editores,
           "proyectos"=>$proyectos,
           "fecha"=>$fecha,
           "fechaExtra"=>$fechaExtra,
           "tipoDocumento"=>$tipoDocumento


           ]);
    }

    public function getReferencia($id){
        $documento = Documento::findOrFail($id);
        $documento->fecha_consulta = Carbon::parse($documento->fecha_consulta)->format('d/m/Y'); // Cambio de formato a la fecha de consulta con ayuda del componente CARBON Laravel


        $autores =DB::table('autor as a')
        ->join('cntrl_autor as ca','ca.fk_autor',"=",'a.Id_autor')
        ->join('documento as d','d.Id_doc',"=",'ca.fk_doc')
        ->where('fk_doc',$documento->Id_doc)
        ->orderBy('orden', 'asc')
        ->get();

        $editores =DB::table('editor as e')
        ->join('cntrl_editor as ce','ce.fk_editor',"=",'e.Id_editor')
        ->join('documento as d','d.Id_doc',"=",'ce.fk_doc')
        ->where('fk_doc',$documento->Id_doc)
        ->get();

        $tipoDocumento = self::obtenerTipoDocumento($documento->tipo, $id);


        $referencia = "";


         //TIPO DE DOCUMENTO
         if ($documento->tipo == 1){
            $tipoConsulta = 'Artículo';
            //Aqui se contruye la referencia
            $referencia = self::construirReferenciaArticulo1($documento, $autores, $editores).
            self::construirReferenciaArticulo2($documento, $autores, $editores).
            BBCode::parse('[i]'.self::construirReferenciaArticulo3($documento, $autores, $editores).'[/i]'. // seccion que coloca el texto en italic
            self::construirReferenciaArticulo4($documento, $autores, $editores));

        }
        else if ($documento->tipo == 2){
            $tipoConsulta = 'Boletines';
        // $referencia = self::construirReferenciaBoletinRevista($documento,$autores,$editores);

            $referencia = self::construirReferenciaBoletinRevista1($documento, $autores, $editores).
            BBCode::parse('[i]'.self::construirReferenciaBoletinRevista2($documento, $autores, $editores).'[/i]'. // seccion que coloca el texto en italic
            self::construirReferenciaBoletinRevista3($documento, $autores, $editores).
            self::construirReferenciaBoletinRevista4($documento, $autores, $editores).
            self::construirReferenciaBoletinRevista5($documento, $autores, $editores));
        Log::warning($referencia);
        }
        else if ($documento->tipo == 3){
            $tipoConsulta = 'Cartas y Oficios';


            $referencia = self::construirReferenciaCartasOficios1($documento, $autores, $editores).
            BBCode::parse('[i]'.self::construirReferenciaCartasOficios2($documento, $autores, $editores).'[/i]'. // seccion que coloca el texto en italic
            self::construirReferenciaCartasOficios3($documento, $autores, $editores));
        }
        else if ($documento->tipo == 4){
            $tipoConsulta = 'Crónicas';

            $referencia = self::construirReferenciaCronica1($documento, $autores, $editores).
            BBCode::parse(''.self::construirReferenciaCronica2($documento, $autores, $editores).''.// seccion que coloca el texto en italic
            self::construirReferenciaCronica3($documento, $autores, $editores));
        }
        else if ($documento->tipo == 5){
            $tipoConsulta = 'Declaraciones y Comunicados';


            $referencia = self::construirReferenciaDeclaracionesComunicados1($documento, $autores, $editores).
            BBCode::parse('[i]'.self::construirReferenciaDeclaracionesComunicados2($documento, $autores, $editores).'[/i]'.// seccion que coloca el texto en italic
            self::construirReferenciaDeclaracionesComunicados3($documento, $autores, $editores));
        }
        else if ($documento->tipo == 6)
        {
            $tipoConsulta = 'Discursos';


            $referencia = self::construirReferenciaDiscurso1($documento, $autores, $editores).
            BBCode::parse('[i]'.self::construirReferenciaDiscurso2($documento, $autores, $editores).'[/i]'.// seccion que coloca el texto en italic
            self::construirReferenciaDiscurso3($documento, $autores, $editores));
        }

        else if ($documento->tipo == 7){
            $tipoConsulta = 'Informes';


            $referencia = self::construirReferenciaInformes1($documento, $autores, $editores).
            BBCode::parse('[i]'.self::construirReferenciaInformes2($documento, $autores, $editores).'[/i]'.// seccion que coloca el texto en italic
            self::construirReferenciaInformes3($documento, $autores, $editores));
        }
        else if ($documento->tipo == 8){
            $tipoConsulta = 'Libros';


            $referencia = self::construirReferenciaLibro1($documento, $autores, $editores).
            BBCode::parse('[i]'.self::construirReferenciaLibro2($documento, $autores, $editores).'[/i]'.// seccion que coloca el texto en italic
            self::construirReferenciaLibro3($documento, $autores, $editores));


        }

        else if ($documento->tipo == 9){
            $tipoConsulta = 'Notas';


            $referencia = self::construirReferenciaNotas1($documento, $autores, $editores).
            BBCode::parse('[i]'.self::construirReferenciaNotas2($documento, $autores, $editores).'[/i]'.// seccion que coloca el texto en italic
            self::construirReferenciaNotas3($documento, $autores, $editores));

        }
        else if ($documento->tipo == 10){
            $tipoConsulta = 'Ponencias';


            $referencia = self::construirReferenciaPonencia1($documento, $autores, $editores).
            BBCode::parse('[i]'.self::construirReferenciaPonencia2($documento, $autores, $editores).'[/i]'.// seccion que coloca el texto en italic
            self::construirReferenciaPonencia3($documento, $autores, $editores));
        }

        else if ($documento->tipo == 11){
            $tipoConsulta = 'Proyectos';


            $referencia = self::construirReferenciaProyectos1($documento, $autores, $editores).
            BBCode::parse('[i]'.self::construirReferenciaProyectos2($documento, $autores, $editores).'[/i]'.// seccion que coloca el texto en italic
            self::construirReferenciaProyectos3($documento, $autores, $editores));
        }
        else if ($documento->tipo == 12){
            $tipoConsulta = 'Otros';


            $referencia = self::construirReferenciaOtros1($documento, $autores, $editores).
            BBCode::parse('[i]'.self::construirReferenciaOtros2($documento, $autores, $editores).'[/i]'.// seccion que coloca el texto en italic
            self::construirReferenciaOtros3($documento, $autores, $editores));

        }

        else if ($documento->tipo == 13){
            $tipoConsulta = 'Tesis';


            $referencia = self::construirReferenciaTesis1($documento, $autores, $editores,$tipoDocumento).
            self::construirReferenciaTesis2($documento, $autores, $editores,$tipoDocumento).// seccion que coloca el texto en italic
            self::construirReferenciaTesis3($documento, $autores, $editores,$tipoDocumento);
        }
        else if ($documento->tipo == 14){
            $tipoConsulta = 'Artículo de Revista';


            $referencia = self::construirReferenciaArticuloBoletinRevista1($documento, $autores, $editores,$tipoDocumento).
            self::construirReferenciaArticuloBoletinRevista2($documento, $autores, $editores,$tipoDocumento).
            BBCode::parse('[i]'.self::construirReferenciaArticuloBoletinRevista3($documento, $autores, $editores,$tipoDocumento).'[/i]'.// seccion que coloca el texto en italic
            self::construirReferenciaArticuloBoletinRevista4($documento, $autores, $editores,$tipoDocumento));

        }

        else if ($documento->tipo == 15){
            $tipoConsulta = 'Capítulo de Libros';


            $referencia = self::construirReferenciaCapituloLibro1($documento, $autores, $editores,$tipoDocumento).
            BBCode::parse('[i]'.self::construirReferenciaCapituloLibro2($documento, $autores, $editores,$tipoDocumento).'[/i]'.
            self::construirReferenciaCapituloLibro3($documento, $autores, $editores,$tipoDocumento));
        }
        
        else if ($documento->tipo == 16){
            $tipoConsulta = 'Videos';
            $tituloAux  = preg_replace("/[\r\n|\n|\r]+/", " ", $documento->titulo);
        
            $referencia = "Título y Subtítulo: ".
            BBCode::parse('[b][i]'.$tituloAux.'<br><br>'.'[/b][/i]'.self::construirReferenciaVideo($documento,$editores));
        }
           
        else if ($documento->tipo == 17){
            $tipoConsulta = 'Revistas';
            //:´v why?
            //$referencia = self::construirReferenciaArticulosBoletines($documento, $autores, $editores,$tipoDocumento);
            //$referencia = self::construirReferenciaBoletinRevista($documento,$autores,$editores);

            $referencia = self::construirReferenciaBoletinRevista1($documento, $autores, $editores).
            BBCode::parse('[i]'.self::construirReferenciaBoletinRevista2($documento, $autores, $editores).'[/i]'.// seccion que coloca el texto en italic
            self::construirReferenciaBoletinRevista3($documento, $autores, $editores).
            self::construirReferenciaBoletinRevista4($documento, $autores, $editores).
            self::construirReferenciaBoletinRevista5($documento, $autores, $editores));

            Log::warning($referencia);
        }

        else if ($documento->tipo == 18){
            //$referencia = self::construirReferenciaArticuloBoletinRevista($documento, $autores, $editores,$tipoDocumento);
            $referencia = self::construirReferenciaArticuloBoletinRevista1($documento, $autores, $editores,$tipoDocumento).
            self::construirReferenciaArticuloBoletinRevista2($documento, $autores, $editores,$tipoDocumento).
            BBCode::parse('[i]'.self::construirReferenciaArticuloBoletinRevista3($documento, $autores, $editores,$tipoDocumento).'[/i]'.
            self::construirReferenciaArticuloBoletinRevista4($documento, $autores, $editores,$tipoDocumento));
            $tipoConsulta = 'Artículos de Boletín';
        }

        return $referencia."";
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $documento =  Documento::findOrFail($id);
        $fecha=null;
        $fechaExtra=null;

        $categorias=DB::table('catalogo_docu')->get();

        $categorias = $categorias->filter(function($item) { //funcion que quita elemnto con id 18 (Videos)
          return $item->id_cata_doc != 16;
      });

        $mesesFecha = array('nombre'=>'Enero', 'Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');


        if($documento->fecha_publi==1){
            $fecha=FechaNormalController::obtenerFechaNormal($id);
            $fechaExtra = new FechaExtra();
            $fechaExtra->mes = '';
            $fechaExtra->mes2 = '';





        }else{
            $fechaExtra=FechaExtraController::obtenerFechaExtra($id);


        }


        //$documento->fecha_publi==1?$fecha=FechaNormalController::obtenerFechaNormal($id): $fechaExtra=FechaExtraController::obtenerFechaExtra($id);

        $tipoDocumento = self::obtenerTipoDocumento($documento->tipo,$id);

        return view('documento.edit', ["documento" => $documento,
        "fecha"=>$fecha,
        "fechaExtra"=>$fechaExtra,
        "mesesFecha"=>$mesesFecha,
        'categorias' => $categorias,
        'tipoDocumento'=>$tipoDocumento
        ]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DocumentoFormRequest $request, $id)
    {
        $documento = Documento::findOrFail($id);
        $documento->titulo = $request->get('titulo');
        $documento->lugar_public_pais = $request->get('lugar_public_pais');
        $documento->lugar_public_edo = $request->get('lugar_public_edo');
        $documento->derecho_autor = $request->get('derecho_autor');
        $documento->fecha_publi = $request->get('fecha_publi');
        $documento->url = $request->get('url');
        $documento->fecha_consulta = $request->get('fecha_consulta');
        $documento->poblacion = $request->get('poblacion');
        $documento->tipo = $request->get('tipo');
        $documento->notas = $request->get('notas')?$request->get('notas'):'';

            DB::beginTransaction();

            if($documento->save()){

               $fechaNormal = new FechaNormalController();
               $fechaExtra = new FechaExtraController();


               $fechaNormal->eliminarFecha($id);// elimina si existe
               $fechaExtra->eliminarFecha($id); //elimina de las tablas si exite




            if($documento->fecha_publi==1){
                $fechaNormal->agregarFechaNormal($id,$request);
            }else{

                $fechaExtra->agregarFechaExtra($id,$request);

            }

            self::borrarTipoDocumento($id);
            self::agregarTipoDocumento($id,$request->get('tipo'),$request);  ///llamada al metodo agregartipo documento

                //activar el log de la base de datos
                DB::connection()->enableQueryLog();

                DB::commit();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
           LogController::agregarLog(
               2,
               "Documento",
               "Se actualizó el documento: ". json_encode($documento)
           );



            }else{

                DB::rollback();
            }

            return Redirect::to('documento');




    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $documento=Documento::findOrFail($id);

      if(Auth::user()->name==$documento->investigador||Auth::user()->authorizeRoles(['admin', 'revisor'])){
        self::borrarTipoDocumento($id);

        $fechaNormal = new FechaNormalController();
        $fechaExtra = new FechaExtraController();

        $fechaNormal->eliminarFecha($id);// elimina si existe
        $fechaExtra->eliminarFecha($id); //elimina de las tablas si exite



        DB::connection()->enableQueryLog();
            DocumentoAutorController::eliminarDocumentoCascada($id);
            DocumentoEditorController::eliminarDocumentoCascada($id);
            DocumentoPersonaController::eliminarDocumentoCascada($id);
            DocumentoInstitucionController::eliminarDocumentoCascada($id);
            DocumentoTemaController::eliminarDocumentoCascada($id);
            DocumentoLugarController::eliminarDocumentoCascada($id);
            DocumentoSubtemaController::eliminarDocumentoCascada($id);
            DocumentoObraController::eliminarDocumentoCascada($id);
        $documento->delete();

        //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
        LogController::agregarLog(
            3,
            "Documento",
            "Se eliminó el documento: " . json_encode($documento)
        );
        
        Session::flash('message','¡El documento se ha eliminado!');


      }
      return Redirect::to('documento');
    }

    public function cambiarEstadoRevision($id){

        $documento=Documento::findOrFail($id);

        $documento->revisado ==1? $documento->revisado=0: $documento->revisado=1;



        if($documento->save()){

            DB::connection()->enableQueryLog();

            //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
        LogController::agregarLog(
           2,
           "Documento",
           "Se actualizó el estado del documento: ". json_encode($documento)
        );

        }
        return back();


    }


    private function agregarTipoDocumento($idDocumento,$idTipoDocumento,$request)
    {


        if ($idTipoDocumento == '14' || $idTipoDocumento == '18' ||  $idTipoDocumento == '2'  || $idTipoDocumento == '17') {
            //revista_boletin
            $revistaController = new RevistaBoletinController();
            $revistaController->agregar( $idDocumento,$request);
           // RevistaBoletinController::agregar(
              //  $idDocumento,
             //   $request
         //   );
        } elseif ($idTipoDocumento == "15") {

            $capLibroController = new CapituloLibroController();
            $capLibroController->agregar( $idDocumento,$request);



        } elseif ($idTipoDocumento == "13") {


            $tesisController = new TesisController();

            $tesisController->agregar( $idDocumento,$request);




        }  elseif ($idTipoDocumento == "10") {


            $ponenciaController = new PonenciaController();

            $ponenciaController->agregar( $idDocumento,$request);


        }
         elseif ($idTipoDocumento == "8") {

            $libroController = new LibroController();

            $libroController->agregar( $idDocumento,$request);

        }



    }

    private function obtenerTipoDocumento($idTipoDocumento,$idDocumento){




        if ($idTipoDocumento == '14' || $idTipoDocumento == '18' ||  $idTipoDocumento == '2'  || $idTipoDocumento == '17') {
            //revista_boletin

            return RevistaBoletinController::obtenerRevistBoletin($idDocumento);


        } elseif ($idTipoDocumento == "15") {

           return  CapituloLibroController::obtenerCapituloLibro($idDocumento);

        } elseif ($idTipoDocumento == "13") {

            return TesisController::obtenerTesis($idDocumento);


        }  elseif ($idTipoDocumento == "10") {


            return PonenciaController::obtenerPonencia($idDocumento);

        }
         elseif ($idTipoDocumento == "8") {
            return LibroController::obtenerLibro($idDocumento);
        }

    }


    private function borrarTipoDocumento($idDocumento){



            $revistaController = new RevistaBoletinController();

            $revistaController->eliminar( $idDocumento);

            $capLibroController = new CapituloLibroController();

            $capLibroController->eliminar( $idDocumento);

            $tesisController = new TesisController();

            $tesisController->eliminar( $idDocumento);

            $ponenciaController = new PonenciaController();

            $ponenciaController->eliminar( $idDocumento);

            $libroController = new LibroController();

            $libroController->eliminar( $idDocumento);


        }

        private function obtenerFechaReferencia($documento){

            $id=$documento->Id_doc;
            $fecha = null;
            $fechaExtra = null;
            $documento->fecha_publi==1?$fecha=FechaNormalController::obtenerFechaNormal($id): $fechaExtra=FechaExtraController::obtenerFechaExtra($id);


            if($fecha!=null){
              return   Utilidad::getFecha($fecha);
            }else{

                if($fechaExtra!=null){

                    return $fechaExtra->mes . '-' . $fechaExtra->mes2 . ' de ' . $fechaExtra->anio . '';

                }else{

                    return "[s.f.] ";
                }


            }


        }

        private function obtenerFechaConsulta($documento){


            return  Utilidad::getFechaConsulta($documento->fecha_consulta);

         }

         private function obtenerLugarPublicacionReferenia($documento){

            $estado = $documento->lugar_public_edo;

            $pais =  $documento->lugar_public_pais;

            if ($estado != "" && $pais != ""){
              return $estado . ', ' . $pais . ', ';
            } else if($estado != "" && $pais == ""){
                return $estado . ', ';
            }else if($estado == "" && $pais != ""){
                return $pais . ',';
            }else{
                return '[s.l.], ';
            }


        }

        private function obtenerEditoresReferencia($editores){

              $editor='';

            for($i=0;$i<sizeof($editores);$i++){



                if($i==(sizeof($editores)-1)){
                    $editor=$editor . $editores[$i]->editor. "";


                }

                else{
                    $editor= $editor . $editores[$i]->editor.' - ';

                }

            }
           return $editor==''?$editor='[s.e.]':$editor;

        }


        ////funcion para citar boletin y revistas completas  2  y 17
        private function construirReferenciaBoletinRevista($documento,$autores,$editores) {
            $autores = self::construirReferenciaAutores($autores,$documento);
            $titulo = $documento->titulo;
            $lugar = self::obtenerLugarPublicacionReferenia($documento);
            $editor = self::obtenerEditoresReferencia($editores);
            $fechaPublicacion = self::obtenerFechaReferencia($documento);
            //

            $boletinRevista = self::obtenerTipoDocumento($documento->tipo,$documento->Id_doc);

            $anio = $boletinRevista->anio;
            $volumen  =  $boletinRevista->volumen;
            $numero  =  $boletinRevista->num_revista;
            $pag  =  $boletinRevista->pag;
            $info = "";
            $infoFecha_pag;
            if ($anio == "" && $volumen == "" && $numero == ""){
                $info = "";
            }else if ($anio == "" && $volumen == "" && $numero != ""){
                $info = $numero . ', ';
            }else if ($anio == "" && $volumen != "" && $numero == ""){
                $info = $volumen . ', ';
            }else if ($anio != "" && $volumen == "" && $numero == ""){
                $info = $anio . ', ';
            }else if ($anio == "" && $volumen != "" && $numero != ""){
                $info = $volumen . ', ' . $numero . ', ';
            }else if ($anio != "" && $volumen == "" && $numero != ""){
                $info = $anio . ', ' . $numero . ', ';
            }else if ($anio != "" && $volumen != "" && $numero == ""){
                $info = $anio . ', ' . $volumen . ', ';
            }else if ($anio != "" && $volumen == "" && $numero != ""){
                $info = $anio . ', ' . $numero . ', ';
            }else if ($anio != "" && $volumen != "" && $numero != ""){
                $info = $anio . ', ' . $volumen . ', ' . $numero . ', ';
            }
            if ($fechaPublicacion != "" && $pag != "") {
                $infoFecha_pag = $fechaPublicacion . ', ' . $pag . '.';
            }else if ($fechaPublicacion != "" && $pag == ""){
                $infoFecha_pag = $fechaPublicacion . '.';
            }else if ($fechaPublicacion == "" && $pag != ""){
                $infoFecha_pag = $fechaPublicacion . '.';
            }
            $autores = $autores != "" ? $autores . ", " : "";
            return $autores. ''.$titulo .', '. $lugar . '' . $editor . ', ' . $info . ' ' . $infoFecha_pag ;
        }
    ////funcion para citar boletin y revistas completas  2  y 17
    private function construirReferenciaBoletinRevista1($documento, $autores, $editores) {
        $autores = self::construirReferenciaAutores($autores,$documento);
        $autores = $autores != "" ? $autores . ", " : "";
        return $autores;
    }
    ////funcion para citar boletin y revistas completas  2  y 17
    private function construirReferenciaBoletinRevista2($documento, $autores, $editores) {
        $titulo = $documento->titulo;
        return  $titulo . ', ';
    }
    ////funcion para citar boletin y revistas completas  2  y 17
    private function construirReferenciaBoletinRevista3($documento, $autores, $editores) {
        $lugar = self::obtenerLugarPublicacionReferenia($documento);
        return $lugar . ' ';
    }
    ////funcion para citar boletin y revistas completas  2  y 17
    private function construirReferenciaBoletinRevista4($documento, $autores, $editores) {
        //$editor = self::obtenerEditoresReferencia($editores);
        $editor='';

        for($i=0;$i<sizeof($editores);$i++){



            if($i==(sizeof($editores)-1)){
                $editor=$editor . $editores[$i]->editor. "";


            }

            else{
                $editor= $editor . $editores[$i]->editor.' - ';

            }

        }
        return $editor==''?$editor='':$editor.", ";

    }
    ////funcion para citar boletin y revistas completas  2  y 17
    private function construirReferenciaBoletinRevista5($documento, $autores, $editores)
    {

        $fechaPublicacion = self::obtenerFechaReferencia($documento);
            //

        $boletinRevista = self::obtenerTipoDocumento($documento->tipo, $documento->Id_doc);

        $anio = $boletinRevista->anio;
        $volumen = $boletinRevista->volumen;
        $numero = $boletinRevista->num_revista;
        $pag = $boletinRevista->pag;
        $info = "";
        $infoFecha_pag;
        if ($anio == "" && $volumen == "" && $numero == "") {
            $info = "";
        } else if ($anio == "" && $volumen == "" && $numero != "") {
            $info = $numero . ', ';
        } else if ($anio == "" && $volumen != "" && $numero == "") {
            $info = $volumen . ', ';
        } else if ($anio != "" && $volumen == "" && $numero == "") {
            $info = $anio . ', ';
        } else if ($anio == "" && $volumen != "" && $numero != "") {
            $info = $volumen . ', ' . $numero . ', ';
        } else if ($anio != "" && $volumen == "" && $numero != "") {
            $info = $anio . ', ' . $numero . ', ';
        } else if ($anio != "" && $volumen != "" && $numero == "") {
            $info = $anio . ', ' . $volumen . ', ';
        } else if ($anio != "" && $volumen == "" && $numero != "") {
            $info = $anio . ', ' . $numero . ', ';
        } else if ($anio != "" && $volumen != "" && $numero != "") {
            $info = $anio . ', ' . $volumen . ', ' . $numero . ', ';
        }
        if ($fechaPublicacion != "" && $pag != "") {
            $infoFecha_pag = $fechaPublicacion . ', ' . $pag . '.';
        } else if ($fechaPublicacion != "" && $pag == "") {
            $infoFecha_pag = $fechaPublicacion . '.';
        } else if ($fechaPublicacion == "" && $pag != "") {
            $infoFecha_pag = $fechaPublicacion . '.';
        }
        return $info . ' ' . $infoFecha_pag;
    }



    private function construirReferenciaPonencia($documento, $autores, $editores){
        $autores = self::construirReferenciaAutores($autores,$documento);
        $titulo = $documento->titulo;
        $lugarPublicacion = self::obtenerLugarPublicacionReferenia($documento);
        $editor = self::obtenerEditoresReferencia($editores);
        $fechaPublicacion = self::obtenerFechaReferencia($documento);

        $ponencia = self::obtenerTipoDocumento($documento->tipo, $documento->Id_doc);

        $evento = $ponencia->evento;
        $lugar = $ponencia->lugar_presentacion;
        $fechaPresentacion = $ponencia->fecha_pesentacion;
        $pag = $ponencia->paginas;

        $info = "";

        if ($evento != "" && $lugar != "" && $fechaPresentacion != "") {
            $info = ' (' . $evento . ' [' . $lugar . ', ' . $fechaPresentacion . ']), ';
        } else if ($evento != "" && $lugar == "" && $fechaPresentacion == "") {
            $info = ' (' . $evento . '), ';
        } else if ($evento != "" && $lugar != "" && $fechaPresentacion == "") {
            $info = ' (' . $evento . ' [' . $lugar . ']), ';
        } else if ($evento != "" && $lugar == "" && $fechaPresentacion != "") {
            $info = ' (' . $evento . ' [' . $fechaPresentacion . ']), ';
        } else if ($evento == "" && $lugar != "" && $fechaPresentacion != "") {
            $info = ' ([' . $lugar . ', ' . $fechaPresentacion . ']), ';
        } else if ($evento == "" && $lugar == "" && $fechaPresentacion != "") {
            $info = ' ([' . $fechaPresentacion . ']), ';
        } else if ($evento == "" && $lugar != "" && $fechaPresentacion == "") {
            $info = ' ([' . $lugar . ']), ';
        } else if ($evento == "" && $lugar == "" && $fechaPresentacion == "") {
            $info = ', ';
        }
        $autores = $autores != "" ? $autores . ", " : "";

        if ($documento->url != "" && $documento->derecho_autor == 1 or $documento->url != "" && $documento->derecho_autor == 1) {
            return $autores . '' . $titulo . ' ' . $info . '' . $lugarPublicacion . ' ' . $editor . ', ' . $fechaPublicacion . ', ' . $pag . '. ';
        } else {
            return $autores . '' . $titulo . ' ' . $info . '' . $lugarPublicacion . ' ' . $editor . ', ' . $pag . '. ';
        }
    }
    private function construirReferenciaPonencia1($documento, $autores, $editores){
        $autores = self::construirReferenciaAutores($autores,$documento);
        $autores = $autores != "" ? $autores . ", " : "";

        if ($documento->url != "" && $documento->derecho_autor == 1 or $documento->url != "" && $documento->derecho_autor == 1) {
            return $autores;
        } else {
            return $autores;
        }
    }
    private function construirReferenciaPonencia2($documento, $autores, $editores){
        $titulo = $documento->titulo;
        if ($documento->url != "" && $documento->derecho_autor == 1 or $documento->url != "" && $documento->derecho_autor == 1) {
            return $titulo . ' ';
        } else {
            return $titulo . ' ';
        }
    }
    private function construirReferenciaPonencia3($documento, $autores, $editores){
        $lugarPublicacion = self::obtenerLugarPublicacionReferenia($documento);
        $editor = self::obtenerEditoresReferencia($editores);
        $fechaPublicacion = self::obtenerFechaReferencia($documento);

        $ponencia = self::obtenerTipoDocumento($documento->tipo, $documento->Id_doc);

        $evento = $ponencia->evento;
        $lugar = $ponencia->lugar_presentacion;
        $fechaPresentacion = $ponencia->fecha_pesentacion;
        $pag = $ponencia->paginas;

        $info = "";

        if ($evento != "" && $lugar != "" && $fechaPresentacion != "") {
            $info = ' (' . $evento . ' [' . $lugar . ', ' . $fechaPresentacion . ']), ';
        } else if ($evento != "" && $lugar == "" && $fechaPresentacion == "") {
            $info = ' (' . $evento . '), ';
        } else if ($evento != "" && $lugar != "" && $fechaPresentacion == "") {
            $info = ' (' . $evento . ' [' . $lugar . ']), ';
        } else if ($evento != "" && $lugar == "" && $fechaPresentacion != "") {
            $info = ' (' . $evento . ' [' . $fechaPresentacion . ']), ';
        } else if ($evento == "" && $lugar != "" && $fechaPresentacion != "") {
            $info = ' ([' . $lugar . ', ' . $fechaPresentacion . ']), ';
        } else if ($evento == "" && $lugar == "" && $fechaPresentacion != "") {
            $info = ' ([' . $fechaPresentacion . ']), ';
        } else if ($evento == "" && $lugar != "" && $fechaPresentacion == "") {
            $info = ' ([' . $lugar . ']), ';
        } else if ($evento == "" && $lugar == "" && $fechaPresentacion == "") {
            $info = ',';
        }


        if ($documento->url != "" && $documento->derecho_autor == 1 or $documento->url != "" && $documento->derecho_autor == 1) {

            if($fechaPublicacion=='[s.f.]' &&  $pag ==''){
                return $info . '' . $lugarPublicacion . ' ' . $editor . ', ' . $fechaPublicacion .  '. ';
            }else{
                return $info . '' . $lugarPublicacion . ' ' . $editor . ', ' . $fechaPublicacion . ', ' . $pag . '. ';
            }
        } else {
            return $info . '' . $lugarPublicacion . ' ' . $editor . ', ' . $pag . '. ';
        }
    }


          ////funcion para citar boletin y revistas completas  14  y 18
    private function construirReferenciaArticuloBoletinRevista($documento, $autores, $editores) {
        $autores = self::construirReferenciaAutores($autores,$documento);
        $titulo = $documento->titulo;
        $lugar = self::obtenerLugarPublicacionReferenia($documento);
        $editor = self::obtenerEditoresReferencia($editores);
        $fechaPublicacion = self::obtenerFechaReferencia($documento);

        $artBoletinRevista = self::obtenerTipoDocumento($documento->tipo, $documento->Id_doc);

        $nombre = $artBoletinRevista->nombre_revista;
        $anio = $artBoletinRevista->anio;
        $volumen = $artBoletinRevista->volumen;
        $evento = $artBoletinRevista->volumen;
        $num = $artBoletinRevista->num_revista;
        $pag = $artBoletinRevista->pag;

        $info = "";
        $infoAux = "";

        if ($anio == "" && $volumen == "" && $num == "") {
            $info = "";
        } else if ($anio == "" && $volumen == "" && $num != "") {
            $info = $num . ', ';
        } else if ($anio == "" && $volumen != "" && $num == "") {
            $info = $volumen . ', ';
        } else if ($anio != "" && $volumen == "" && $num == "") {
            $info = $anio . ', ';
        } else if ($anio == "" && $volumen != "" && $num != "") {
            $info = $volumen . ', ' . $num . ', ';
        } else if ($anio != "" && $volumen == "" && $num != "") {
            $info = $anio . ', ' . $num . ', ';
        } else if ($anio == "" && $volumen != "" && $num != "") {
            $info = $volumen . ', ' . $num . ', ';
        } else if ($anio != "" && $volumen != "" && $num == "") {
            $info = $anio . ', ' . $volumen . ', ';
        } else if ($anio != "" && $volumen == "" && $num != "") {
            $info = $anio . ', ' . $num . ', ';
        } else if ($anio != "" && $volumen != "" && $num != "") {
            $info = $anio . ', ' . $volumen . ', ' . $num . ', ';
        }

        if ($fechaPublicacion != "" && $pag != "") {
            $infoAux = $fechaPublicacion . ', ' . $pag . '.';
        } else if ($fechaPublicacion != "" && $pag == "") {
            $infoAux = $fechaPublicacion . '.';
        } else if ($fechaPublicacion == "" && $pag != "") {
            $infoAux = $pag . '.';
        }

        if ($nombre != "") {
            $nombreBoletin = $nombre . ', ';
        } else {
            $nombreBoletin = '';
        }

        if ($editor == '[s.e.]') {
            $editor = '';
        } else {
            $editor = $editor . ', ';
        }

        $autores = $autores != "" ? $autores . ", " : "";

        return $autores . '' . $titulo . ', ' . $nombreBoletin . '' . $info . '' . $lugar . '' . $editor . ' ' . $infoAux;
    }
    private function construirReferenciaArticuloBoletinRevista1($documento, $autores, $editores) {
        $autores = self::construirReferenciaAutores($autores,$documento);
        $autores = $autores != "" ? $autores . ", " : "";

        return $autores . '';
    }
    private function construirReferenciaArticuloBoletinRevista2($documento, $autores, $editores) {
        $titulo = $documento->titulo;
       return  "\"" . $documento->titulo . "\"".', ';
       // return $titulo . ', ';
    }
    private function construirReferenciaArticuloBoletinRevista3($documento, $autores, $editores) {
        $artBoletinRevista = self::obtenerTipoDocumento($documento->tipo, $documento->Id_doc);

        $nombre = $artBoletinRevista->nombre_revista;

        if ($nombre != "") {
            $nombreBoletin = $nombre . ', ';
        } else {
            $nombreBoletin = '';
        }

        return $nombreBoletin;
    }
    private function construirReferenciaArticuloBoletinRevista4($documento, $autores, $editores) {

        $lugar = self::obtenerLugarPublicacionReferenia($documento);
        $editor = self::obtenerEditoresReferencia($editores);
        $fechaPublicacion = self::obtenerFechaReferencia($documento);

        $artBoletinRevista = self::obtenerTipoDocumento($documento->tipo, $documento->Id_doc);

        $anio = $artBoletinRevista->anio;
        $volumen = $artBoletinRevista->volumen;
        $evento = $artBoletinRevista->volumen;
        $num = $artBoletinRevista->num_revista;
        $pag = $artBoletinRevista->pag;

        $info = "";
        $infoAux = "";

        if ($anio == "" && $volumen == "" && $num == "") {
            $info = "";
        } else if ($anio == "" && $volumen == "" && $num != "") {
            $info = $num . ', ';
        } else if ($anio == "" && $volumen != "" && $num == "") {
            $info = $volumen . ', ';
        } else if ($anio != "" && $volumen == "" && $num == "") {
            $info = $anio . ', ';
        } else if ($anio == "" && $volumen != "" && $num != "") {
            $info = $volumen . ', ' . $num . ', ';
        } else if ($anio != "" && $volumen == "" && $num != "") {
            $info = $anio . ', ' . $num . ', ';
        } else if ($anio == "" && $volumen != "" && $num != "") {
            $info = $volumen . ', ' . $num . ', ';
        } else if ($anio != "" && $volumen != "" && $num == "") {
            $info = $anio . ', ' . $volumen . ', ';
        } else if ($anio != "" && $volumen == "" && $num != "") {
            $info = $anio . ', ' . $num . ', ';
        } else if ($anio != "" && $volumen != "" && $num != "") {
            $info = $anio . ', ' . $volumen . ', ' . $num . ', ';
        }

        if ($fechaPublicacion != "" && $pag != "") {
            $infoAux = $fechaPublicacion . ', ' . $pag . '.';
        } else if ($fechaPublicacion != "" && $pag == "") {
            $infoAux = $fechaPublicacion . '.';
        } else if ($fechaPublicacion == "" && $pag != "") {
            $infoAux = $pag . '.';
        }

        if ($editor == '[s.e.]') {
            $editor = '';
        } else {
            $editor = $editor . ', ';
        }

        return $info . '' . $lugar . '' . $editor . ' ' . $infoAux;
    }



          ////funcion para citar boletin y revistas completas  8
    private function construirReferenciaLibro($documento, $autores, $editores){
        $autores = self::construirReferenciaAutores($autores,$documento);
        $titulo = $documento->titulo;
        $lugar = self::obtenerLugarPublicacionReferenia($documento);
        $editor = self::obtenerEditoresReferencia($editores);
        $fechaPublicacion = self::obtenerFechaReferencia($documento);

        $libro = self::obtenerTipoDocumento($documento->tipo, $documento->Id_doc);
        if ($libro == null) {
            return "";
        }
        $edicion = $libro->edicion;
        $traductor = $libro->traductor;
        $prologo = $libro->prologo;
        $introduccion = $libro->introduccion;
        $tomo = $libro->tomos;
        $volumen = $libro->volumen;
        $pag = $libro->paginalib;
        $coleccion = $libro->coleccion;
        $noCol = $libro->nocol;
        $serie = $libro->serie;
        $noSerie = $libro->noserie;

        $info = "";
        $infoAux = "";

        if ($edicion != "") $edicion = $edicion . ', ';

        if ($traductor != "") $traductor = 'Trad. ' . $traductor . ', ';

        if ($prologo != "") $prologo = 'Pról. ' . $prologo . ', ';

        if ($introduccion != "") $introduccion = 'Introd. ' . $introduccion . ', ';

        $ref = $traductor . $prologo . $introduccion;

        if ($tomo != "" && $volumen != "") {
            $info = ", " . $tomo . ', ' . $volumen . ', ';
        } else if ($tomo != "" && $volumen == "") {
            $info = ", " . $tomo . ', ';
        } else if ($tomo == "" && $volumen != "") {
            $info = ", " . $volumen . ', ';
        } else if ($tomo == "" && $volumen == "") {
            $info = '';
        }

        if ($coleccion != "" && $noCol != "" && $serie != "" && $noSerie != "") {// Coleccion y Serie 4 tienen datos
            $infoAux = ", " . '(Col.' . $coleccion . ', ' . $noCol . ', Serie ' . $serie . ', ' . $noSerie . ').';
        } else if ($coleccion != "" && $noCol != "" && $serie == "" && $noSerie == "") {//solo colección y no. coleccion tiene datos
            $infoAux = ", " . '(Col. ' . $coleccion . ', ' . $noCol . ').';
        } else if ($coleccion == "" && $noCol == "" && $serie != "" && $noSerie != "") {//Solo seríe y no. de serie tiene datos
            $infoAux = ", " . '(Serie ' . $serie . ', ' . $noSerie . ').';
        } else if ($coleccion != "" && $noCol != "" && $serie == "" && $noSerie != "") {//solo coleccion y serie tiene datos
            $infoAux = ", " . '(Col. ' . $coleccion . ', Serie ' . $serie . ').';
        } else if ($coleccion != "" && $noCol == "" && $serie == "" && $noSerie == "") {//Solo colección
            $infoAux = ", " . '(Col. ' . $coleccion . ').';
        } else if ($coleccion == "" && $noCol == "" && $serie != "" && $noSerie == "") {//Solo Serie
            $infoAux = ", " . '(Serie ' . $serie . ').';
        } else if ($coleccion == "" && $noCol == "" && $serie == "" && $noSerie == "") {//todos vacios
            $infoAux = '';
        }

        if ($infoAux == '' && $pag != '') {
            $infoAux = ", " . $pag . '.';
        } elseif ($infoAux != '' && $pag != '') {
            $infoAux = ", " . $pag . '.' . $infoAux . '';
        } elseif ($infoAux == '' && $pag == '') {
            $infoAux = '.';
        }


        $titulo = $titulo != "" ? "" . $titulo . "" : "";
        $autores = $autores != "" ? $autores . ", " : "";
        return $autores . '' . $titulo . ', ' . $edicion . $ref . '' . $lugar . '' . $editor . ', ' . $fechaPublicacion . '' . $info . '' . $infoAux;
    }
    private function construirReferenciaLibro1($documento, $autores, $editores){
        $autores = self::construirReferenciaAutores($autores,$documento);
        $autores = $autores != "" ? $autores . ", " : "";
        return $autores;
    }
    private function construirReferenciaLibro2($documento, $autores, $editores){
        $titulo = $documento->titulo;
        $titulo = $titulo != "" ? "" . $titulo . "" : "";

        return $titulo . ', ' ;
    }
    private function construirReferenciaLibro3($documento, $autores, $editores){
        $lugar = self::obtenerLugarPublicacionReferenia($documento);
        $editor = self::obtenerEditoresReferencia($editores);
        $fechaPublicacion = self::obtenerFechaReferencia($documento);

        $libro = self::obtenerTipoDocumento($documento->tipo, $documento->Id_doc);
        if ($libro == null) {
            return "";
        }
        $edicion = $libro->edicion;
        $traductor = $libro->traductor;
        $prologo = $libro->prologo;
        $introduccion = $libro->introduccion;
        $tomo = $libro->tomos;
        $volumen = $libro->volumen;
        $pag = $libro->paginalib;
        $coleccion = $libro->coleccion;
        $noCol = $libro->nocol;
        $serie = $libro->serie;
        $noSerie = $libro->noserie;

        $info = "";
        $infoAux = "";

        if ($edicion != "") $edicion = $edicion . ', ';

        if ($traductor != "") $traductor = 'Trad. ' . $traductor . ', ';

        if ($prologo != "") $prologo = 'Pról. ' . $prologo . ', ';

        if ($introduccion != "") $introduccion = 'Introd. ' . $introduccion . ', ';

        $ref = $traductor . $prologo . $introduccion;

        if ($tomo != "" && $volumen != "") {
            $info = ", " . $tomo . ', ' . $volumen . ', ';
        } else if ($tomo != "" && $volumen == "") {
            $info = ", " . $tomo . ', ';
        } else if ($tomo == "" && $volumen != "") {
            $info = ", " . $volumen . ', ';
        } else if ($tomo == "" && $volumen == "") {
            $info = '';
        }

        if ($coleccion != "" && $noCol != "" && $serie != "" && $noSerie != "") {// Coleccion y Serie 4 tienen datos
            $infoAux = " " . ' (Col.' . $coleccion . ', ' . $noCol . ', Serie ' . $serie . ', ' . $noSerie . ')';
        } else if ($coleccion != "" && $noCol != "" && $serie == "" && $noSerie == "") {//solo colección y no. coleccion tiene datos
            $infoAux = " " . ' (Col. ' . $coleccion . ', ' . $noCol . ')';
        } else if ($coleccion == "" && $noCol == "" && $serie != "" && $noSerie != "") {//Solo seríe y no. de serie tiene datos
            $infoAux = " " . ' (Serie ' . $serie . ', ' . $noSerie . ')';
        } else if ($coleccion != "" && $noCol != "" && $serie == "" && $noSerie != "") {//solo coleccion y serie tiene datos
            $infoAux = " " . ' (Col. ' . $coleccion . ', Serie ' . $serie . ')';
        } else if ($coleccion != "" && $noCol == "" && $serie == "" && $noSerie == "") {//Solo colección
            $infoAux = " " . ' (Col. ' . $coleccion . ')';
        } else if ($coleccion == "" && $noCol == "" && $serie != "" && $noSerie == "") {//Solo Serie
            $infoAux = " " . ' (Serie ' . $serie . ')';
        } else if ($coleccion == "" && $noCol == "" && $serie == "" && $noSerie == "") {//todos vacios
            $infoAux = '';
        }

        if ($infoAux == '' && $pag != '') {
            $infoAux = ", " . $pag . '.';
        } elseif ($infoAux != '' && $pag != '') {
            $infoAux = ", " . $pag . '.' . $infoAux . '';
        } elseif ($infoAux == '' && $pag == '') {
            $infoAux = '.';
        }
        return $edicion . $ref . '' . $lugar . ' ' . $editor . ', ' . $fechaPublicacion . '' . $info . '' . $infoAux;
    }


    
    private function construirReferenciaVideo($documento,$editores){

        $referenciaVideo="";
        //Obtener video
        $video = Video::where('fk_doc', $documento->Id_doc)->firstOrFail();
       
        //Si no existe el dato no aparece. El título “Título Secundario:” lo pone la programación.
        if($video->secundario!="")
            $referenciaVideo =$referenciaVideo. " Título Secundario: " . $video->secundario .'<br><br>';
        //Si no existe el dato no aparece. El título “Director:” lo pone la programación.
        if($video->director!="")
            $referenciaVideo =$referenciaVideo. " Director: " . $video->director .'<br><br>';
         //Si no existe el dato no aparece. El título “Productor:” lo pone la programación.
         if($video->productor!="")
            $referenciaVideo =$referenciaVideo. " Productor: " . $video->productor.'<br><br>';
        //Si no existe el dato no aparece. El título “Realizador:” lo pone la programación.
          if($video->realizador!="")
            $referenciaVideo =$referenciaVideo. " Realizador: " . $video->realizador.'<br><br>';
         //Si no existe el dato no aparece. El título “Guionista:” lo pone la programación.
         if($video->guionista!="")
            $referenciaVideo =$referenciaVideo. " Guionista: " . $video->guionista.'<br><br>';
        //Si no existe el dato no aparece. El título “Fotografía:” lo pone la programación.
        if($video->fotografia!="")
            $referenciaVideo =$referenciaVideo. " Fotografía: " . $video->fotografia.'<br><br>';
        //Si no existe el dato no aparece. El título “Música:” lo pone la programación
        if($video->musica!="" && $video->musica!=null && $video->musica!=" ")
        $referenciaVideo =$referenciaVideo. " Música: " . $video->musica.'<br><br>';
        //Si no existe el dato no aparece. El título “Conducción:” lo pone la programación.
        if($video->conductor!="")
            $referenciaVideo =$referenciaVideo. " Conducción: " . $video->conductor.'<br><br>';
        //Si no existe el dato no aparece. El título “Reportaje:” lo pone la programación.
        if($video->reportero!="")
        $referenciaVideo =$referenciaVideo. " Reportaje: " . $video->reportero.'<br><br>';
        //Si no existe el dato no aparece. El título “Reparto:” lo pone la programación.
        if($video->actores!="")
        $referenciaVideo =$referenciaVideo. " Reparto: " . $video->actores.'<br><br>';
        //Si no existe el dato no aparece. El título “Narración:” lo pone la programación.
        if($video->narrador!="")
        $referenciaVideo =$referenciaVideo. " Narración: " . $video->narrador.'<br><br>';
        //Si no existe el dato no aparece. El título “Compañía Productora:” lo pone la programación.
        //EN REALIDAD ES EL EDITOR
        $editor = self::obtenerEditoresReferencia($editores);
        if($editor!=null && $editor!="[s.e.]")
        $referenciaVideo =$referenciaVideo. " Compañía Productora: " . $editor.'<br><br>';

        
        //Si no existe el dato no aparece. El título “Canal Transmisor:” lo pone la programación.
        if($video->canal!="")
        $referenciaVideo =$referenciaVideo. " Canal Transmisor: " . $video->canal.'<br><br>';

        //Al visualizarse en la referencia se unen a un sólo titulo. La segunda forma es para
        //cuando no exista el dato, el “[s.l.]” lo pone la programación. El título “Lugar de Edición o
        //Producción:” lo pone la programación.


        
        if($documento->lugar_public_pais!='' && $documento->lugar_public_edo!=''){
        $referenciaVideo =$referenciaVideo." Lugar de Edición o Producción: " . $documento->lugar_public_edo .", " . $documento->lugar_public_pais.'<br><br>';
        }else{
        $referenciaVideo =$referenciaVideo." Lugar de Edición o Producción: [s.l.]".'<br><br>';
        }

        //Fecha de Edición o Producción: dd de mm de aaaa / mm de aaaa / aaaa / mes-mes del año 
        //Fecha de Edición o Producción: [s.f.]
        /*****************************FALTA ESTE CAMPO*************************** */
        $fecha="";
        $documento->fecha_publi==1?$fecha=FechaNormalController::obtenerFechaNormal($documento->Id_doc): $fecha=FechaExtraController::obtenerFechaExtra($documento->Id_doc);
        if($documento->fecha_publi!=null && $documento->fecha_publi!="" && $fecha!="0000-00-00"){ 
            $referenciaVideo =$referenciaVideo." Fecha de Edición o Producción: ".self::fechaAEspaniol($fecha).'<br><br>'; 

        }
        else{
            $referenciaVideo =$referenciaVideo." Fecha de Edición o Producción: [s.f.] ".'<br><br>';
        }
       

        //Si no existe el dato no aparece. El título “Programa:” lo pone la programación.
        if($video->programa!="")
        $referenciaVideo =$referenciaVideo. " Programa: " . $video->programa.'<br><br>';;
        //Si no existe el dato no aparece. El título “Fecha de Transmisión:” lo pone la programación.
        if($video->fecha_trans!="")
        $referenciaVideo =$referenciaVideo. " Fecha de Transmisión: " . $video->fecha_trans.'<br><br>';
        //Si no existe el dato no aparece. El título “Hora de Transmisión:” lo pone la programación.
        if($video->hora_trans!="")
        $referenciaVideo =$referenciaVideo. " Hora de Transmisión: " . $video->hora_trans.'<br><br>';
        //Si no existe el dato no aparece. El título “Formato:” lo pone la programación.
        if($video->formato!="")
        $referenciaVideo =$referenciaVideo. " Formato: " . $video->formato.'<br><br>';
        //Si no existe el dato no aparece. El título “Idioma:”: lo pone la programación.
        if($video->idioma!="")
        $referenciaVideo =$referenciaVideo. " Idioma: " . $video->idioma.'<br><br>';
        //Si no existe el dato no aparece. El título “Subtítulos:”: lo pone la programación.
        if($video->subtitulo!="")
        $referenciaVideo =$referenciaVideo. " Subtítulos: " . $video->subtitulo.'<br><br>';
        //Si no existe el dato no aparece. El título “Duración:” lo pone la programación.
        if($video->duracion!="")
        $referenciaVideo =$referenciaVideo. " Duración: " . $video->duracion.'<br><br>';
       


        if($documento->url!=null){
            $referenciaVideo =$referenciaVideo.
            BBCode::parse('[b]'." Consultado en: <br> ". $documento->url."<br>".'[/b]');

            }
        else {
            $referenciaVideo =$referenciaVideo.
            BBCode::parse('[b]'. " Consultado en:<br> Sin url"."<br>".'[/b]');

        }

        if($documento->fecha_consulta!=null){
            $referenciaVideo =$referenciaVideo.
            BBCode::parse('[b]'. "Fecha de consulta: ". $documento->fecha_consulta ."<br><br>".'[/b]');
        }
        else{
            $referenciaVideo =$referenciaVideo.
            BBCode::parse('[b]'. " Fecha de consulta: Sin fecha"."<br><br>".'[/b]');
        }
        
         //Siempre se le asignarán todos los temas El título “Temas:” y los temas en sí, los pone la programación.
         $temas =DB::table('temas as t')
         ->join('cntrl_tema as cp','cp.fk_tema',"=",'t.id_tema')
         ->join('documento as d','d.Id_doc',"=",'cp.fk_doc')
         ->where('fk_doc',$documento->Id_doc)
         ->get();
         if($temas!=null && $temas!="")
         $referenciaVideo =$referenciaVideo. " " . self::construirReferenciaTemas($temas).'.<br>';

           //Si no existe el dato no aparece. El título “Población:” lo pone la programación.
        if($documento->poblacion!=" " && $documento->poblacion!=null && $documento->poblacion!="" && $documento->poblacion!=0){
            $poblacionRef="";
            //POBLACION
            if ($documento->poblacion==1)
            $poblacionRef = 'Afrodescendiente';
            else if ($documento->poblacion==2)
            $poblacionRef = 'Indígena';
            else if ($documento->poblacion==3)
            $poblacionRef = 'Afrodescendiente e Indígena';
            else if ($documento->poblacion==0)
            $poblacionRef = '';
            $referenciaVideo =$referenciaVideo." Población: " . $poblacionRef.'.<br><br>';
        }

        return $referenciaVideo;
    }    

    private function fechaAEspaniol ($fecha) {
        $stringFecha="";
        $fecha = substr($fecha, 0, 10);
        $numeroDia = date('d', strtotime($fecha));
        $dia = date('l', strtotime($fecha));
        $mes = date('F', strtotime($fecha));
        $anio = date('Y', strtotime($fecha));
        $dias_ES = array("Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo");
        $dias_EN = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
        $nombredia = str_replace($dias_EN, $dias_ES, $dia);
        $meses_ES = array("enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre");
        $meses_EN = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
        $nombreMes = str_replace($meses_EN, $meses_ES, $mes);

        //Fecha completa (dd de mm de aaaa): 23 de agosto de 2009
        if($numeroDia!=null && $nombreMes!=null && $anio!=null)
        $stringFecha = $numeroDia." de ".$nombreMes." de ".$anio;
        //Fecha sólo con mes y año (mm de aaaa): Agosto de 2009
        if($numeroDia==null && $nombreMes!=null && $anio!=null)
        $stringFecha = $nombreMes." de ".$anio;
        //Fecha sólo con año (aaaa): 2009
        if($numeroDia==null && $nombreMes==null && $anio!=null)
        $stringFecha = $anio;
        

        return $numeroDia." de ".$nombreMes." de ".$anio;
      }
      

      public function verPdf($id)
      {

        $filepath = storage_path("/documentos/".$id.'.pdf');

        if(file_exists($filepath )){
            return response()->file($filepath);

        }else{
             abort(404);
        }

      
         
      }



}
