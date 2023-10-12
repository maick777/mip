<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('referencia', 70)->nullable();
            $table->unsignedInteger('id_trabajador')->nullable();
            $table->unsignedInteger('id_contrato')->nullable();
            $table->unsignedInteger('id_moneda')->nullable();
            $table->double('monto')->nullable();
            $table->date('fecha')->nullable();
            $table->string('archivo', 255)->nullable();
            $table->unsignedInteger('id_estado')->nullable()->default(1);
            $table->date('fecha_pago')->nullable();
            $table->text('observacion')->nullable();
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
        Schema::dropIfExists('pagos');
    }
}
