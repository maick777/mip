<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSlidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sliders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('titulo', 30);
            $table->string('texto', 150);
            $table->string('libro', 70);
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
        Schema::dropIfExists('sliders');
    }
}
