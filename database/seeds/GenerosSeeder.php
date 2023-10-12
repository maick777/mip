<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GenerosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('generos')->insert([
            'id'  => 1,
            'nombre' => "Masculino",
            'icon' => "fa fa-male",
            'color'    => "success"
        ]);

        DB::table('generos')->insert([
            'id'  => 2,
            'nombre' => "Femenino",
            'icon' => "fa fa-female",
            'color' => "primary"
        ]);
    }
}
