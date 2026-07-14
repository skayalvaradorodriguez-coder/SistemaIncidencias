<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['nombre' => 'Administrador', 'descripcion' => 'Acceso total al sistema'],
            ['nombre' => 'Responsable', 'descripcion' => 'Gestiona y atiende incidencias asignadas'],
            ['nombre' => 'Ciudadano', 'descripcion' => 'Registra y consulta incidencias'],
        ];

        foreach ($roles as $rol) {
            DB::table('roles')->updateOrInsert(
                ['nombre' => $rol['nombre']],
                ['descripcion' => $rol['descripcion'], 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }
}