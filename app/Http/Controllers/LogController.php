<?php

namespace sistema\Http\Controllers;

use Illuminate\Http\Request;
use sistema\Http\Controllers\Controller;
use sistema\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use sistema\TipoCambio;
use Illuminate\Support\Facades\Log as consola;

class LogController extends Controller
{
    public function __construct()
    {
        //si no esta logeado regresa al login
        //$this->middleware('auth');
    }

    static function agregarLog($tipoCambio,$tabla, $descripcion){
        $queries = DB::getQueryLog();
        DB::connection()->disableQueryLog();
        $log = new Log;
        $log->idTipoCambio=TipoCambio::findOrFail($tipoCambio)->idTipoCambio;
        $log->idUsuario=Auth::user()->id;
        $log->descripcion = $descripcion;
        $log->sentenciaSql =json_encode($queries);
        $log->tabla = $tabla;        
        $log->fechaCreacion = date("Y-m-d H:i:s");
        $log->save();
        
    }
    
    public function index(Request $request)
    {
        if ($request) {

            
            $query = trim($request->get('searchText'));
            $arrayquery = explode(" ", $query);
            if(count($arrayquery)>1){
                //aun no funciona del todo correcto
                $logs = Log::join("users", "users.id", "=", "log.idUsuario")
                    ->join("tipocambio", "tipocambio.idTipoCambio", "=", "log.idTipoCambio")
                    ->orwhere('idLog', 'LIKE', '%' . $arrayquery[0] . '%')
                    ->orwhere('users.name', 'LIKE', '%' . $arrayquery[0] . '%')
                    ->orwhere('tipocambio.tipoCambio', 'LIKE', '%' . $arrayquery[0] . '%')
                    ->orwhere('descripcion', 'LIKE', '%' . $arrayquery[0] . '%')
                    ->orwhere('tabla', 'LIKE', '%' . $arrayquery[0] . '%')
                    ->orwhere('fechaCreacion', 'LIKE', '%' . $arrayquery[0] . '%')
                    ->orwhere('idLog', 'LIKE', '%' . $arrayquery[1] . '%')
                    ->orwhere('users.name', 'LIKE', '%' . $arrayquery[1] . '%')
                    ->orwhere('tipocambio.tipoCambio', 'LIKE', '%' . $arrayquery[1] . '%')
                    ->orwhere('descripcion', 'LIKE', '%' . $arrayquery[1] . '%')
                    ->orwhere('tabla', 'LIKE', '%' . $arrayquery[1] . '%')
                    ->orwhere('fechaCreacion', 'LIKE', '%' . $arrayquery[1] . '%')
                    ->orderBy('idLog', 'desc')
                    ->paginate(10);

                   
                
                
            }else{
                $logs = Log::join("users", "users.id", "=", "log.idUsuario")
                    ->join("tipocambio", "tipocambio.idTipoCambio", "=", "log.idTipoCambio")
                    ->orwhere('idLog', 'LIKE', '%' . $query . '%')
                    ->orwhere('users.name', 'LIKE', '%' . $query . '%')
                    ->orwhere('tipocambio.tipoCambio', 'LIKE', '%' . $query . '%')
                    ->orwhere('descripcion', 'LIKE', '%' . $query . '%')
                    ->orwhere('sentenciaSql', 'LIKE', '%' . $query . '%')
                    ->orwhere('tabla', 'LIKE', '%' . $query . '%')
                    ->orwhere('fechaCreacion', 'LIKE', '%' . $query . '%')
                    ->orderBy('idLog', 'desc')
                    ->paginate(10);
            }

            $page = $request->get('page') != null ? $request->get('page') : 1;
            $numeroRegistros = Log::join("users", "users.id", "=", "log.idUsuario")
                ->join("tipocambio", "tipocambio.idTipoCambio", "=", "log.idTipoCambio")
                ->orwhere('idLog', 'LIKE', '%' . $query . '%')
                ->orwhere('users.name', 'LIKE', '%' . $query . '%')
                ->orwhere('tipocambio.tipoCambio', 'LIKE', '%' . $query . '%')
                ->orwhere('descripcion', 'LIKE', '%' . $query . '%')
                ->orwhere('sentenciaSql', 'LIKE', '%' . $query . '%')
                ->orwhere('tabla', 'LIKE', '%' . $query . '%')
                ->orwhere('fechaCreacion', 'LIKE', '%' . $query . '%')
                ->orderBy('idLog', 'desc')->count();

            return view(
                'log.index',
                [
                    'logs' => $logs,
                    "searchText" => $query,
                    "totalRegistros" => $numeroRegistros,
                    "page" => $page
                ]
            );
        }
    }

    public function create()
    {
        return view('log.create');
    }

    public function store(AutorFormRequest $request)
    {

        $log = new Log;
        $log->save();

        return Redirect::to('log');
    }

    public function show($id)
    {
        return view('log.show', ["log" => Log::findOrFail($id)]);
    }
    public function edit($id)
    {
        return view('log.edit', ["log" => Log::findOrFail($id)]);
    }

    public function update(AutorFormRequest $request, $id)
    {
        $log = Log::findOrFail($id);
        return Redirect::to('log');

    }

    public function destroy($id)
    {
        $log = Log::findOrFail($id);
        $log->delete();
        return Redirect::to('log');

    }
}
