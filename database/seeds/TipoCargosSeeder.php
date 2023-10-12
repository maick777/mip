<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoCargosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tipo_cargos')->insert([
            'id'  => 1,
            'nombre' => "Pastor",
            'nombre_p' => "Pastores",
            'nombre_g' => "Pastorado",
            'color'    => "success"
        ]);

        DB::table('tipo_cargos')->insert([
            'id'  => 2,
            'nombre' => "Diácono",
            'nombre_p' => "Diáconos",
            'nombre_g' => "Diaconado",
            'color'    => "primary "
        ]);

        DB::table('tipo_cargos')->insert([
            'id'  => 3,
            'nombre' => "Intercesor",
            'nombre_p' => "Intercesores",
            'nombre_g' => "Intercessión",
            'color' => "danger",
        ]);

        DB::table('tipo_cargos')->insert([
            'id'  => 4,
            'nombre' => "Ministerio",
            'nombre_p' => "Ministerios",
            'nombre_g' => "Ministerial",
            'color'    => "warning"
        ]);

        DB::table('tipo_cargos')->insert([
            'id'  => 5,
            'nombre' => "Miembro",
            'nombre_p' => "Miembros",
            'nombre_g' => "Miembro",
            'color'    => "secondary"
        ]);
    }
}
