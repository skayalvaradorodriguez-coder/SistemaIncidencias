<?php

namespace App\Http\Controllers;

use App\Models\Incidencia;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Dashboard WEB (Blade)
     */
    public function index()
    {
        $totalIncidencias = Incidencia::count();

        $totalUsuarios = User::where('activo', true)->count();

        $pendientes = Incidencia::whereHas('estado', function ($query) {
            $query->where('nombre', 'Pendiente');
        })->count();

        $enProceso = Incidencia::whereHas('estado', function ($query) {
            $query->where('nombre', 'En Proceso');
        })->count();

        $resueltas = Incidencia::whereHas('estado', function ($query) {
            $query->where('nombre', 'Resuelto');
        })->count();

        $recientes = Incidencia::with([
            'usuario',
            'estado',
            'ciudad'
        ])
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();

        // Incidencias georreferenciadas para el mapa general
        $conUbicacion = Incidencia::with(['estado', 'tipo'])
            ->whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->get([
                'id',
                'titulo',
                'prioridad',
                'latitud',
                'longitud',
                'estado_incidencia_id',
                'tipo_incidencia_id',
            ]);

        // Conteo por tipo de incidencia (para gráfico de barras)
        $porTipo = Incidencia::selectRaw('tipos_incidencia.nombre, count(*) as total')
            ->join('tipos_incidencia', 'tipos_incidencia.id', '=', 'incidencias.tipo_incidencia_id')
            ->groupBy('tipos_incidencia.nombre')
            ->orderByDesc('total')
            ->get();

        // Conteo por prioridad (para gráfico)
        $porPrioridad = Incidencia::selectRaw('prioridad, count(*) as total')
            ->groupBy('prioridad')
            ->get();

        return view('dashboard', compact(
            'totalIncidencias',
            'totalUsuarios',
            'pendientes',
            'enProceso',
            'resueltas',
            'recientes',
            'conUbicacion',
            'porTipo',
            'porPrioridad'
        ));
    }

    /**
     * Dashboard API (JSON)
     */
    public function api()
    {
        $totalIncidencias = Incidencia::count();

        $totalUsuarios = User::where('activo', true)->count();

        $porEstado = Incidencia::select(
                'estado_incidencia_id',
                DB::raw('count(*) as total')
            )
            ->with('estado')
            ->groupBy('estado_incidencia_id')
            ->get();

        $porTipo = Incidencia::select(
                'tipo_incidencia_id',
                DB::raw('count(*) as total')
            )
            ->with('tipo')
            ->groupBy('tipo_incidencia_id')
            ->get();

        $porPrioridad = Incidencia::select(
                'prioridad',
                DB::raw('count(*) as total')
            )
            ->groupBy('prioridad')
            ->get();

        $recientes = Incidencia::with([
            'usuario',
            'estado',
            'ciudad'
        ])
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