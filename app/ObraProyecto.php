<?php

namespace sistema;

use Illuminate\Database\Eloquent\Model;

class ObraProyecto extends Model
{
   protected $table='obra_proyec';
   public $timestamps=false;


	protected $fillable =[
        'fk_proyec',
        'fk_obra'
	]; 

	protected $guarded =[

	];
}
