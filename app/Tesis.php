<?php

namespace sistema;

use Illuminate\Database\Eloquent\Model;

class Tesis extends Model
{
    protected $table='tesis';
	
	protected $primaryKey=null;
	public $timestamps=false;


	protected $fillable =[
		'fk_doc',
        'grado',
        'asesor',
        'num_paginas'


	]; 

	const UPDATED_AT = null;

    public function setUpdatedAt($value)
    {
        return $this;
	}
	public $incrementing = false;

	protected $guarded =[

	];
    //
}
