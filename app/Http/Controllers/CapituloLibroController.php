<?php

namespace sistema\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;  
use sistema\Http\Requests;
use sistema\CapituloLibro;
use Illuminate\Support\Facades\Redirect;
use DB;
use sistema\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;  

class CapituloLibroController extends Controller
{

     public  function agregar($idDocumento,$request){

        $capLibro = new CapituloLibro;
        $capLibro->fk_doc =$idDocumento;
        $capLibro->nombre_libro =$request->get('capLibro-nombre')?$request->get('capLibro-nombre'):'';
        $capLibro->autorgral =$request->get('capLibro-autor')?$request->get('capLibro-autor'):'';
        $capLibro->edicion =$request->get('capLibro-ed')?$request->get('capLibro-ed'):'';
        $capLibro->tomos =$request->get('capLibro-tomo')?$request->get('capLibro-tomo'):'';
        $capLibro->volumen =$request->get('capLibro-volumen')?$request->get('capLibro-volumen'):'';
        $capLibro->coleccion =$request->get('capLibro-col')?$request->get('capLibro-col'):'';
        $capLibro->nocol =$request->get('capLibro-Numcole')?$request->get('capLibro-Numcole'):'';
        $capLibro->serie =$request->get('capLibro-serie')?$request->get('capLibro-serie'):'';
        $capLibro->noserie =$request->get('capLibro-serie')?$request->get('capLibro-serie'):'';
        $capLibro->traductor =$request->get('capLibro-traductor')?$request->get('capLibro-traductor'):'';
        $capLibro->paginas =$request->get('capLibro-pag')?$request->get('capLibro-pag'):'';

        $capLibro->save();






    }


    public static  function obtenerCapituloLibro($idDocumento){

        $libro =DB::table('capitulo_libro')
        ->where('fk_doc',$idDocumento)
        ->first();

        return $libro;
        
    }

    public function eliminar($idDocumento){

        $aux=CapituloLibro::where('fk_doc', '=', $idDocumento)->exists();
        if($aux){

            CapituloLibro::where('fk_doc', '=', $idDocumento)->delete();

        }


    }

    
}
