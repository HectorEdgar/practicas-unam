<?php

namespace sistema;

use Illuminate\Database\Eloquent\Model;

class CapituloLibro extends Model
{
    protected $table='capitulo_libro';
	
	protected $primaryKey=null;
	public $timestamps=false;


	protected $fillable =[
		'fk_doc',
        'nombre_libro',
        'autorgral',
        'edicion',
        'tomos',
        'volumen',
        'coleccion',
        'nocol',
        'serie',
        'noserie',
        'traductor',
        'paginas'



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
