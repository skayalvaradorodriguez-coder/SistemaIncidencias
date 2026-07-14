<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        $rolResponsable = DB::table('roles')->where('nombre', 'Responsable')->first();
        $rolCiudadano = DB::table('roles')->where('nombre', 'Ciudadano')->first();

        $usuarios = [
            [
                'email' => 'responsable@incidencias.com',
                'name' => 'Carlos',
                'apellido' => 'Mendoza',
                'rol_id' => $rolResponsable->id,
                'password' => 'Responsable123!',
            ],
            [
                'email' => 'ciudadano@incidencias.com',
                'name' => 'Maria',
                'apellido' => 'Lopez',
                'rol_id' => $rolCiudadano->id,
                'password' => 'Ciudadano123!',
            ],
        ];

        foreach ($usuarios as $u) {
            DB::table('users')->updateOrInsert(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'apellido' => $u['apellido'],
                    'rol_id' => $u['rol_id'],
                    'password' => Hash::make($u['password']),
                    'activo' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}