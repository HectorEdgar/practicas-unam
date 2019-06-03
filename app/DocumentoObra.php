<?php

namespace sistema;

use Illuminate\Database\Eloquent\Model;

class DocumentoObra extends Model
{
  protected $table='obra_doc';
  public $timestamps=false;

	protected $fillable =[
        'fk_doc',
        'fk_obra',
        'status',
        'revisado',
        'investigador'
	]; 

	protected $guarded =[

  ];
  


 
}
