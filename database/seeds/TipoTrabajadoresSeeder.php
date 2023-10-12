<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoTrabajadoresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tipo_trabajadors')->insert([
            'id'  => 1,
            'nombre' => "Pleno",
            'nombre_p' => "Plenos",
            'color' => 'success',
        ]);

        DB::table('tipo_trabajadors')->insert([
            'id'  => 2,
            'nombre' => "Aderente",
            'nombre_p' => "Aderentes",
            'color' => 'primary',
        ]);
        DB::table('tipo_trabajadors')->insert([
            'id'  => 3,
            'nombre' => "Invitado",
            'nombre_p' => "Invitados",
            'color' => 'warning',
        ]);
        DB::table('tipo_trabajadors')->insert([
            'id'  => 4,
            'nombre' => "Otro",
            'nombre_p' => "Otros",
            'color' => 'info'
        ]);
    }
}
