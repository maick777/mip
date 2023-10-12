<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoMonedasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tipo_monedas')->insert([
            'id'  => 1,
            'simbolo' => "S/.",
            'nombre' => "Nuevo Sol",
        ]);

        DB::table('tipo_monedas')->insert([
            'id'  => 2,
            'simbolo' => "US$",
            'nombre' => "Dólar",
        ]);
    }
}
