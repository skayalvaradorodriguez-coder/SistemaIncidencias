<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadosIncidenciaSeeder extends Seeder
{
    public function run(): void
    {
        $estados = [
            ['nombre' => 'Pendiente', 'color' => 'warning', 'descripcion' => 'Incidencia registrada, pendiente de atención'],
            ['nombre' => 'En Proceso', 'color' => 'info', 'descripcion' => 'Incidencia siendo atendida'],
            ['nombre' => 'Resuelto', 'color' => 'success', 'descripcion' => 'Incidencia resuelta satisfactoriamente'],
            ['nombre' => 'Rechazado', 'color' => 'danger', 'descripcion' => 'Incidencia rechazada por no cumplir criterios'],
        ];

        foreach ($estados as $estado) {
            DB::table('estados_incidencia')->updateOrInsert(
                ['nombre' => $estado['nombre']],
                ['color' => $estado['color'], 'descripcion' => $estado['descripcion'], 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }
}