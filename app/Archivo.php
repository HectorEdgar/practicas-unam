<?php

namespace sistema;

use Illuminate\Database\Eloquent\Model;

class Archivo extends Model
{
    protected $table = 'archivos';

    protected $primaryKey = 'idArchivo';

    public $timestamps = false;

    protected $fillable = [
        'idArchivo',
        'nombre',
        'ruta',
        'tipoArchivo',
        'peso'
    ];

    protected $guarded = [];
}
