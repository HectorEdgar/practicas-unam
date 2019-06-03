<?php

namespace sistema;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{

    protected $table = 'log';
    protected $primaryKey = "idLog";
    
    public $timestamps = false;


    protected $fillable = [
        'idTipoCambio',
        'idUsuario',
        'descripcion',
        'sentenciaSql',
        'tabla',
        'fechaCreacion'
    ];

    protected $guarded = [];

    public function tipoCambio() {
        return $this->hasOne("sistema\TipoCambio", "idTipoCambio", "idTipoCambio");
    }

    public function usuario(){
        return $this->hasOne("sistema\User", "id","id");
    }
}
