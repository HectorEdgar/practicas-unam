<?php

namespace sistema;

use Illuminate\Database\Eloquent\Model;

class DocumentoInstitucion extends Model
{
  protected $table='cntrl_instit';
  public $timestamps=false;

	protected $fillable =[
        'fk_doc',
        'fk_instit'
        
	]; 

	protected $guarded =[

	];
}
