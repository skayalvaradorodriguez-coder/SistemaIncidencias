<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IncidenciaController;
use App\Http\Controllers\UsuarioController;

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/', [DashboardController::class, 'index']);

Route::get('/tablero', [IncidenciaController::class, 'vistaTablero'])
    ->name('incidencias.tablero');

Route::get('/mis-reportes', [IncidenciaController::class, 'vistaMisReportes'])
    ->name('incidencias.mis');

Route::get('/reportes', [IncidenciaController::class, 'vistaReportes'])
    ->name('reportes');

Route::get('/emergencias', function () {
    return view('emergencias');
})->name('emergencias');

Route::get('/perfil', function () {
    return view('perfil');
})->name('perfil');

Route::get('/incidencias', [IncidenciaController::class, 'vistaIndex'])
    ->name('incidencias.index');

Route::get('/incidencias/crear', [IncidenciaController::class, 'vistaCreate'])
    ->name('incidencias.create');

Route::get('/incidencias/{id}', [IncidenciaController::class, 'vistaShow'])
    ->name('incidencias.show');

Route::get('/incidencias/{id}/editar', [IncidenciaController::class, 'vistaEdit'])
    ->name('incidencias.edit');

Route::get('/usuarios', function () {
    return view('usuarios.index');
})->name('usuarios.index');

Route::get('/usuarios/crear', function () {
    return view('usuarios.create');
})->name('usuarios.create');

Route::get('/usuarios/{id}', function () {
    return view('usuarios.show');
})->name('usuarios.show');

Route::get('/usuarios/{id}/editar', function () {
    return view('usuarios.edit');
})->name('usuarios.edit');