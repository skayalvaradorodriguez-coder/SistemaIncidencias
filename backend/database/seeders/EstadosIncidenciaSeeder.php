<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadosIncidenciaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('estados_incidencia')->insert([
            ['nombre' => 'Pendiente', 'color' => 'warning', 'descripcion' => 'Incidencia registrada, pendiente de atención', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'En Proceso', 'color' => 'info', 'descripcion' => 'Incidencia siendo atendida', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Resuelto', 'color' => 'success', 'descripcion' => 'Incidencia resuelta satisfactoriamente', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Rechazado', 'color' => 'danger', 'descripcion' => 'Incidencia rechazada por no cumplir criterios', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}