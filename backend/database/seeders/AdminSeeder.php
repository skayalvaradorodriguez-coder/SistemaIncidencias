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

        DB::table('users')->insert([
            'rol_id' => $rolAdmin->id,
            'name' => 'Administrador',
            'apellido' => 'Sistema',
            'email' => 'admin@incidencias.com',
            'password' => Hash::make('Admin123!'),
            'activo' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}