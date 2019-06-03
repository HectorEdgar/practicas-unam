<?php

namespace sistema;

use Illuminate\Database\Eloquent\Model;

class Obra extends Model
{
    protected $table = 'obras';

    protected $primaryKey = 'id_obra';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'extra',
        'revisado'
    ];
}
