<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            ['nombre' => 'Administrador', 'descripcion' => 'Acceso total al sistema', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Responsable', 'descripcion' => 'Gestiona y atiende incidencias asignadas', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Ciudadano', 'descripcion' => 'Registra y consulta incidencias', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}