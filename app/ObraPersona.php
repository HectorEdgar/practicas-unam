<?php

namespace sistema;

use Illuminate\Database\Eloquent\Model;

class ObraPersona extends Model
{
   protected $table='obra_persona';
   public $timestamps=false;


	protected $fillable =[
        'fk_persona',
        'fk_obra'
	]; 

	protected $guarded =[

	];
}
