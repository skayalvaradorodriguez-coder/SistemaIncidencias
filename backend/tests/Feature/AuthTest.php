<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->crearCatalogos();
    }

    public function test_login_con_credenciales_correctas_devuelve_token(): void
    {
        $this->crearCiudadano([
            'email' => 'usuario@test.com',
            'password' => Hash::make('Clave1234'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'usuario@test.com',
            'password' => 'Clave1234',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email', 'rol']]);
    }

    public function test_login_con_credenciales_incorrectas_devuelve_401(): void
    {
        $this->crearCiudadano(['email' => 'usuario@test.com']);

        $response = $this->postJson('/api/login', [
            'email' => 'usuario@test.com',
            'password' => 'ClaveIncorrecta1',
        ]);

        $response->assertStatus(401);
    }

    public function test_usuario_inactivo_no_puede_iniciar_sesion(): void
    {
        $this->crearCiudadano([
            'email' => 'inactivo@test.com',
            'password' => Hash::make('Clave1234'),
            'activo' => false,
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'inactivo@test.com',
            'password' => 'Clave1234',
        ]);

        $response->assertStatus(403);
    }

    public function test_registro_crea_cuenta_con_rol_ciudadano(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Nuevo',
            'apellido' => 'Usuario',
            'email' => 'nuevo@test.com',
            'password' => 'ClaveSegura1',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('user.rol.nombre', 'Ciudadano');

        $this->assertDatabaseHas('users', ['email' => 'nuevo@test.com']);
    }

    public function test_registro_rechaza_contrasena_debil(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Nuevo',
            'apellido' => 'Usuario',
            'email' => 'debil@test.com',
            'password' => '123456',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);

        $this->assertDatabaseMissing('users', ['email' => 'debil@test.com']);
    }

    public function test_throttle_bloquea_tras_cinco_intentos_fallidos(): void
    {
        $this->crearCiudadano(['email' => 'victima@test.com']);

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/login', [
                'email' => 'victima@test.com',
                'password' => 'Incorrecta' . $i,
            ])->assertStatus(401);
        }

        $this->postJson('/api/login', [
            'email' => 'victima@test.com',
            'password' => 'Incorrecta6',
        ])->assertStatus(429);
    }

    public function test_ruta_protegida_sin_token_devuelve_401(): void
    {
        $this->getJson('/api/incidencias')->assertStatus(401);
    }
}