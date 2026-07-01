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