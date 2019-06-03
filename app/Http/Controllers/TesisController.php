<?php

namespace sistema\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;  
use sistema\Http\Requests;
use sistema\Tesis;
use Illuminate\Support\Facades\Redirect;
use DB;
use sistema\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;  

class TesisController extends Controller
{
     public  function agregar($idDocumento,$request){

        $tesis = new Tesis;
        $tesis->fk_doc =$idDocumento;
        $tesis->asesor =$request->get('tesis-asesor')?$request->get('tesis-asesor'):'';
        $tesis->grado =$request->get('tesis-grado')?$request->get('tesis-grado'):'';
        $tesis->num_paginas =$request->get('tesis-pag')?$request->get('tesis-pag'):'';
        $tesis->save();






    }


    public static  function obtenerTesis($idDocumento){

        $tesis =DB::table('tesis')
        ->where('fk_doc',$idDocumento)
        ->first();

        return $tesis;
        
    }

    public function eliminar($idDocumento){

        $aux=Tesis::where('fk_doc', '=', $idDocumento)->exists();
        if($aux){

            Tesis::where('fk_doc', '=', $idDocumento)->delete();
        }


    }
   
}
