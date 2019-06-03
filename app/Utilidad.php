<?php

namespace sistema;

use DB;
use Illuminate\Support\Facades\Log;

class Utilidad
{

    private static function getMaxId($nombreTabla,$nombreId)
    {
        $query = DB::table($nombreTabla)->max($nombreId);
        return $query;
    }

    public static function getCount($nombreTabla)
    {
         return DB::table($nombreTabla)->count();
    }
    //Devuelve el id que le corresponde una nueva inserción dependiendo si hay huecos en la serie de id´s 
    //si hay huecos devuelve el id correspondiente al hueco ordenado de menor a mayor y si no devuelve el id mayor + 1 :´v
    //Id 100% real No FAKE!!
    public static function getId($nombreTabla, $nombreId)
    {
        $maxId=Utilidad::getMaxId($nombreTabla, $nombreId);
        $numeroRegistros = DB::table($nombreTabla)->count();

        if($numeroRegistros==$maxId){
            return $maxId+1;
        }else{
            $ids=DB::table($nombreTabla)->orderBy($nombreId, 'asc')->pluck($nombreId);
            $cont=1;
            $auxId=0;
            foreach ($ids as $id) {
                if($id==$cont){
                    $auxId=$cont;
                }else{
                    return $auxId+1;
                }
                $cont=$cont+1;
            }
        }
        
    }

    public static function getFecha($fecha){

                $dia=substr($fecha,8,2);
                $mes=substr($fecha,5,2);
                $anio=substr($fecha,0,4);
                
                switch ($mes) {
                    case 1:
                    $mes="enero";
                    break;
                    
                    case 2:
                    $mes="febrero";
                    break;
                    
                    case 3:
                    $mes="marzo";
                    break;
                    
                    case 4;
                    $mes="abril";
                    break;
                    
                    case 5;
                    $mes="mayo";
                    break;
                    
                    case 6;
                    $mes="junio";
                    break;
                    
                    case 7;
                    $mes="julio";
                    break;
                    
                    
                    case 8;
                    $mes="agosto";
                    break;
                    
                    case 9;
                    $mes="septiembre";
                    break;
                    
                    case 10;
                    $mes="octubre";
                    break;
                    
                    case 11;
                    $mes="noviembre";
                    break;
                    
                    case 12;
                    $mes="diciembre";
                    
                break;
                }

                $fechaRetornada = "";
                    /*Obteniendo cadena fecha Fecha*/
                $fechaRetornada = $dia.' de '.$mes.' de '.$anio;
                
                if($dia=="00"){
                    $fechaRetornada=$mes.' de '.$anio.'';
                }		
                if($mes=="00"){
                    $fechaRetornada=$anio.'';	
                }
                if($anio=="00"){
                    $fechaRetornada= '[s.f.]';	
                }
                
                
            return $fechaRetornada;


    }


    public static function getFechaConsulta($fecha){
	
       
        $dia=substr($fecha,8,2);
        $mes=substr($fecha,5,2);
        $anio=substr($fecha,0,4);
        
        return  $dia.'/'.$mes.'/'.$anio.'.';
    
    
   
    }
    
}