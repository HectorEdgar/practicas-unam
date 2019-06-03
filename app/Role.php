<?php

namespace sistema;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

class Role extends Model
{
    protected $table = 'role';


    protected $primaryKey = 'id';


    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    protected $guarded = [];

    public function users()
    {
        return $this->belongsToMany('App\User')->withTimestamps();
    }
}
