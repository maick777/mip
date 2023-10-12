<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PeriodoPagosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('periodo_pagos')->insert([
            'id'  => 1,
            'nombre' => "Anual",
            'color' => 'success'
        ]);

        DB::table('periodo_pagos')->insert([
            'id'  => 2,
            'nombre' => "Mensual",
            'color' => 'primary'
        ]);

        DB::table('periodo_pagos')->insert([
            'id'  => 3,
            'nombre' => "Quincenal",
            'color' => 'danger'
        ]);

    }
}
