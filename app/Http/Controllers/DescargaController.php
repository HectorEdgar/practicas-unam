<?php

namespace sistema\Http\Controllers;

use Illuminate\Http\Request;
use sistema\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use sistema\Archivo;
use sistema\Http\Requests\DescargaFormRequest;
use sistema\Descarga;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;

class DescargaController extends Controller
{
    public function __construct()
    {
        //si no esta logeado regresa al login
        //$this->middleware('auth');
    }
    /*
    private function validarRoles()
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin', 'revisor'])) {return Redirect::to('/');}
    }
    */
    public function index(Request $request)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin', 'revisor'])) {return Redirect::to('/');}

        if ($request) {
            $query = trim($request->get('searchText'));
            $descargas = Descarga::join("archivos","archivos.idArchivo","=","descargas.idArchivo")
                ->orwhere('idDescarga', 'LIKE', '%' . $query . '%')
                ->orwhere('titulo', 'LIKE', '%' . $query . '%')
                ->orwhere('url', 'LIKE', '%' . $query . '%')
                ->orwhere('fechaIngreso', 'LIKE', '%' . $query . '%')
                ->orwhere('tipoProyecto', 'LIKE', '%' . $query . '%')
                ->orwhere('estado', 'LIKE', '%' . $query . '%')
                ->orderBy('fechaIngreso', 'desc')
                ->paginate(10);

            $page = $request->get('page') != null ? $request->get('page') : 1;

            $numeroRegistros = DB::table('descargas')
                ->where('idDescarga', 'LIKE', '%' . $query . '%')
                ->orwhere('titulo', 'LIKE', '%' . $query . '%')
                ->orwhere('url', 'LIKE', '%' . $query . '%')
                ->orwhere('fechaIngreso', 'LIKE', '%' . $query . '%')
                ->orwhere('tipoProyecto', 'LIKE', '%' . $query . '%')
                ->orwhere('estado', 'LIKE', '%' . $query . '%')
                ->orderBy('fechaIngreso', 'desc')->count();

            return view(
                'descarga.index',
                [
                    'descargas' => $descargas,
                    "searchText" => $query,
                    "totalRegistros" => $numeroRegistros,
                    "page" => $page
                ]
            );
        }
    }

    public function create()
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin', 'revisor'])) {return Redirect::to('/');}
        return view('descarga.create');
    }

    public function downloadFile($src)
    {
        //$src=$src->get('ruta');
        //$src=$ruta;
        Log::info('message');
        if (is_file($src)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $content_type = finfo_file($finfo, $src);
            finfo_close($finfo);
            $file_name = basename($src) . PHP_EOL;
            $size = filesize($src);
            header('Content-Type: application/force-download');
            header("Content-Disposition: attachment; filename=$file_name");
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: $size");
            readfile($src);
            return true;
        } else {
            return false;
        }
    }
    public function download($ruta)
    {
        if (!$this->downloadFile(public_path() ."/archivos/". $ruta)) {
            //return redirect()->back();

        }
    }
    protected function store(DescargaFormRequest $request)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin', 'revisor'])) {return Redirect::to('/');}
        $descarga = new Descarga;

        $descarga->titulo = $request->get('titulo');
        $descarga->url = $request->get('url');
        $descarga->fechaIngreso = date("Y-m-d H:i:s");
        $descarga->tipoProyecto  = $request->get('tipoProyecto');
        $descarga->estado = $request->get('estado');

        //$descarga->idArchivo = $request->get('idArchivo');


        DB::connection()->enableQueryLog();
        if (Input::hasFile('archivo')) {
            $file = Input::file('archivo');

            $file->move(public_path() . '/archivos', $file->getClientOriginalName());
            $archivo = new Archivo;
            $archivo->nombre = $file->getClientOriginalName();
            $archivo->ruta = public_path() . '/archivos/'. $file->getClientOriginalName();
            $archivo->tipoArchivo = $file->getClientOriginalExtension();
            $archivo->peso = $file->getClientSize();
            $archivo->save();
            //$descarga->archivo()->save($archivo);
            $idArchivo = Archivo::where('nombre', '=', $archivo->nombre)->where('ruta', '=', $archivo->ruta)->first()->idArchivo;
            $descarga->idArchivo=$idArchivo;
        }


        //activar el log de la base de datos

        $descarga->save();
        //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
        LogController::agregarLog(
            1,
            "descarga",
            "Se agregó la descarga: " . json_encode($descarga)
        );

        return Redirect::to('descarga');
    }

    public function show($id)
    {
        if (!Auth::user()->authorizeRoles(['catalogador', 'admin', 'revisor'])) {return Redirect::to('/');}
        return view('descarga.show', ["descarga" => Descarga::findOrFail($id)]);
    }
    public function edit($id)
    {
        if (!Auth::user()->authorizeRoles(['admin', 'revisor'])) {return Redirect::to('/');}
        return view('descarga.edit', ["descarga" => Descarga::findOrFail($id)]);
    }

    public function update(DescargaFormRequest $request, $id)
    {
        if (!Auth::user()->authorizeRoles(['admin', 'revisor'])) {return Redirect::to('/');}
        $descarga =Descarga::findOrFail($id);

        $descarga->titulo = $request->get('titulo');
        $descarga->url = $request->get('url');
        $descarga->fechaIngreso = date("Y-m-d H:i:s");
        $descarga->tipoProyecto  = $request->get('tipoProyecto');
        $descarga->estado = $request->get('estado');

        //$descarga->idArchivo = $request->get('idArchivo');
        /*
        $aux = Archivo::findOrFail($descarga->idArchivo);
        array_map("unlink", glob($aux->ruta));

        DB::connection()->enableQueryLog();
        if (Input::hasFile('archivos')) {

            $file = Input::file('archivos');

            $file->move(public_path() . '/archivos', $file->getClientOriginalName());
            Log::error($file->getClientOriginalName());
            $archivo = new Archivo;
            $archivo->nombre = $file->getClientOriginalName();
            $archivo->ruta = public_path() . '/archivos/' . $file->getClientOriginalName();
            $archivo->tipoArchivo = $file->getClientOriginalExtension();
            $archivo->peso = $file->getClientSize();
            $archivo->save();
            //$descarga->archivo()->save($archivo);
            $idArchivo=Archivo::where('nombre', '=', $archivo->nombre)->where('ruta', '=', $archivo->ruta)->first()->idArchivo;
            $descarga->idArchivo = $idArchivo;
        }

       */
        //activar el log de la base de datos


        $descarga->save();

        //$aux->delete();
        //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
        LogController::agregarLog(
            2,
            "descarga",
            "Se actualizó la descarga: " . json_encode($descarga)
        );
        return Redirect::to('descarga');

    }

    public function destroy($id)
    {
        if (!Auth::user()->authorizeRoles(['admin', 'revisor'])) {return Redirect::to('/');}
        $descarga = Descarga::findOrFail($id);
        //activar el log de la base de datos
        $archivo=Archivo::findOrFail($descarga->idArchivo);
        array_map("unlink", glob($archivo->ruta));
        //unlink($archivo->ruta);

        DB::connection()->enableQueryLog();
        $archivo->delete();
        $descarga->delete();
        //1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
        LogController::agregarLog(
            3,
            "descarga",
            "Se eliminó la descarga: " . json_encode($descarga)
        );

        //Session::flash('message','El descarga fue eliminado');

        return Redirect::to('descarga');

    }
}
