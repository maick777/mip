<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoPagosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('tipo_pagos')->insert([
            'id'  => 1,
            'nombre' => "Efectivo",
            'icon' => "fa fa-money",
            'color' => "success"
        ]);

        DB::table('tipo_pagos')->insert([
            'id'  => 2,
            'nombre' => "Transferencia bancaria",
            'icon' => "fa fa-cc-visa",
            'color' => "primary"
        ]);

        DB::table('tipo_pagos')->insert([
            'id'  => 3,
            'nombre' => "Depósito",
            'icon' => "fa fa-credit-card",
            'color' => "danger"
        ]);
    }
}


