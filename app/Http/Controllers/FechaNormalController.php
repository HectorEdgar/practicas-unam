<?php

namespace sistema\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;  
use sistema\Http\Requests;
use sistema\FechaNormal;
use Illuminate\Support\Facades\Redirect;
use DB;
use sistema\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;  

class FechaNormalController extends Controller
{

    public  function agregarFechaNormal($idDocumento,$request)
    {

      
            $fechaNormal = new FechaNormal;
            $fechaNormal->fk_doc = $idDocumento; //referencia id doc a la fecha normal
            $fechaNormal->fecha =  $request->get('fechaNormalValor')?$request->get('fechaNormalValor'):'0000-00-00';
            $fechaNormal->save();
           
    

     
    }

   
    static public  function obtenerFechaNormal($idDocumento)
    {

        $aux=DB::table('fecha')->where('fk_doc', '=', $idDocumento)->first();

      

        return  $aux!=null? $aux->fecha : "0000-00-00";

        
     
    }

     private  function obtenerFechaNormalInterna($idDocumento)
     
    {

        Log::warning($idDocumento);

        $aux=FechaNormal::where('fk_doc', '=', $idDocumento)->firstOrFail();
        return $aux;
     
    }

    public  function actualizarFechaNormal($idDocumento,$request)
    {
        ///no implementada


        $fechaNormal = self::obtenerFechaNormalInterna($idDocumento);

        

        $fechaNormal->fecha =  $request->get('fechaNormalValor')?$request->get('fechaNormalValor'):'0000-00-00';

        $fechaNormal->save();

    }

    public function eliminarFecha($idDocumento)
    {

        Log::warning($idDocumento);

        $aux=FechaNormal::where('fk_doc', '=', $idDocumento)->exists();

        Log::warning($aux);
       
        if($aux){

            FechaNormal::where('fk_doc', '=', $idDocumento)->delete();


        }
       
    }

    
}
