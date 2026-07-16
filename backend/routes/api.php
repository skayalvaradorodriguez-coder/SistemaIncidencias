<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\IncidenciaController;
use App\Http\Controllers\ComentarioController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\TipoController;
use App\Http\Controllers\UbicacionController;
use App\Http\Controllers\EstadoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AsignacionController;

// Rutas públicas (limitadas a 5 intentos por minuto contra fuerza bruta)
Route::middleware('throttle:5,1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

// Rutas protegidas (requieren token válido)
Route::middleware('auth:sanctum')->group(function () {

    // Auth y perfil
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/perfil', [AuthController::class, 'actualizarPerfil']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'api']);

    // Incidencias (lectura y registro: cualquier usuario autenticado)
    Route::get('/incidencias', [IncidenciaController::class, 'index']);
    Route::post('/incidencias', [IncidenciaController::class, 'store']);
    Route::get('/incidencias/{id}', [IncidenciaController::class, 'show']);
    Route::put('/incidencias/{id}', [IncidenciaController::class, 'update']);

    // Asignaciones (consulta abierta a autenticados)
    Route::get('/incidencias/{id}/asignaciones', [AsignacionController::class, 'index']);

    // Comentarios
    Route::get('/incidencias/{id}/comentarios', [ComentarioController::class, 'index']);
    Route::post('/incidencias/{id}/comentarios', [ComentarioController::class, 'store']);

    // Notificaciones (siempre del usuario autenticado)
    Route::get('/notificaciones', [NotificacionController::class, 'index']);
    Route::put('/notificaciones/{id}/leer', [NotificacionController::class, 'marcarLeida']);
    Route::put('/notificaciones/leer-todas', [NotificacionController::class, 'marcarTodasLeidas']);

    // Catálogos (lectura abierta a autenticados)
    Route::get('/tipos', [TipoController::class, 'index']);
    Route::get('/estados', [EstadoController::class, 'index']);
    Route::get('/paises', [UbicacionController::class, 'paises']);
    Route::get('/paises/{id}/provincias', [UbicacionController::class, 'provincias']);
    Route::get('/provincias/{id}/ciudades', [UbicacionController::class, 'ciudades']);

    // ===== Gestión operativa: Administrador o Responsable =====
    Route::middleware('rol:Administrador,Responsable')->group(function () {

        Route::post('/incidencias/{id}/estado', [IncidenciaController::class, 'cambiarEstado']);

        Route::post('/incidencias/{id}/asignaciones', [AsignacionController::class, 'store']);
        Route::put('/asignaciones/{id}', [AsignacionController::class, 'update']);
        Route::delete('/asignaciones/{id}', [AsignacionController::class, 'destroy']);

        Route::delete('/comentarios/{id}', [ComentarioController::class, 'destroy']);
    });

    // ===== Administración del sistema: solo Administrador =====
    Route::middleware('rol:Administrador')->group(function () {

        Route::get('/usuarios', [UsuarioController::class, 'index']);
        Route::post('/usuarios', [UsuarioController::class, 'store']);
        Route::get('/usuarios/{id}', [UsuarioController::class, 'show']);
        Route::put('/usuarios/{id}', [UsuarioController::class, 'update']);
        Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy']);

        Route::delete('/incidencias/{id}', [IncidenciaController::class, 'destroy']);

        Route::post('/tipos', [TipoController::class, 'store']);
        Route::put('/tipos/{id}', [TipoController::class, 'update']);
        Route::delete('/tipos/{id}', [TipoController::class, 'destroy']);
        Route::post('/tipos/{id}/subtipos', [TipoController::class, 'storeSubtipo']);

        Route::post('/estados', [EstadoController::class, 'store']);
        Route::put('/estados/{id}', [EstadoController::class, 'update']);
    });
});