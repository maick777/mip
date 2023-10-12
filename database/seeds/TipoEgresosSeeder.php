<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoEgresosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tipo_egresos')->insert([
            'id'  => 1,
            'nombre' => "Pago",
            'nombre_p' => "Pagos",
            'icon' => "fa fa-money",
            'color' => 'primary'
        ]);

        DB::table('tipo_egresos')->insert([
            'id'  => 2,
            'nombre' => "Compra",
            'nombre_p' => "Compras",
            'icon' => "fa fa-shopping-cart",
            'color' => 'success'
        ]);

        DB::table('tipo_egresos')->insert([
            'id'  => 3,
            'nombre' => "Venta",
            'nombre_p' => "Ventas",
            'icon' => "fa fa-money",
            'color' => 'warning'
        ]);

        DB::table('tipo_egresos')->insert([
            'id'  => 4,
            'nombre' => "Otro",
            'nombre_p' => "Otros",
            'icon' => "fa fa-money",
            'color' => 'danger'
        ]);
    }
}
