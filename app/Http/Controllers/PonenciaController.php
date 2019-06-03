<?php

namespace sistema\Http\Controllers;

use Illuminate\Http\Request;
use sistema\Http\Requests;
use sistema\Ponencia;
use Illuminate\Support\Facades\Redirect;

use DB;
use sistema\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class PonenciaController extends Controller
{
    //


    public function __construct()
	{

    }

     public  function agregar($idDocumento,$request){

        $ponencia = new Ponencia;
        $ponencia->fk_doc =$idDocumento;
        $ponencia->evento = $request->get('ponencia-nombre')?$request->get('ponencia-nombre'):'';
        $ponencia->lugar_presentacion = $request->get('ponecia-lugar')?$request->get('ponecia-lugar'):'';
        $ponencia->fecha_pesentacion = $request->get('ponencia-fecha')?$request->get('ponencia-fecha'):'';
        $ponencia->paginas = $request->get('ponencia-pag')?$request->get('ponencia-pag'):'';
        $ponencia->save();



    }


    public static  function obtenerPonencia($idDocumento){

      

        $ponencia=DB::table('ponencias')
        ->where('fk_doc',$idDocumento)
        ->first();

        

        return $ponencia;
      

       
        
    }

    public function eliminar($idDocumento){

        $aux=Ponencia::where('fk_doc', '=', $idDocumento)->exists();
        if($aux){

            Ponencia::where('fk_doc', '=', $idDocumento)->delete();

        }


    }
   

    




}
