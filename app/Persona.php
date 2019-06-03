<?php

namespace sistema;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
     protected $table = 'persona';

    protected $primaryKey = 'Id_persona';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'apellidos',
        'cargo',
        'extra'
    ];

    protected $guarded = [];
}
