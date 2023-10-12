<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_tipo_documento')->nullable()->default(0);
            $table->string('nro_documento', 20)->nullable()->unique();
            $table->string('nombres_razon_social', 140);
            $table->string('foto', 255)->nullable();
            $table->string('correo', 70)->nullable()->unique();
            $table->string('celular', 11)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->unsignedInteger('id_pais')->default(0);
            $table->unsignedInteger('id_departamento')->default(0);
            $table->unsignedInteger('id_provincia')->default(0);
            $table->unsignedInteger('id_distrito')->default(0);
            $table->string('direccion', 150)->nullable();
            $table->text('detalle')->nullable();
            $table->unsignedInteger('id_estado')->nullable()->default(1);
            $table->unsignedInteger('listable')->nullable()->default(1);
            $table->unsignedInteger('id_user_create')->nullable();
            $table->unsignedInteger('id_user_update')->nullable();
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
        Schema::dropIfExists('clientes');
    }
}
