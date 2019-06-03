<?php

namespace sistema;

use Illuminate\Database\Eloquent\Model;

class Descarga extends Model
{
    protected $table = 'descargas';

    protected $primaryKey = 'idDescarga';

    public $timestamps = false;

    protected $fillable = [
        'idDescarga',
        'idArchivo',
        'titulo',
        'url',
        'fechaIngreso',
        'tipoProyecto',
        'estado'
    ];

    protected $guarded = [];

    public function archivo()
    {
        return $this->hasOne("sistema\Archivo", "idArchivo", "idArchivo");
    }
}
