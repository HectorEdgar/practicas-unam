<?php

namespace sistema;

use Illuminate\Database\Eloquent\Model;

class Autor extends Model
{
    protected $table = 'autor';

    protected $primaryKey = 'Id_autor';

    public $timestamps = false;

    protected $fillable = [
        'pseudonimo',
        'nombre',
        'apellidos'
    ];

    protected $guarded = [];
}
