<?php

namespace sistema;

use Illuminate\Database\Eloquent\Model;

class Etnia extends Model
{
    protected $table = 'etnia';

    protected $primaryKey = 'id_etnia';

    public $timestamps = false;

    protected $fillable = [
        'id_etnia',
        'nombre',
        'nombre2',
        'territorio',
        'familia'
    ];

    protected $guarded = [];
}
