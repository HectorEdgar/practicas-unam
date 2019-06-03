<?php

namespace sistema\Http\Controllers;

use Illuminate\Http\Request;
use sistema\Autor;
use sistema\Http\Requests\AutorFormRequest;
use Illuminate\Support\Facades\Redirect;
use DB;
use Session;
use sistema\Utilidad;
use Illuminate\Support\Facades\Auth;

class AutorController extends Controller
{
    public function __construct()
    {
        //si no esta logeado regresa al login
        //$this->middleware('auth');
    }
    /*
    private function validarRoles(){
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin', 'revisor'])){return Redirect::to('/');}
    }
    */
    public function index(Request $request)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin', 'revisor'])){return Redirect::to('/');}

        if ($request) {
            $query = trim($request->get('searchText'));
            $autores = DB::table('autor')
                ->where('nombre', 'LIKE', '%' . $query . '%')
                ->orwhere('apellidos', 'LIKE', '%' . $query . '%')
                ->orwhere('pseudonimo', 'LIKE', '%' . $query . '%')
                ->orwhere('Id_autor', 'LIKE', '%' . $query . '%')
                ->orderBy('apellidos', 'asc')
                ->paginate(10);

            $page=$request->get('page')!=null? $request->get('page'):1;

            $numeroRegistros= DB::table('autor')
                ->where('nombre', 'LIKE', '%' . $query . '%')
                ->orwhere('apellidos', 'LIKE', '%' . $query . '%')
                ->orwhere('pseudonimo', 'LIKE', '%' . $query . '%')
                ->orwhere('Id_autor', 'LIKE', '%' . $query . '%')
                ->orderBy('apellidos', 'asc')
                ->count();

            return view('autor.index',
                [
                    'autores' => $autores,
                    "searchText" => $query,
                    "totalRegistros"=> $numeroRegistros,
                    "page"=> $page
                ]
            );
        }
    }

    public function create()
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin', 'revisor'])){return Redirect::to('/');}
        return view('autor.create');
    }

    public function store(AutorFormRequest $request)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin', 'revisor'])){return Redirect::to('/');}


        //VALIDACIÓN
        if($request->get('pseudonimo')==null && $request->get('nombre')==null ){
            Session::flash('Aviso', 'Ingrese por lo menos el Nombre del Autor');
            return view('autor.create');
        }
        else {
                $autor = new Autor;
                //Obtiene el Id 100 real
                $AuxPseudonimo ="";
                $AuxNombre="";
                $AuxApellidos="";

                if($request->get('pseudonimo')==null)
                    $AuxPseudonimo ="";
                else
                    $AuxPseudonimo=$request->get('pseudonimo');

                if($request->get('nombre')==null)
                    $AuxNombre ="";
                else
                    $AuxNombre=$request->get('nombre');

                if($request->get('apellidos')==null)
                    $AuxApellidos ="";
                else
                    $AuxApellidos=$request->get('apellidos');


                $autor->Id_autor=Utilidad::getId("autor","Id_autor");
                $autor->pseudonimo = $AuxPseudonimo;
                $autor->nombre = $AuxNombre;
                $autor->apellidos = $AuxApellidos;



                //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                $autor->save();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    1,
                    "autor",
                    "Se agregó el autor: ". json_encode($autor)
                );

                //Session::flash('Aviso', 'Se ingresó: Pseudónimo:'.$AuxPseudonimo.' | Nombre: '.$AuxNombre.' | Apellidos: '.$AuxApellidos);

                return Redirect::to('autor');
            }
    }

    public function show($id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin', 'revisor'])){return Redirect::to('/');}
        //DOCUMENTOS DE AUTOR
        //Tabla Cntrl_AUTOR

        $documentos =DB::table('cntrl_autor as ctp')
            ->join('documento as doc','doc.Id_doc','=','ctp.fk_doc')
            ->where('fk_autor',$id)
            ->orderBy('doc.Id_doc', 'desc')
            ->get();

        return view('autor.verDetalle',
        ["autor" => Autor::findOrFail($id),
        "documentos" => $documentos
        ]);
    }
    public function edit($id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin', 'revisor'])){return Redirect::to('/');}
        return view('autor.edit', ["autor" => Autor::findOrFail($id)]);
    }

    public function update(AutorFormRequest $request, $id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin', 'revisor'])){return Redirect::to('/');}
        $autor = Autor::findOrFail($id);

        //VALIDACIÓN
        if($request->get('pseudonimo')==null && $request->get('nombre')==null ){
            Session::flash('Aviso', 'Ingrese por lo menos el Nombre del Autor');
            return Redirect::to('autor/'.$autor->Id_autor.'/edit');
        }
        else {
                //Obtiene el Id 100 real
                $AuxPseudonimo ="";
                $AuxNombre="";
                $AuxApellidos="";

                if($request->get('pseudonimo')==null)
                    $AuxPseudonimo ="";
                else
                    $AuxPseudonimo=$request->get('pseudonimo');

                if($request->get('nombre')==null)
                    $AuxNombre ="";
                else
                    $AuxNombre=$request->get('nombre');

                if($request->get('apellidos')==null)
                    $AuxApellidos ="";
                else
                    $AuxApellidos=$request->get('apellidos');

                $autor->pseudonimo = $AuxPseudonimo;
                $autor->nombre = $AuxNombre;
                $autor->apellidos = $AuxApellidos;

                //activar el log de la base de datos
                DB::connection()->enableQueryLog();
                $autor->update();
                //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
                LogController::agregarLog(
                    2,
                    "autor",
                    "Se actualizó el autor: " . json_encode($autor)
                );
                Session::flash('¡Éxito!', 'El autor se modificó exitosamente.');

                return Redirect::to('autor');
        }
    }

    public function destroy($id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin', 'revisor'])){return Redirect::to('/');}
        $autor = Autor::findOrFail($id);
        //activar el log de la base de datos
        DB::connection()->enableQueryLog();
        $autor->delete();
        //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
        LogController::agregarLog(
            3,
            "autor",
            "Se eliminó el autor: " . json_encode($autor)
        );

        //Session::flash('message','El autor fue eliminado');

        return Redirect::to('autor');

    }
}
