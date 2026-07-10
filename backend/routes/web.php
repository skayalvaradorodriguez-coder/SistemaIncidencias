<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IncidenciaController;

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/', [DashboardController::class, 'index']);

Route::get('/incidencias', [IncidenciaController::class, 'vistaIndex'])
    ->name('incidencias.index');

Route::get('/incidencias/crear', [IncidenciaController::class, 'vistaCreate'])
    ->name('incidencias.create');

Route::get('/incidencias/{id}', [IncidenciaController::class, 'vistaShow'])
    ->name('incidencias.show');

Route::get('/incidencias/{id}/editar', [IncidenciaController::class, 'vistaEdit'])
    ->name('incidencias.edit');

    use App\Http\Controllers\UsuarioController;

Route::get('/usuarios', [UsuarioController::class, 'vistaIndex'])
    ->name('usuarios.index');

Route::get('/usuarios/create', [UsuarioController::class, 'create'])
    ->name('usuarios.create');