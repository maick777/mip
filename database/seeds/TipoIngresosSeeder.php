<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoIngresosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tipo_ingresos')->insert([
            'id'  => 1,
            'nombre' => "Diezmo",
            'nombre_p' => "Diezmos",
            'color'    => "success"
        ]);

        DB::table('tipo_ingresos')->insert([
            'id'  => 2,
            'nombre' => "Ofrenda",
            'nombre_p' => "Ofrendas",
            'color'    => "primary"
        ]);

        DB::table('tipo_ingresos')->insert([
            'id'  => 3,
            'nombre' => "Donación",
            'nombre_p' => "Donaciones",
            'color'    => "warning"
        ]);
    }
}
