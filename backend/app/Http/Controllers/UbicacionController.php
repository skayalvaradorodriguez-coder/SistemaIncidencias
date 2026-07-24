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

        // ============================================================
        // Provincias y ciudades del Ecuador, agrupadas por región
        // ============================================================
        $provincias = [

            // ----- Región Costa -----
            'Esmeraldas' => ['Esmeraldas', 'Atacames', 'Quinindé', 'San Lorenzo'],
            'Manabí' => ['Manta', 'Portoviejo', 'Chone', 'Bahía de Caráquez'],
            'Santa Elena' => ['La Libertad', 'Santa Elena', 'Salinas'],
            'Guayas' => ['Guayaquil', 'Milagro', 'Daule', 'Durán'],
            'Los Ríos' => ['Babahoyo', 'Quevedo', 'Vinces'],
            'El Oro' => ['Machala', 'Pasaje', 'Santa Rosa'],
            'Santo Domingo de los Tsáchilas' => ['Santo Domingo'],

            // ----- Región Sierra -----
            'Carchi' => ['Tulcán', 'San Gabriel'],
            'Imbabura' => ['Ibarra', 'Otavalo', 'Cotacachi'],
            'Pichincha' => ['Quito', 'Cayambe', 'Sangolquí'],
            'Cotopaxi' => ['Latacunga', 'Saquisilí'],
            'Tungurahua' => ['Ambato', 'Baños'],
            'Bolívar' => ['Guaranda'],
            'Chimborazo' => ['Riobamba'],
            'Cañar' => ['Azogues', 'La Troncal'],
            'Azuay' => ['Cuenca', 'Gualaceo'],
            'Loja' => ['Loja', 'Catamayo'],

            // ----- Región Amazónica (Oriente) -----
            'Sucumbíos' => ['Nueva Loja (Lago Agrio)'],
            'Napo' => ['Tena'],
            'Orellana' => ['Puerto Francisco de Orellana (Coca)'],
            'Pastaza' => ['Puyo'],
            'Morona Santiago' => ['Macas'],
            'Zamora Chinchipe' => ['Zamora'],

            // ----- Región Insular -----
            'Galápagos' => ['Puerto Baquerizo Moreno', 'Puerto Ayora'],
        ];

        foreach ($provincias as $nombreProvincia => $ciudades) {

            DB::table('provincias')->updateOrInsert(
                ['pais_id' => $paisId, 'nombre' => $nombreProvincia],
                ['updated_at' => now(), 'created_at' => now()]
            );

            $provinciaId = DB::table('provincias')
                ->where('pais_id', $paisId)
                ->where('nombre', $nombreProvincia)
                ->value('id');

            foreach ($ciudades as $ciudad) {
                DB::table('ciudades')->updateOrInsert(
                    ['provincia_id' => $provinciaId, 'nombre' => $ciudad],
                    ['updated_at' => now(), 'created_at' => now()]
                );
            }
        }
    }
}