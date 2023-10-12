<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateYacimientosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yacimientos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_mina')->default(0);
            $table->string('codigo', 20)->nullable();
            $table->string('nombre', 70)->nullable();
            $table->string('direccion', 140)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('celular', 20)->nullable();
            $table->string('correo')->nullable();
            $table->string('google_map', 70)->nullable();
            $table->unsignedInteger('id_estado')->default(0); 
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
        Schema::dropIfExists('yacimientos');
    }
}
