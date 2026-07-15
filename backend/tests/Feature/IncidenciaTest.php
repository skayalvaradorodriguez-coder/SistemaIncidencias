<?php

namespace Tests\Feature;

use App\Models\EstadoIncidencia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class IncidenciaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->crearCatalogos();
    }

    public function test_crear_incidencia_genera_historial_y_notifica_a_gestores(): void
    {
        $admin = $this->crearAdmin();
        $ciudadano = $this->crearCiudadano();

        Sanctum::actingAs($ciudadano);

        $response = $this->postJson('/api/incidencias', [
            'titulo' => 'Poste de alumbrado dañado',
            'descripcion' => 'Poste sin funcionar hace una semana',
            'ciudad_id' => 1,
            'tipo_incidencia_id' => 1,
            'subtipo_incidencia_id' => 1,
            'prioridad' => 'Alta',
            'latitud' => -2.2276,
            'longitud' => -80.8585,
            'direccion' => 'Av. Principal',
        ]);

        $response->assertStatus(201);

        $incidenciaId = $response->json('id');

        // La incidencia nace en estado Pendiente con historial
        $this->assertDatabaseHas('historial_estados', [
            'incidencia_id' => $incidenciaId,
            'usuario_id' => $ciudadano->id,
        ]);

        // El administrador recibe notificación; el creador no
        $this->assertDatabaseHas('notificaciones', [
            'usuario_id' => $admin->id,
            'incidencia_id' => $incidenciaId,
        ]);

        $this->assertDatabaseMissing('notificaciones', [
            'usuario_id' => $ciudadano->id,
            'incidencia_id' => $incidenciaId,
        ]);
    }

    public function test_crear_incidencia_sin_titulo_devuelve_422(): void
    {
        Sanctum::actingAs($this->crearCiudadano());

        $response = $this->postJson('/api/incidencias', [
            'descripcion' => 'Sin título',
            'ciudad_id' => 1,
            'tipo_incidencia_id' => 1,
            'subtipo_incidencia_id' => 1,
            'prioridad' => 'Alta',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['titulo']);
    }

    public function test_coordenadas_invalidas_son_rechazadas(): void
    {
        Sanctum::actingAs($this->crearCiudadano());

        $response = $this->postJson('/api/incidencias', [
            'titulo' => 'Prueba coordenadas',
            'descripcion' => 'Latitud fuera de rango',
            'ciudad_id' => 1,
            'tipo_incidencia_id' => 1,
            'subtipo_incidencia_id' => 1,
            'prioridad' => 'Media',
            'latitud' => 999,
            'longitud' => -80.8585,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['latitud']);
    }

    public function test_cambio_de_estado_registra_historial_y_notifica_al_reportante(): void
    {
        $ciudadano = $this->crearCiudadano();
        $responsable = $this->crearResponsable();
        $incidencia = $this->crearIncidencia($ciudadano);

        $enProceso = EstadoIncidencia::where('nombre', 'En Proceso')->first();

        Sanctum::actingAs($responsable);

        $response = $this->postJson("/api/incidencias/{$incidencia->id}/estado", [
            'estado_incidencia_id' => $enProceso->id,
            'observacion' => 'Cuadrilla en camino',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('incidencias', [
            'id' => $incidencia->id,
            'estado_incidencia_id' => $enProceso->id,
        ]);

        $this->assertDatabaseHas('historial_estados', [
            'incidencia_id' => $incidencia->id,
            'estado_nuevo_id' => $enProceso->id,
            'observacion' => 'Cuadrilla en camino',
        ]);

        $this->assertDatabaseHas('notificaciones', [
            'usuario_id' => $ciudadano->id,
            'incidencia_id' => $incidencia->id,
        ]);
    }

    public function test_cambiar_al_mismo_estado_es_rechazado(): void
    {
        $ciudadano = $this->crearCiudadano();
        $incidencia = $this->crearIncidencia($ciudadano);

        $pendiente = EstadoIncidencia::where('nombre', 'Pendiente')->first();

        Sanctum::actingAs($this->crearAdmin());

        $response = $this->postJson("/api/incidencias/{$incidencia->id}/estado", [
            'estado_incidencia_id' => $pendiente->id,
        ]);

        $response->assertStatus(422);
    }

    public function test_listar_incidencias_con_filtro_por_estado(): void
    {
        $ciudadano = $this->crearCiudadano();
        $this->crearIncidencia($ciudadano);

        $pendiente = EstadoIncidencia::where('nombre', 'Pendiente')->first();

        Sanctum::actingAs($ciudadano);

        $this->getJson('/api/incidencias?estado_id=' . $pendiente->id)
            ->assertStatus(200)
            ->assertJsonCount(1);

        $enProceso = EstadoIncidencia::where('nombre', 'En Proceso')->first();

        $this->getJson('/api/incidencias?estado_id=' . $enProceso->id)
            ->assertStatus(200)
            ->assertJsonCount(0);
    }
}