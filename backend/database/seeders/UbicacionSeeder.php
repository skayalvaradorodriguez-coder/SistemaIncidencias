<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UbicacionSeeder extends Seeder
{
    public function run(): void
    {
        $paisId = DB::table('paises')->insertGetId([
            'nombre' => 'Ecuador',
            'codigo' => 'EC',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $provinciaId = DB::table('provincias')->insertGetId([
            'pais_id' => $paisId,
            'nombre' => 'Santa Elena',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('ciudades')->insert([
            ['provincia_id' => $provinciaId, 'nombre' => 'La Libertad', 'created_at' => now(), 'updated_at' => now()],
            ['provincia_id' => $provinciaId, 'nombre' => 'Santa Elena', 'created_at' => now(), 'updated_at' => now()],
            ['provincia_id' => $provinciaId, 'nombre' => 'Salinas', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}