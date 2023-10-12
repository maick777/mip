<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContratosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contratos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_trabajador')->nullable();
            $table->unsignedInteger('id_periodo_pago')->nullable();
            $table->unsignedInteger('id_cargo')->nullable();
            $table->unsignedInteger('id_tipo_cargo')->nullable();
            $table->unsignedInteger('id_moneda')->nullable();
            $table->unsignedInteger('id_referencia')->nullable()->default(0);
            $table->string('referencia', 70)->nullable();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
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
        Schema::dropIfExists('contratos');
    }
}
