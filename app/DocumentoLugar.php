<?php

namespace sistema;

use Illuminate\Database\Eloquent\Model;

class DocumentoLugar extends Model
{

  protected $table='cntrl_lugar';
  public $timestamps=false;

	protected $fillable =[
        'fk_doc',
        'fk_lugar'
        
	]; 

	protected $guarded =[

	];
}
