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

        // Tasa de resolución (% resueltas sobre el total)
        $tasaResolucion = $totalIncidencias > 0
            ? round(($resueltas / $totalIncidencias) * 100, 1)
            : 0;

        // Tiempo promedio de resolución en horas (desde la vista SQL)
        $tiempoPromedio = 0;
        try {
            $fila = DB::selectOne('SELECT horas_promedio FROM vista_tiempo_resolucion');
            $tiempoPromedio = $fila && $fila->horas_promedio ? round($fila->horas_promedio, 1) : 0;
        } catch (\Throwable $e) {
            $tiempoPromedio = 0;
        }

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

        // Top 5 ciudades con más incidencias
        $porCiudad = Incidencia::selectRaw('ciudades.nombre, count(*) as total')
            ->join('ciudades', 'ciudades.id', '=', 'incidencias.ciudad_id')
            ->groupBy('ciudades.nombre')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Incidencias por mes (últimos 6 meses) para la línea de tendencia
        $porMes = Incidencia::selectRaw("TO_CHAR(created_at, 'YYYY-MM') as mes, count(*) as total")
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        return view('dashboard', compact(
            'totalIncidencias',
            'totalUsuarios',
            'pendientes',
            'enProceso',
            'resueltas',
            'tasaResolucion',
            'tiempoPromedio',
            'recientes',
            'conUbicacion',
            'porTipo',
            'porPrioridad',
            'porCiudad',
            'porMes'
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