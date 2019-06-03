<?php

namespace sistema\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;  
use sistema\Http\Requests;
use sistema\Libro;
use Illuminate\Support\Facades\Redirect;
use DB;
use sistema\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;  

class LibroController extends Controller
{
     public  function agregar($idDocumento,$request){
        Log::warning($request);

        $libro = new Libro;
        $libro->fk_doc =$idDocumento;
        $libro->edicion =$request->get('libro-ed') ?$request->get('libro-ed'):'';
        $libro->traductor =$request->get('libro-traductor')?$request->get('libro-traductor'):'';
        $libro->prologo =$request->get('libro-resPrologo')?$request->get('libro-resPrologo'):'';
        $libro->introduccion =$request->get('libro-introduccion')?$request->get('libro-introduccion'):'';
        $libro->tomos =$request->get('libro-tomos')?$request->get('libro-tomos'):'';
        $libro->volumen =$request->get('libro-vol')?$request->get('libro-vol'):'';
        $libro->coleccion =$request->get('libro-col')?$request->get('libro-col'):'';
        $libro->nocol =$request->get('libro-nocol')?$request->get('libro-nocol'):'';
        $libro->serie =$request->get('libro-serie')?$request->get('libro-serie'):'';
        $libro->noserie =$request->get('libro-noserie')?$request->get('libro-noserie'):'';
        $libro->paginalib =$request->get('libro-pag')?$request->get('libro-pag'):'';

        $libro->save();



    }

    public static  function obtenerLibro($idDocumento){

        $libro =DB::table('libro')
        ->where('fk_doc',$idDocumento)
        ->first();

        return $libro;
        
    }


    public function eliminar($idDocumento){

        $aux=Libro::where('fk_doc', '=', $idDocumento)->exists();
        if($aux){

            Libro::where('fk_doc', '=', $idDocumento)->delete();

        }


    }
}
