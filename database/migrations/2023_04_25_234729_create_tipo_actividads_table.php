<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTipoActividadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_actividads', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 70);
            $table->unsignedInteger('grupo')->nullable()->default(1);
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
        Schema::dropIfExists('tipo_actividads');
    }
}
