<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UbicacionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('paises')->updateOrInsert(
            ['codigo' => 'EC'],
            ['nombre' => 'Ecuador', 'updated_at' => now(), 'created_at' => now()]
        );
        $paisId = DB::table('paises')->where('codigo', 'EC')->value('id');

        DB::table('provincias')->updateOrInsert(
            ['pais_id' => $paisId, 'nombre' => 'Santa Elena'],
            ['updated_at' => now(), 'created_at' => now()]
        );
        $provinciaId = DB::table('provincias')
            ->where('pais_id', $paisId)
            ->where('nombre', 'Santa Elena')
            ->value('id');

        $ciudades = ['La Libertad', 'Santa Elena', 'Salinas'];

        foreach ($ciudades as $ciudad) {
            DB::table('ciudades')->updateOrInsert(
                ['provincia_id' => $provinciaId, 'nombre' => $ciudad],
                ['updated_at' => now(), 'created_at' => now()]
            );
        }
    }
}