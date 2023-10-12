<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListaAsistenciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lista_asistencias', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_actividad')->nullable();
            $table->unsignedInteger('id_asistencia')->nullable();
            $table->unsignedInteger('id_trabajador')->nullable();
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
        Schema::dropIfExists('lista_asistencias');
    }
}
