<?php

namespace sistema;

use Illuminate\Database\Eloquent\Model;

class LugarEtnia extends Model
{

   protected $table='cntrl_etnia';
   public $timestamps=false;


	protected $fillable =[
        'fk_etnia',
        'fk_lugar',
        'latitud',
        'longitud'
	]; 

	protected $guarded =[

	];
}
