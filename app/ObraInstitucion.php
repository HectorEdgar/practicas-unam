<?php

namespace sistema;

use Illuminate\Database\Eloquent\Model;

class ObraInstitucion extends Model
{
   protected $table='obra_inst';
   public $timestamps=false;


	protected $fillable =[
        'fk_obra',
        'fk_inst',
        'extra'
	]; 

	protected $guarded =[

	];
}
