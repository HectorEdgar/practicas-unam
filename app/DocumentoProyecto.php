<?php

namespace sistema;

use Illuminate\Database\Eloquent\Model;

class DocumentoProyecto extends Model
{
  protected $table='cntrl_proyec';
  public $timestamps=false;

	protected $fillable =[
        'fk_doc',
        'fk_proyec'
        
	]; 

	protected $guarded =[

	];
}
