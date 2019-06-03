<?php

namespace sistema;

use Illuminate\Database\Eloquent\Model;

class Tema extends Model
{
    protected $table = 'temas';

    protected $primaryKey = 'id_tema';

    public $timestamps = false;

    protected $fillable = [
        'descripcion'
    ];

    protected $guarded = [];
}
