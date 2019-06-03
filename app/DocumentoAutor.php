<?php

namespace sistema;

use Illuminate\Database\Eloquent\Model;

class DocumentoAutor extends Model
{
    protected $table='cntrl_autor';
	
	protected $primaryKey='orden';
	public $timestamps=false;


	protected $fillable =[
        'orden',
        'fk_doc',
        'fk_autor',
        'extra'
	]; 

	protected $guarded =[

	];
}
