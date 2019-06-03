<?php

namespace sistema;

use Illuminate\Database\Eloquent\Model;

class Libro extends Model
{
    protected $table='libro';
	
	protected $primaryKey=null;
	public $timestamps=false;


	protected $fillable =[
		'fk_doc',
        'edicion',
        'traductor',
        'prologo',
        'introduccion',
        'tomos',
        'volumen',
        'coleccion',
        'nocol',
        'serie',
        'noserie',
        'paginalib'


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
