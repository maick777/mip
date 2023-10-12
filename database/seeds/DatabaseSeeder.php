<?php

use Database\Seeders\CategoriasSeeder;
use Database\Seeders\EstadoCivilSeeder;
use Database\Seeders\EstadosSeeder;
use Database\Seeders\GenerosSeeder;
use Database\Seeders\TipoCargosSeeder;
use Database\Seeders\TipoDocumentosSeeder;
use Database\Seeders\TipoEgresosSeeder;
use Database\Seeders\TipoIngresosSeeder;
use Database\Seeders\TipoMonedasSeeder;
use Database\Seeders\TipoPagosSeeder;
use Database\Seeders\TipoTrabajadoresSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);

        $this->call([

            TipoDocumentosSeeder::class,
            GenerosSeeder::class,
            EstadoCivilSeeder::class,
            TipoTrabajadoresSeeder::class,
            EstadosSeeder::class,
            CategoriasSeeder::class,
            TipoCargosSeeder::class,
            TipoIngresosSeeder::class,
            TipoMonedasSeeder::class,
            TipoPagosSeeder::class,
            TipoEgresosSeeder::class
        ]);
    }
}
