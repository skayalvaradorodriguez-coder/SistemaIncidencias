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

// Rutas públicas
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Rutas protegidas
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Usuarios
    Route::get('/usuarios', [UsuarioController::class, 'index']);
    Route::get('/usuarios/{id}', [UsuarioController::class, 'show']);
    Route::put('/usuarios/{id}', [UsuarioController::class, 'update']);
    Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy']);

    // Incidencias
    Route::get('/incidencias', [IncidenciaController::class, 'index']);
    Route::post('/incidencias', [IncidenciaController::class, 'store']);
    Route::get('/incidencias/{id}', [IncidenciaController::class, 'show']);
    Route::put('/incidencias/{id}', [IncidenciaController::class, 'update']);
    Route::delete('/incidencias/{id}', [IncidenciaController::class, 'destroy']);
    Route::post('/incidencias/{id}/estado', [IncidenciaController::class, 'cambiarEstado']);

    // Comentarios
    Route::get('/incidencias/{id}/comentarios', [ComentarioController::class, 'index']);
    Route::post('/incidencias/{id}/comentarios', [ComentarioController::class, 'store']);
    Route::delete('/comentarios/{id}', [ComentarioController::class, 'destroy']);

    // Notificaciones
    Route::get('/notificaciones', [NotificacionController::class, 'index']);
    Route::put('/notificaciones/{id}/leer', [NotificacionController::class, 'marcarLeida']);
    Route::put('/notificaciones/leer-todas', [NotificacionController::class, 'marcarTodasLeidas']);

    // Tipos e incidencia
    Route::get('/tipos', [TipoController::class, 'index']);
    Route::post('/tipos', [TipoController::class, 'store']);
    Route::put('/tipos/{id}', [TipoController::class, 'update']);
    Route::delete('/tipos/{id}', [TipoController::class, 'destroy']);
    Route::post('/tipos/{id}/subtipos', [TipoController::class, 'storeSubtipo']);

    // Estados
    Route::get('/estados', [EstadoController::class, 'index']);
    Route::post('/estados', [EstadoController::class, 'store']);
    Route::put('/estados/{id}', [EstadoController::class, 'update']);

    // Ubicación
    Route::get('/paises', [UbicacionController::class, 'paises']);
    Route::get('/paises/{id}/provincias', [UbicacionController::class, 'provincias']);
    Route::get('/provincias/{id}/ciudades', [UbicacionController::class, 'ciudades']);
});