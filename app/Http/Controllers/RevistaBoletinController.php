<?php

namespace sistema\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;  
use sistema\Http\Requests;
use sistema\RevistaBoletin;
use Illuminate\Support\Facades\Redirect;
use DB;
use sistema\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;  
class RevistaBoletinController extends Controller
{
     public  function agregar($idDocumento,$request)
    {

        
        Log::warning($idDocumento);
        Log::warning($request);
        switch ((int)$request->get('tipo')) {
            case 14:
                    $revistBoletin = new RevistaBoletin;
                    $revistBoletin->fk_doc =$idDocumento;
                    $revistBoletin->num_revista = $request->get('artRevista-num')?$request->get('artRevista-num'):'';
                    $revistBoletin->nombre_revista = $request->get('artRevista-nombre')? $request->get('artRevista-nombre'):'';
                    $revistBoletin->pag = $request->get('artRevista-pag')?$request->get('artRevista-pag'):'';
                    $revistBoletin->volumen = $request->get('artRevista-vol')?$request->get('artRevista-vol'):'';
                    $revistBoletin->anio = $request->get('artRevista-anio')?$request->get('artRevista-anio'):'';
                    $revistBoletin->save();
                    break;
            case 18:
                    $revistBoletin = new RevistaBoletin;
                    $revistBoletin->fk_doc =$idDocumento;
                    $revistBoletin->num_revista = $request->get('artBoletin-num')?$request->get('artBoletin-num'):'';
                    $revistBoletin->nombre_revista = $request->get('artBoletin-nombre')?$request->get('artBoletin-nombre'):'';
                    $revistBoletin->pag = $request->get('artBoletin-pag')?$request->get('artBoletin-pag'):'';
                    $revistBoletin->volumen = $request->get('artBoletin-vol')?$request->get('artBoletin-vol'):'';
                    $revistBoletin->anio = $request->get('artBoletin-anio')?$request->get('artBoletin-anio'):'';
                    $revistBoletin->save();
                    break;
            case 2:
                    $revistBoletin = new RevistaBoletin;
                    $revistBoletin->fk_doc =$idDocumento;
                    $revistBoletin->num_revista = $request->get('boletinRevista-num')?$request->get('boletinRevista-num'):'';
                    $revistBoletin->pag = $request->get('boletinRevista-pag')?$request->get('boletinRevista-pag'):'';
                    $revistBoletin->volumen = $request->get('boletinRevista-vol')?$request->get('boletinRevista-vol'):'';
                    $revistBoletin->anio = $request->get('boletinRevista-anio')?$request->get('boletinRevista-anio'):'';
                    $revistBoletin->save();
                     break;
            case 17:
                     $revistBoletin = new RevistaBoletin;
                     $revistBoletin->fk_doc =$idDocumento;
                     $revistBoletin->num_revista = $request->get('revista-num')?$request->get('revista-num'):'';
                     $revistBoletin->pag = $request->get('revista-pag')?$request->get('revista-pag'):'';
                     $revistBoletin->volumen = $request->get('revista-vol')?$request->get('revista-vol'):'';
                     $revistBoletin->anio = $request->get('revista-anio')?$request->get('revista-anio'):'';
                     $revistBoletin->save();
                     break;
                     
                    }

     
    }



    public static  function obtenerRevistBoletin($idDocumento){

        $revistaBoletin =DB::table('revista_boletin')
        ->where('fk_doc',$idDocumento)
        ->first();

        return $revistaBoletin;
        
    }

    public function eliminar($idDocumento){
        //Comprueba que el registro exista en la base de datos, si existe lo elimina.
        $aux=RevistaBoletin::where('fk_doc', '=', $idDocumento)->exists();
        if($aux){
            RevistaBoletin::where('fk_doc', '=', $idDocumento)->delete();
        }


    }
}
