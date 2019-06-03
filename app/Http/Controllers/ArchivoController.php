<?php

namespace sistema\Http\Controllers;

use Illuminate\Http\Request;
use sistema\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use sistema\Archivo;

class ArchivoController extends Controller
{
	public function __construct()
	{
		//si no esta logeado regresa al login
		//$this->middleware('auth');
	}

	/*
	private function validarRoles()
	{
		if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {
			return Redirect::to('/');
		}
	}
	*/
	public function index(Request $request)
	{
		if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {
			return Redirect::to('/');
		}

		if ($request) {
			$query = trim($request->get('searchText'));
			$archivos = DB::table('archivos')
			->where('idArchivo', 'LIKE', '%' . $query . '%')
			->orwhere('nombre', 'LIKE', '%' . $query . '%')
			->orwhere('ruta', 'LIKE', '%' . $query . '%')
			->orwhere('tipoArchivo', 'LIKE', '%' . $query . '%')
			->orwhere('peso', 'LIKE', '%' . $query . '%')
			->orderBy('Id_autor', 'desc')
			->paginate(10);

			$page = $request->get('page') != null ? $request->get('page') : 1;

			$numeroRegistros = DB::table('archivos')
			->where('idArchivo', 'LIKE', '%' . $query . '%')
			->orwhere('nombre', 'LIKE', '%' . $query . '%')
			->orwhere('ruta', 'LIKE', '%' . $query . '%')
			->orwhere('tipoArchivo', 'LIKE', '%' . $query . '%')
			->orwhere('peso', 'LIKE', '%' . $query . '%')
			->orderBy('Id_autor', 'desc')->count();

			return view(
				'archivo.index',
				[
					'archivos' => $archivos,
					"searchText" => $query,
					"totalRegistros" => $numeroRegistros,
					"page" => $page
					]
				);
			}
		}

		public function create()
		{
			if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {
				return Redirect::to('/');
			}
			return view('archivo.create');
		}

		public function store(AutorFormRequest $request)
		{
			if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {
				return Redirect::to('/');
			}
			$archivo = new Archivo;
			$archivo->nombre = $request->get('nombre');
			$archivo->ruta = $request->get('ruta');
			$archivo->tipoArchivo = $request->get('tipoArchivo');
			$archivo->peso = $request->get('peso');

			//activar el log de la base de datos
			DB::connection()->enableQueryLog();
			$archivo->save();
			//1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
			LogController::agregarLog(
				1,
				"archivo",
				"Se agregó el archivo: " . json_encode($archivo)
			);

			return Redirect::to('archivo');
		}

		public function show($id)
		{
			if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {
				return Redirect::to('/');
			}
			return view('archivo.show', ["archivo" => Archivo::findOrFail($id)]);
		}
		public function edit($id)
		{
			if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {
				return Redirect::to('/');
			}
			return view('archivo.edit', ["archivo" => Archivo::findOrFail($id)]);
		}

		public function update(AutorFormRequest $request, $id)
		{
			if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {
				return Redirect::to('/');
			}
			$archivo = Archivo::findOrFail($id);
			$archivo->nombre = $request->get('nombre');
			$archivo->ruta = $request->get('ruta');
			$archivo->tipoArchivo = $request->get('tipoArchivo');
			$archivo->peso = $request->get('peso');

			//activar el log de la base de datos
			DB::connection()->enableQueryLog();
			$archivo->update();
			//1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
			LogController::agregarLog(
				2,
				"archivo",
				"Se actualizó el archivo: " . json_encode($archivo)
			);
			return Redirect::to('archivo');

		}

		public function destroy($id)
		{
			if (!Auth::user()->authorizeRoles(['catalogador', 'admin','revisor'])) {
				return Redirect::to('/');
			}
			$archivo = Archivo::findOrFail($id);
			//activar el log de la base de datos
			DB::connection()->enableQueryLog();
			$archivo->delete();
			//1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
			LogController::agregarLog(
				3,
				"archivo",
				"Se eliminó el archivo: " . json_encode($archivo)
			);

			//Session::flash('message','El archivo fue eliminado');

			return Redirect::to('archivo');

		}
	}
