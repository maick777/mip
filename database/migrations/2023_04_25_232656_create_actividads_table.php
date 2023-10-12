<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActividadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actividads', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_delegado')->nullable()->default(0);
            $table->unsignedInteger('id_tipo_actividad')->nullable()->default(0);
            $table->unsignedInteger('id_grupo')->nullable()->default(0);
            $table->string('titulo', 70)->nullable();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->datetime('fecha_hora_inicio')->nullable();
            $table->datetime('fecha_hora_fin')->nullable();
            $table->text('descripcion')->nullable();
            $table->unsignedInteger('id_mina')->nullable();
            $table->unsignedInteger('id_yacimiento')->nullable();
            $table->unsignedInteger('id_estado')->nullable()->default(1);
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
        Schema::dropIfExists('actividads');
    }
}
