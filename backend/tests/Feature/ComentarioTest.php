<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ComentarioTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->crearCatalogos();
    }

    public function test_agregar_comentario_registra_autor_y_notifica_al_reportante(): void
    {
        $ciudadano = $this->crearCiudadano();
        $admin = $this->crearAdmin();
        $incidencia = $this->crearIncidencia($ciudadano);

        Sanctum::actingAs($admin);

        $response = $this->postJson("/api/incidencias/{$incidencia->id}/comentarios", [
            'comentario' => 'Estamos revisando el caso',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('usuario.id', $admin->id);

        $this->assertDatabaseHas('comentarios', [
            'incidencia_id' => $incidencia->id,
            'usuario_id' => $admin->id,
            'comentario' => 'Estamos revisando el caso',
        ]);

        $this->assertDatabaseHas('notificaciones', [
            'usuario_id' => $ciudadano->id,
            'incidencia_id' => $incidencia->id,
        ]);
    }

    public function test_comentario_vacio_es_rechazado(): void
    {
        $ciudadano = $this->crearCiudadano();
        $incidencia = $this->crearIncidencia($ciudadano);

        Sanctum::actingAs($ciudadano);

        $response = $this->postJson("/api/incidencias/{$incidencia->id}/comentarios", [
            'comentario' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['comentario']);
    }

    public function test_comentar_incidencia_inexistente_devuelve_404(): void
    {
        Sanctum::actingAs($this->crearCiudadano());

        $this->postJson('/api/incidencias/9999/comentarios', [
            'comentario' => 'Hola',
        ])->assertStatus(404);
    }
}