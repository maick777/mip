<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoDocumentosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tipo_documentos')->insert([
            'id'  => 1,
            'nombre' => "Dni",
            'color'    => "success"
        ]);

        DB::table('tipo_documentos')->insert([
            'id'  => 2,
            'nombre' => "Pasaporte",
            'color'    => "primary"
        ]);
        DB::table('tipo_documentos')->insert([
            'id'  => 3,
            'nombre' => "Carnet Ext.",
            'color'    => "warning"
        ]);
        DB::table('tipo_documentos')->insert([
            'id'  => 4,
            'nombre' => "Ruc",
            'color'    => "danger"

        ]);
        DB::table('tipo_documentos')->insert([
            'id'  => 5,
            'nombre' => "Otro",
            'color'    => "info"
        ]);
    }
}
