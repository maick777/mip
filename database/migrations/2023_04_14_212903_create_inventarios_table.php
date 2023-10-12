<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventarios', function (Blueprint $table) {

            $table->increments('id');
            $table->string('nombre', 70)->nullable();
            $table->string('descripcion', 70)->nullable();
            $table->unsignedInteger('cantidad')->nullable(0);
            $table->double('valor_adquisicion')->nullable();
            $table->double('valor_total')->nullable();
            $table->date('fecha_adquisicion')->nullable();
            $table->text('observacion')->nullable();
            $table->string('foto', 255)->nullable();
            $table->unsignedInteger('id_responsable')->nullable()->default(0);
            $table->unsignedInteger('uso')->nullable()->default(0);
            $table->unsignedInteger('id_mina')->nullable();
            $table->unsignedInteger('id_yacimiento')->nullable();
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
        Schema::dropIfExists('inventarios');
    }
}
