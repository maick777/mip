<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEgresosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('egresos', function (Blueprint $table) {
            $table->increments('id');
            
            $table->unsignedInteger('id_tipo_egreso')->nullable(); // Compra / Venta / Pago Otro ::Tabla por crear
            $table->string('nombre', 70); // Pago por : Servicio de agua
            $table->unsignedInteger('id_trabajador')->nullable();
            $table->unsignedInteger('cantidad')->nullable();
            $table->unsignedInteger('id_moneda')->default(1);
            $table->double('precio_unidad')->nullable();
            $table->double('precio_total')->nullable();
            $table->unsignedInteger('id_tipo_ingreso')->nullable(0); //Pagado con: Donación / Otro
            $table->date('fecha_egreso')->nullable();
            $table->string('comprobante', 255)->nullable();
            $table->string('correo', 70)->unique()->nullable();
            $table->string('celular', 11)->nullable();
            $table->text('observacion')->nullable();
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
        Schema::dropIfExists('egresos');
    }
}
