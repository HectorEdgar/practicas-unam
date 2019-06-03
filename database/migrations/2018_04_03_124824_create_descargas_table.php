<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDescargasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('descargas', function (Blueprint $table) {
            $table->increments('idDescarga');
            $table->unsignedInteger('idArchivo');
            $table->string('titulo');
            $table->string('url')->nullable();
            $table->dateTime('fechaIngreso');
            $table->string('tipoProyecto')->nullable();
            $table->string('estado');
            //$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('descargas');
    }
}
