<?php

use Illuminate\Database\Seeder;
use sistema\TipoCambio;

class TipoCambioTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tipoCambio=new TipoCambio();
        $tipoCambio->tipoCambio="Alta";
        $tipoCambio->save();
        $tipoCambio = new TipoCambio();
        $tipoCambio->tipoCambio = "Actualización";
        $tipoCambio->save();
        $tipoCambio = new TipoCambio();
        $tipoCambio->tipoCambio = "Eliminación";
        $tipoCambio->save();
        $tipoCambio = new TipoCambio();
        $tipoCambio->tipoCambio="Consulta";
        $tipoCambio->save();

    }
}
