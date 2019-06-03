<?php

namespace sistema;

use Illuminate\Database\Eloquent\Model;

class DocumentoTema extends Model
{
  protected $table='cntrl_tema';
  public $timestamps=false;

	protected $fillable =[
        'fk_doc',
        'fk_tema'
	]; 

	protected $guarded =[

	];
}
