<?php

namespace sistema;

use Illuminate\Database\Eloquent\Model;

class DocumentoSubtema extends Model
{
  
  protected $table='cntrl_sub';
  public $timestamps=false;

	protected $fillable =[
        'fk_doc',
        'fk_sub'
	]; 

	protected $guarded =[

	];
}
