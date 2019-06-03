<?php

namespace sistema\Http\Controllers;
use Illuminate\Http\Request;
use sistema\Subtema;
use sistema\Http\Requests\SubtemaFormRequest;
use Illuminate\Support\Facades\Redirect;
use DB;
use Session;
use sistema\Utilidad;
use Illuminate\Support\Facades\Auth;
use sistema\Http\Controllers\Controller;

class SubtemaController extends Controller
{
      public function __construct()
    {
        //si no esta logeado regresa al login
        //$this->middleware('auth');
    }
    /*
    private function validarRoles(){
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin'])) {
            return Redirect::to('/');
        }
    }
    */
    public function index(Request $request)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}

        if ($request) {
            $query = trim($request->get('searchText'));
            $subtemas = DB::table('subtema')
                ->where('subtema', 'LIKE', '%' . $query . '%')
                ->orwhere('id_sub', 'LIKE', '%' . $query . '%')
                ->orderBy('id_sub', 'desc')
                ->paginate(10);

            $page=$request->get('page')!=null? $request->get('page'):1;

            $numeroRegistros= DB::table('subtema')
                ->where('subtema', 'LIKE', '%' . $query . '%')
                ->orwhere('id_sub', 'LIKE', '%' . $query . '%')
                ->orderBy('id_sub', 'desc')
                ->count();

            return view('subtema.index',
                [
                    'subtemas' => $subtemas,
                    "searchText" => $query,
                    "totalRegistros"=> $numeroRegistros,
                    "page"=> $page
                ]
            );
        }
    }

    public function create()
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        return view('subtema.create');
    }

    public function store(SubtemaFormRequest $request)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        $subtema = new Subtema;
        //Obtiene el Id 100 real
        $id=Utilidad::getId("subtema","id_sub");
        $subtema->id_sub=$id;
        $subtema->subtema = $request->get('subtema');
        $subtema->extra = 0;

        //activar el log de la base de datos
        DB::connection()->enableQueryLog();
        $subtema->save();
        //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
        LogController::agregarLog(
            1,
            "subtema",
            "Se agregó el subtema: ". json_encode($subtema)
        );
        Session::flash('message','El subtema fue añadido con id ('.$id.')');

        return Redirect::to('subtema');
    }

    public function show($id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        //DOCUMENTOS DEL SUBTEMA
        //Tabla Cntrl_SUBTMA

        $documentos =DB::table('cntrl_sub as cts')
            ->join('documento as doc','doc.Id_doc','=','cts.fk_doc')
            ->where('fk_sub',$id)
            ->orderBy('doc.Id_doc', 'desc')
            ->get();

            //Consulta que busca en la tabla de Cntrl para obtener los documentos relacionados con ese subtema
        


        return view('subtema.verDetalle',
        ["subtema" => Subtema::findOrFail($id),
        "documentos" => $documentos,
        ]);

    }
    public function edit($id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        return view('subtema.edit', ["subtema" => Subtema::findOrFail($id)]);
    }

    public function update(SubtemaFormRequest $request, $id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        $subtema = Subtema::findOrFail($id);
        $subtema->subtema = $request->get('subtema');
        //activar el log de la base de datos
        DB::connection()->enableQueryLog();
        $subtema->update();
        //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
        LogController::agregarLog(
            2,
            "subtema",
            "Se actualizó el subtema: " . json_encode($subtema)
        );
        Session::flash('message2','El subtema fue editado con id ('.$id.')');

        return Redirect::to('subtema');
    }

    public function destroy($id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {return Redirect::to('/');}
        $subtema = Subtema::findOrFail($id);
        //activar el log de la base de datos
        DB::connection()->enableQueryLog();
        $subtema->delete();
        //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
        LogController::agregarLog(
            3,
            "subtema",
            "Se eliminó el subtema: " . json_encode($subtema)
        );

        Session::flash('message3','El subtema con id ('.$id.') fue eliminado');

        return Redirect::to('subtema');

    }
}
