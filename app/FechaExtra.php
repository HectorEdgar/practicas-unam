<?php

namespace sistema;

use Illuminate\Database\Eloquent\Model;

class FechaExtra extends Model
{
    protected $table='fecha_extra';
	
	protected $primaryKey=null;
	public $timestamps=false;


	protected $fillable =[
        'mes',
        'mes2',
		'anio',
		'id_fx'
		
	]; 

	const UPDATED_AT = null;

    public function setUpdatedAt($value)
    {
        return $this;
	}
	public $incrementing = false;

	protected $guarded =[

	];
}
