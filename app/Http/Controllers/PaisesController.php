<?php

namespace sistema\Http\Controllers;

use Illuminate\Http\Request;
use sistema\Http\Requests;
use sistema\Paises;
use Illuminate\Support\Facades\Redirect;
use sistema\Http\Requests\PaisesFormRequest;
use DB;
use sistema\Http\Controllers\Controller;
use sistema\Utilidad;
class PaisesController extends Controller
{
    //
     //
    //CONSTRUCTOR
	public function __construct()
	{
		//si no esta logeado regresa al login
        //ES COMO EL PINCHE SPRING SECURITY :v

	}

	//FUNCION INDEX
	public function index(Request $request)
	{
		//SI EXISTE OBJETO REQUEST
		if($request)
		{
			//FILTRO DE BUSQUEDA
			//OBTENER TODOS LOS REGISTROS DE LA TABLA EDITOR
			//TEXTO A BUSCAR
            $query=trim($request->get('searchText'));
			//SE REALIZA LA BUSQUEDA
			$paises=DB::table('paises')
			->where('nombre','LIKE','%'.$query.'%')
			->orwhere('id_pais', 'LIKE', '%' . $query . '%')
			->orderBy('id_pais','desc')
			->paginate(10);

			$page = $request->get('page') != null ? $request->get('page') : 1;

			$numeroRegistros = DB::table('paises')
				->where('nombre', 'LIKE', '%' . $query . '%')
				->orwhere('id_pais', 'LIKE', '%' . $query . '%')
				->orderBy('id_pais', 'desc')
				->count();

			return view('paises.index',
				[
					"paises"=>$paises,
					"searchText"=>$query,
					"totalRegistros" => $numeroRegistros,
					"page" => $page
				]
			);
		}
	}
	public function create()
	{

		return view("paises.create");
	}

	public function store(PaisesFormRequest $request)
	{
		$pais=new Paises;
		$pais->id_pais=Utilidad::getId("paises","id_pais");
		$pais->nombre=$request->get('nombre');
		$pais->save();
		return Redirect::to('paises');
	}

	//BUSQUEDA DE DE UN SOLO REGISTRO
	//PARAMETRO ID DEl editor A BUSCAR
	public function show($id)
	{

		return view("paises.show",["paises"=>Paises::findOrFail($id)]);
	}

	//modificar los campos de l editor
	public function edit($id)
	{
		return view("paises.edit",["paises"=>Paises::findOrFail($id)]);
	}
	
	public function update(PaisesFormRequest $request, $id)
	{
		$pais=Paises::findOrFail($id);
		$pais->nombre=$request->get('nombre');
		$pais->update();
		return Redirect::to('paises');

	}
	
	public function destroy($id)
	{
		$pais=Paises::findOrFail($id);
		$pais->delete();
		return Redirect::to('paises');
	}
}
