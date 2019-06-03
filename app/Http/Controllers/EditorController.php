<?php

namespace sistema\Http\Controllers;

use Illuminate\Http\Request;

use sistema\Http\Requests;
use sistema\Editor;
use Illuminate\Support\Facades\Redirect;
use sistema\Http\Requests\EditorFormRequest;
use DB;
use sistema\Utilidad;

use sistema\Http\Controllers\Controller;

class EditorController extends Controller
{
    //
    //CONSTRUCTOR
	public function __construct()
	{
		//si no esta logeado regresa al login
        //ES COMO EL PINCHE SPRING SECURITY :v
        $this->middleware('auth');
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
			$editores=DB::table('editor')
			->where('editor','LIKE','%'.$query.'%')
			->orwhere('id_editor','LIKE','%'.$query.'%')
			->orwhere('pais','LIKE','%'.$query.'%')
			->orwhere('estado','LIKE','%'.$query.'%')
			->orwhere('der_autor','LIKE','%'.$query.'%')
			->orderBy('id_editor','desc')
			->paginate(10);


			$page = $request->get('page') != null ? $request->get('page') : 1;

			$numeroRegistros = DB::table('editor')
				->where('editor', 'LIKE', '%' . $query . '%')
				->orwhere('id_editor', 'LIKE', '%' . $query . '%')
				->orwhere('pais','LIKE','%'.$query.'%')
				->orwhere('estado','LIKE','%'.$query.'%')
				->orwhere('der_autor','LIKE','%'.$query.'%')
				->orderBy('id_editor', 'desc')
				->count();


			return view('editor.index',
				[
					"editores"=>$editores,
					"searchText"=>$query,
					"totalRegistros" => $numeroRegistros,
					"page" => $page
				]
			);
		}
	}


	public function create()
	{

		return view("editor.create");
	}

	public function store(EditorFormRequest $request)
	{

		$editor=new Editor;
		$editor->id_editor=Utilidad::getId("editor","id_editor");

		$editor->editor=$request->get('editor');

		$editor->pais=$request->get('pais')==null? '' :$editor->pais=$request->get('pais');
		$editor->estado=$request->get('estado')==null? '' :$editor->estado=$request->get('estado');
		$editor->der_autor=$request->get('der_autor')==null? '' :$editor->der_autor=$request->get('der_autor');



		//activar el log de la base de datos
		DB::connection()->enableQueryLog();
		$editor->save();
		//1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
		LogController::agregarLog(
			1,
			"editor",
			"Se agregó el editor: ". json_encode($editor)
		);

		//Session::flash('Aviso', 'Se ingresó: Pseudónimo:'.$AuxPseudonimo.' | Nombre: '.$AuxNombre.' | Apellidos: '.$AuxApellidos);

		return Redirect::to('editor');
	}

	//BUSQUEDA DE DE UN SOLO REGISTRO
	//PARAMETRO ID DEl editor A BUSCAR
	public function show($id)
	{
		//$this->validarRoles();
        //DOCUMENTOS DE editor
        //Tabla Cntrl_editor

        /*$documentos =DB::table('cntrl_autor as ctp')
            ->join('documento as doc','doc.Id_doc','=','ctp.fk_doc')
            ->where('fk_autor',$id)
            ->orderBy('doc.Id_doc', 'desc')
            ->get();
		*/
		$documentos =DB::table('cntrl_editor as cntl')
            ->join('documento as doc','doc.Id_doc','=','cntl.fk_doc')
            ->where('cntl.fk_editor',$id)
            ->orderBy('doc.Id_doc', 'desc')
            ->get();
        return view('editor.verDetalle',
        ["editor" => Editor::findOrFail($id),
        "documentos" => $documentos
		]);


		return view("editor.show",["editor"=>Editor::findOrFail($id)]);
	}

	//modificar los campos de l editor
	public function edit($id)
	{
		return view("editor.edit",["editor"=>Editor::findOrFail($id)]);

	}

	public function update(EditorFormRequest $request, $id)
	{
		$editor=Editor::findOrFail($id);
		$editor->editor=$request->get('editor');

		$editor->pais=$request->get('pais')==null? '' :$editor->pais=$request->get('pais');
		$editor->estado=$request->get('estado')==null? '' :$editor->estado=$request->get('estado');
		$editor->der_autor=$request->get('der_autor')==null? '' :$editor->der_autor=$request->get('der_autor');

		//activar el log de la base de datos
		DB::connection()->enableQueryLog();
		$editor->update();
		//1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
		LogController::agregarLog(
			2,
			"editor",
			"Se editó el editor: ". json_encode($editor)
		);



		return Redirect::to('editor');

	}

	public function destroy($id)
	{
		$editor = DB::table('editor')
			->where('id_editor', '=',$id)
			->first();

		$editor=Editor::findOrFail($id);

		//activar el log de la base de datos
		DB::connection()->enableQueryLog();
		$editor->delete();
		//1.-Agregar, 2.-Actualizar, 3.-Eliminar, 4.-Consultar
		LogController::agregarLog(
			3,
			"editor",
			"Se eliminó el editor: ". json_encode($editor)
		);
		return Redirect::to('editor');
	}

}
