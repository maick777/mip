<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEjecutivosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ejecutivos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_yacimiento')->default(0);
            $table->unsignedInteger('id_cms_users')->default(0);
            $table->string('nombres', 70)->nullable();
            $table->string('apellidos', 70)->nullable();
            $table->string('abreviacion', 70)->nullable();
            $table->string('codigo', 20)->nullable();
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
        Schema::dropIfExists('ejecutivos');
    }
}
