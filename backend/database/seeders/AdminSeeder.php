<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $rolAdmin = DB::table('roles')->where('nombre', 'Administrador')->first();

        DB::table('users')->updateOrInsert(
            ['email' => 'admin@incidencias.com'],
            [
                'rol_id' => $rolAdmin->id,
                'name' => 'Administrador',
                'apellido' => 'Sistema',
                'password' => Hash::make('Admin123!'),
                'activo' => true,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }
}