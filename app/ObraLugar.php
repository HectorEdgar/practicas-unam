<?php

namespace sistema;

use Illuminate\Database\Eloquent\Model;

class ObraLugar extends Model
{
   
   protected $table='cntrl_ubic';
   public $timestamps=false;


	protected $fillable =[
        'fk_obra',
        'fk_lugar',
        'latitud',
        'longitud',
        'complejo'
	]; 

	protected $guarded =[

	];
}
