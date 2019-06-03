<?php

namespace sistema;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $table = 'video';

    protected $primaryKey = null;
    public $timestamps = false;

    protected $fillable = [
        'fk_doc',
        'secundario',
        'director',
        'productor',
        'realizador',
        'conductor',
        'reportero',
        'guionista',
        'fotografia',
        'musica',
        'actores',
        'narrador',
        'fecha_trans',
        'hora_trans',
        'idioma',
        'subtitulo',
        'formato',
        'duracion',
        'programa',
        'canal'
    ];

    protected $guarded = [];
}
