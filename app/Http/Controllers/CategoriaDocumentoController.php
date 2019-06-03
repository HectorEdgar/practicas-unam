<?php

namespace sistema\Http\Controllers;

use Illuminate\Http\Request;
use sistema\Http\Requests;
use sistema\CategoriaDocumento;
use Illuminate\Support\Facades\Redirect;
use sistema\Http\Requests\CategoriaDocumentoFormRequest;
use DB;
use sistema\Http\Controllers\Controller;
use sistema\Utilidad;
use Illuminate\Support\Facades\Auth;

class CategoriaDocumentoController extends Controller
{


    public function __construct()
    {
       // $this->middleware('auth');

    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function index(Request $request)
    {
        if (!Auth::user()->authorizeRoles(['admin','revisor'])) {return Redirect::to('/');}
        if ($request) {
            $query = trim($request->get('searchText'));
            $categoriaDoc = DB::table('catalogo_docu')
                ->where('tipo_doc', 'LIKE', '%' . $query . '%')
                ->orwhere('id_cata_doc', 'LIKE', '%' . $query . '%') // duda por ser string
                ->orderBy('id_cata_doc', 'desc')
                ->paginate(10);
            $numeroRegistros = DB::table('catalogo_docu')
                ->where('tipo_doc', 'LIKE', '%' . $query . '%')
                ->orwhere('id_cata_doc', 'LIKE', '%' . $query . '%') // duda por ser string
                ->orderBy('id_cata_doc', 'desc')
                ->count();
            $page = $request->get('page') != null ? $request->get('page') : 1;
            
            return view('categoriaDocumento.index', 
                [
                    'categoriasDocumento' => $categoriaDoc, 
                    "searchText" => $query,
                    "totalRegistros" => $numeroRegistros,
                    "page" => $page
                ]  
            );
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         return view('categoriaDocumento.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
   public function store(CategoriaDocumentoFormRequest $request)
    {
        if (!Auth::user()->authorizeRoles([ 'admin','revisor'])) {return Redirect::to('/');}
        $categoriaDocumento=new CategoriaDocumento;
        $categoriaDocumento->id_cata_doc=Utilidad::getId("catalogo_docu","id_cata_doc");
        $categoriaDocumento->tipo_doc=$request->get('tipo_doc');
        $categoriaDocumento->save();
        return Redirect::to('categoriaDocumento');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!Auth::user()->authorizeRoles([ 'admin','revisor'])) {return Redirect::to('/');}
        return view("categoriaDocumento.show",["categoriasDocumento"=>CategoriaDocumento::findOrFail($id)]);
    }
    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!Auth::user()->authorizeRoles([ 'admin','revisor'])) {return Redirect::to('/');}
        return view("categoriaDocumento.edit",["categoriasDocumento"=>CategoriaDocumento::findOrFail($id)]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CategoriaDocumentoFormRequest $request, $id)
    {
        if (!Auth::user()->authorizeRoles([ 'admin','revisor'])) {return Redirect::to('/');}
       $categoriaDocumento=CategoriaDocumento::findOrFail($id);
       $categoriaDocumento->tipo_doc=$request->get('tipo_doc');
        $categoriaDocumento->update();
    return Redirect::to('categoriaDocumento');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!Auth::user()->authorizeRoles([ 'admin','revisor'])) {return Redirect::to('/');}
       $categoriaDocumento=CategoriaDocumento::findOrFail($id);
        $categoriaDocumento->delete();
        return Redirect::to('categoriaDocumento');
    }
}
