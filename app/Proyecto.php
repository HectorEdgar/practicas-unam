<?php

namespace sistema;

use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    protected $table = 'catalogo_proyecto';

    protected $primaryKey = 'id_proyecto';

    public $timestamps = false;

    protected $fillable = [
        'proyecto',
    ];

    protected $guarded = [];
}
