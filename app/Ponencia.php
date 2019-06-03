<?php

namespace sistema;

use Illuminate\Database\Eloquent\Model;

class Ponencia extends Model
{
	protected $table='ponencias';
	
	protected $primaryKey=null;
	public $timestamps=false;


	protected $fillable =[


		

		'fk_doc',
        'evento',
        'lugar_presentacion',
        'fecha_pesentacion',
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
}
