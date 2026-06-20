<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Incidencia;
use App\Models\User;
use App\Models\EstadoIncidencia;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalIncidencias = Incidencia::count();
        $totalUsuarios = User::where('activo', true)->count();

        $porEstado = Incidencia::select('estado_incidencia_id', DB::raw('count(*) as total'))
            ->with('estado')
            ->groupBy('estado_incidencia_id')
            ->get();

        $porTipo = Incidencia::select('tipo_incidencia_id', DB::raw('count(*) as total'))
            ->with('tipo')
            ->groupBy('tipo_incidencia_id')
            ->get();

        $porPrioridad = Incidencia::select('prioridad', DB::raw('count(*) as total'))
            ->groupBy('prioridad')
            ->get();

        $recientes = Incidencia::with(['usuario', 'estado', 'ciudad'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'total_incidencias' => $totalIncidencias,
            'total_usuarios' => $totalUsuarios,
            'por_estado' => $porEstado,
            'por_tipo' => $porTipo,
            'por_prioridad' => $porPrioridad,
            'recientes' => $recientes,
        ]);
    }
}