<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SeguridadRolesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->crearCatalogos();
    }

    public function test_ciudadano_no_puede_listar_usuarios(): void
    {
        Sanctum::actingAs($this->crearCiudadano());

        $this->getJson('/api/usuarios')->assertStatus(403);
    }

    public function test_administrador_si_puede_listar_usuarios(): void
    {
        Sanctum::actingAs($this->crearAdmin());

        $this->getJson('/api/usuarios')->assertStatus(200);
    }

    public function test_ciudadano_no_puede_eliminar_incidencias(): void
    {
        $ciudadano = $this->crearCiudadano();
        $incidencia = $this->crearIncidencia($ciudadano);

        Sanctum::actingAs($ciudadano);

        $this->deleteJson("/api/incidencias/{$incidencia->id}")->assertStatus(403);

        $this->assertDatabaseHas('incidencias', ['id' => $incidencia->id]);
    }

    public function test_ciudadano_no_puede_cambiar_estados(): void
    {
        $ciudadano = $this->crearCiudadano();
        $incidencia = $this->crearIncidencia($ciudadano);

        Sanctum::actingAs($ciudadano);

        $response = $this->postJson("/api/incidencias/{$incidencia->id}/estado", [
            'estado_incidencia_id' => 2,
        ]);

        $response->assertStatus(403);
    }

    public function test_responsable_si_puede_cambiar_estados(): void
    {
        $ciudadano = $this->crearCiudadano();
        $incidencia = $this->crearIncidencia($ciudadano);

        Sanctum::actingAs($this->crearResponsable());

        $response = $this->postJson("/api/incidencias/{$incidencia->id}/estado", [
            'estado_incidencia_id' => 2,
            'observacion' => 'Iniciando atención',
        ]);

        $response->assertStatus(200);
    }

    public function test_administrador_no_puede_desactivar_su_propia_cuenta(): void
    {
        $admin = $this->crearAdmin();

        Sanctum::actingAs($admin);

        $this->deleteJson("/api/usuarios/{$admin->id}")->assertStatus(422);

        $this->assertDatabaseHas('users', ['id' => $admin->id, 'activo' => true]);
    }
}