<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log', function (Blueprint $table) {
            $table->increments('idLog');
            $table->unsignedInteger('idTipoCambio');
            $table->unsignedInteger('idUsuario');
            $table->string('descripcion');
            $table->string('sentenciaSql');
            $table->string('tabla');
            $table->dateTime('fechaCreacion');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log');
    }
}
