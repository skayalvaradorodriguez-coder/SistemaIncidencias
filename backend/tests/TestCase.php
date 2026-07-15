<?php

namespace Tests;

use App\Models\Ciudad;
use App\Models\EstadoIncidencia;
use App\Models\Incidencia;
use App\Models\Pais;
use App\Models\Provincia;
use App\Models\Rol;
use App\Models\SubtipoIncidencia;
use App\Models\TipoIncidencia;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Crea los catálogos mínimos que el sistema necesita para operar.
     * El orden de los roles importa: el registro público asigna rol_id = 3 (Ciudadano).
     */
    protected function crearCatalogos(): void
    {
        Rol::create(['nombre' => 'Administrador', 'descripcion' => 'Acceso total al sistema']);
        Rol::create(['nombre' => 'Responsable', 'descripcion' => 'Gestiona incidencias asignadas']);
        Rol::create(['nombre' => 'Ciudadano', 'descripcion' => 'Reporta incidencias']);

        EstadoIncidencia::create(['nombre' => 'Pendiente', 'color' => 'warning', 'descripcion' => 'Registrada, sin atender']);
        EstadoIncidencia::create(['nombre' => 'En Proceso', 'color' => 'primary', 'descripcion' => 'En atención']);
        EstadoIncidencia::create(['nombre' => 'Resuelto', 'color' => 'success', 'descripcion' => 'Atendida y cerrada']);

        $pais = Pais::create(['nombre' => 'Ecuador', 'codigo' => 'EC']);
        $provincia = Provincia::create(['pais_id' => $pais->id, 'nombre' => 'Santa Elena']);
        Ciudad::create(['provincia_id' => $provincia->id, 'nombre' => 'La Libertad']);

        $tipo = TipoIncidencia::create(['nombre' => 'Infraestructura', 'descripcion' => 'Daños en infraestructura pública']);
        SubtipoIncidencia::create(['tipo_incidencia_id' => $tipo->id, 'nombre' => 'Alumbrado', 'descripcion' => 'Alumbrado público']);
    }

    protected function crearUsuario(string $rolNombre, array $atributos = []): User
    {
        $rol = Rol::where('nombre', $rolNombre)->firstOrFail();

        return User::factory()->create(array_merge([
            'rol_id' => $rol->id,
            'activo' => true,
        ], $atributos));
    }

    protected function crearAdmin(array $atributos = []): User
    {
        return $this->crearUsuario('Administrador', $atributos);
    }

    protected function crearResponsable(array $atributos = []): User
    {
        return $this->crearUsuario('Responsable', $atributos);
    }

    protected function crearCiudadano(array $atributos = []): User
    {
        return $this->crearUsuario('Ciudadano', $atributos);
    }

    protected function crearIncidencia(User $reportadoPor): Incidencia
    {
        return Incidencia::create([
            'usuario_id' => $reportadoPor->id,
            'ciudad_id' => Ciudad::first()->id,
            'tipo_incidencia_id' => TipoIncidencia::first()->id,
            'subtipo_incidencia_id' => SubtipoIncidencia::first()->id,
            'estado_incidencia_id' => EstadoIncidencia::where('nombre', 'Pendiente')->first()->id,
            'titulo' => 'Incidencia de prueba',
            'descripcion' => 'Descripción de prueba',
            'prioridad' => 'Alta',
            'latitud' => -2.2276,
            'longitud' => -80.8585,
            'direccion' => 'Av. Principal, La Libertad',
        ]);
    }
}