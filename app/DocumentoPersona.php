<?php

namespace sistema;

use Illuminate\Database\Eloquent\Model;

class DocumentoPersona extends Model
{
  protected $table='cntrl_persona';
  public $timestamps=false;

	protected $fillable =[
        'fk_doc',
        'fk_persona'
        
	]; 

	protected $guarded =[

	];
}
