<?php

namespace sistema;

use Illuminate\Database\Eloquent\Model;

class Subtema extends Model
{
    protected $table = 'subtema';

    protected $primaryKey = 'id_sub';

    public $timestamps = false;

    protected $fillable = [
        'subtema',
        'extra'
    ];
}
