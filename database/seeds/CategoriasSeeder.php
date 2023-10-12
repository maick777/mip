<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categorias')->insert([
            'id'  => 1,
            'nombre' => "Niño",
            'nombre_p' => "Niños",
            'color'    => "success"
        ]);

        DB::table('categorias')->insert([
            'id'  => 2,
            'nombre' => "Joven",
            'nombre_p' => "Jóvenes",
            'color'    => "primary"
        ]);
        DB::table('categorias')->insert([
            'id'  => 3,
            'nombre' => "Adulto",
            'nombre_p' => "Adultos",
            'color'    => "warning"
        ]);
        DB::table('categorias')->insert([
            'id'  => 4,
            'nombre' => "Anciano",
            'nombre_p' => "Ancianos",
            'color'    => "danger"
        ]);
    }
}
