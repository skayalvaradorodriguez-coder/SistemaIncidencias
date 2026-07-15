<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Restringe el acceso a rutas según el rol del usuario autenticado.
 * Uso: ->middleware('rol:Administrador') o ->middleware('rol:Administrador,Responsable')
 */
class VerificarRol
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user || !$user->rol || !in_array($user->rol->nombre, $roles)) {
            return response()->json([
                'message' => 'No tiene permisos para realizar esta acción.'
            ], 403);
        }

        return $next($request);
    }
}