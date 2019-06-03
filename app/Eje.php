<?php

namespace sistema;

use Illuminate\Database\Eloquent\Model;

class Eje extends Model
{
    protected $table = 'eje';

    protected $primaryKey = 'Id_eje';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'area',
        'poblacion'
    ];

    protected $guarded = [];
}
