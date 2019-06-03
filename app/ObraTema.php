<?php

namespace sistema;

use Illuminate\Database\Eloquent\Model;

class ObraTema extends Model
{
   protected $table='obra_tema';
   public $timestamps=false;


	protected $fillable =[
        'fk_tema',
        'fk_obra'
	]; 

	protected $guarded =[

	];
}
