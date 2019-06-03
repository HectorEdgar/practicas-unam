<?php

namespace sistema;

use Illuminate\Database\Eloquent\Model;

class ObraEje extends Model
{
   protected $table='eje_obra';
   public $timestamps=false;


	protected $fillable =[
        'fk_eje',
        'fk_obra'
	]; 

	protected $guarded =[

	];
}
