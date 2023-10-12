<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMinasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('minas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 70)->nullable();
            $table->string('codigo', 20)->nullable();
            $table->string('correo')->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('celular', 20)->nullable();
            $table->unsignedInteger('id_moneda')->default(0);
            $table->string('logo', 255)->nullable();
            $table->unsignedInteger('id_pais')->default(0);
            $table->unsignedInteger('id_departamento')->default(0);
            $table->unsignedInteger('id_provincia')->default(0);
            $table->unsignedInteger('id_distrito')->default(0);
            $table->string('direccion', 150)->nullable();
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
        Schema::dropIfExists('minas');
    }
}
