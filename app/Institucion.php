<?php

namespace sistema;

use Illuminate\Database\Eloquent\Model;

class Institucion extends Model
{
    //
    protected $table='institucion';
	protected $primaryKey="Id_institucion";
	public $timestamps=false;


	protected $fillable =[
		'nombre',
		'siglas',
		'pais',
		'localidad',
		'extra'
	]; 

	protected $guarded =[

	];
}
