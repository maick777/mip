<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('estados')->insert([
            'id'  => 1,
            'nombre' => "Activo",
            'nombre2' => "Aprobado",
            'nombre3' => "Activo",
            'icon'    => "fa fa-toggle-on",
            'icon2'    => "fa fa-check-circle",
            'color' => "success"
        ]);

        DB::table('estados')->insert([
            'id'  => 2,
            'nombre' => "Inactivo",
            'nombre2' => "Rechazado",
            'nombre3' => "Inactivo",
            'icon'    => "fa fa-toggle-off",
            'icon2'    => "fa fa-close",
            'color'    => "danger"
        ]);

        DB::table('estados')->insert([
            'id'  => 3,
            'nombre' => "Retirado",
            'nombre2' => "Pendiente",
            'nombre3' => "Renunció",
            'icon'    => "fa fa-exclamation-circle",
            'icon2'    => "fa fa-clock-o",
            'color'    => "warning"
        ]);

        DB::table('estados')->insert([
            'id'  => 4,
            'nombre' => "Fallecido",
            'nombre2' => "Observado",
            'nombre3' => "Disciplinado",
            'icon'    => "fa fa-minus-circle",
            'icon2'    => "fa fa-warning",
            'color'    => "dark"
        ]);
    }
}
