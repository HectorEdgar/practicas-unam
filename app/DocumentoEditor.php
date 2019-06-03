<?php

namespace sistema;

use Illuminate\Database\Eloquent\Model;

class DocumentoEditor extends Model
{
    //nombre de la tabla
    protected $table='cntrl_editor';
	
	protected $primaryKey='orden';
	public $timestamps=false;


	protected $fillable =[
        'orden',
        'fk_doc',
        'fk_editor'
	]; 

	protected $guarded =[

	];
}
