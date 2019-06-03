<?php

namespace sistema;

use Illuminate\Database\Eloquent\Model;

class Paises extends Model
{
    protected $table='paises';
	protected $primaryKey='id_pais';
	public $timestamps=false;


	protected $fillable =[
		'nombre'
		
	]; 

	protected $guarded =[];
}
