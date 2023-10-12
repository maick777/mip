<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIngresosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ingresos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('referencia', 70)->nullable();
            $table->unsignedInteger('id_responsable')->nullable()->default(0);
            $table->unsignedInteger('id_tipo_ingreso')->default(0);
            $table->unsignedInteger('id_moneda')->default(1);
            $table->double('monto')->nullable();
            $table->unsignedInteger('id_tipo_pago')->nullable()->default(0);
            $table->date('fecha_pago')->nullable();
            $table->text('observacion')->nullable();
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
        Schema::dropIfExists('ingresos');
    }
}
