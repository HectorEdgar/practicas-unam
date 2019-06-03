<?php

namespace sistema\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;  
use sistema\Http\Requests;
use sistema\FechaExtra;
use Illuminate\Support\Facades\Redirect;
use DB;
use sistema\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;  

class FechaExtraController extends Controller
{
     public  function agregarFechaExtra($idDocumento,$request)
    {

        $fechaExtra = new FechaExtra;
        $fechaExtra->id_fx = $idDocumento; //referencia id doc a la fecha extra
        $fechaExtra->mes =  $request->get('fechaExtraMes');
        $fechaExtra->mes2 =  $request->get('fechaExtraAlMes');
        $fechaExtra->anio =  $request->get('fechaExtraAño');

        $fechaExtra->save();


     
    }
    
   
 static public  function obtenerFechaExtra($idDocumento)
    {

        $aux=DB::table('fecha_extra')->where('id_fx', '=', $idDocumento)->first();
        if($aux!=null){
            return $aux;


        }else{

            return '';
        }
      
     
    }


     public  function actualizarFechaExtra($idDocumento,$request)
    {
        //no implementado

        $fechaExtra = self::obtenerFechaExtra($idDocumento);
        $fechaExtra->mes =  $request->get('fechaExtraMes');
        $fechaExtra->mes2 =  $request->get('fechaExtraAlMes');
        $fechaExtra->anio =  $request->get('fechaExtraAño');

        $fechaExtra->save();


     
    }

    public  function eliminarFecha($idDocumento)
    {
        
        $aux=FechaExtra::where('id_fx', '=', $idDocumento)->exists();
        if($aux){

            FechaExtra::where('id_fx', '=', $idDocumento)->delete();

        }
           

    
       
     
    }

}
