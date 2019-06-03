<?php

namespace sistema;

use Illuminate\Database\Eloquent\Model;

class ObraObra extends Model
{
   protected $table='obra_obra';
   public $timestamps=false;


	protected $fillable =[
        'fk_obra',
        'fk_obra2'
	]; 

	protected $guarded =[

	];
}
