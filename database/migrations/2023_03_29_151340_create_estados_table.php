<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estados', function (Blueprint $table) {
            $table->increments('id');
            $table->string('estado_activacion', 30)->nullable();
            $table->string('estado_aprobacion', 30)->nullable();
            $table->string('estado_contrato', 30)->nullable();
            $table->string('estado_si_no', 30)->nullable();
            $table->string('estado_pago', 30)->nullable();
            $table->string('estado_condicion', 30)->nullable();
            $table->string('estado_prioridad', 30)->nullable();
            $table->string('estado_uso', 30)->nullable();
            $table->string('estado_vigencia', 30)->nullable();
            $table->unsignedInteger('grupo')->nullable()->default(1);
            $table->string('icon', 30)->nullable();
            $table->string('icon2', 30)->nullable();
            $table->string('color', 30)->nullable()->default('secondary');
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
        Schema::dropIfExists('estados');
    }
}
