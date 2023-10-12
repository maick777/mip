<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoCivilSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('estado_civils')->insert([
            'id'  => 1,
            'nombre' => "Soltero",
            'color' => "success"
        ]);

        DB::table('estado_civils')->insert([
            'id'  => 2,
            'nombre' => "Casado",
            'color' => "primary"
        ]);

        DB::table('estado_civils')->insert([
            'id'  => 3,
            'nombre' => "Viudo",
            'color' => "dark"
        ]);
    }
}
