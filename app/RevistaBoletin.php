<?php

namespace sistema;

use Illuminate\Database\Eloquent\Model;

class RevistaBoletin extends Model
{
    protected $table='revista_boletin';
	
	protected $primaryKey=null;
	public $timestamps=false;


	protected $fillable =[
		'fk_doc',
        'num_revista',
        'nombre_revista',
        'pag',
        'volumen',
        'anio'


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
