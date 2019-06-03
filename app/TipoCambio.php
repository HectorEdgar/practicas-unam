<?php

namespace sistema;

use Illuminate\Database\Eloquent\Model;
use sistema\Log;

class TipoCambio extends Model
{
    protected $table = 'tipocambio';

    protected $primaryKey = 'idTipoCambio';

    public $timestamps = false;

    protected $fillable = [
        'tipoCambio',
    ];

    protected $guarded = [];


}
