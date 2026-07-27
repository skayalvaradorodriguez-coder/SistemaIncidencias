<?php

namespace App\Http\Controllers;

use App\Models\Asignacion;
use App\Models\Incidencia;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Panel de Coordinación del Administrador.
 *
 * Reúne dos vistas complementarias:
 *  - responsables(): carga de trabajo actual de cada Responsable.
 *  - sinAsignar(): incidencias que todavía no tienen un Responsable asignado.
 *
 * Ambas alimentan la pantalla "Coordinador / Asignar Tareas", desde donde
 * el Administrador asigna incidencias usando el endpoint que ya existe:
 * POST /api/incidencias/{id}/asignaciones (AsignacionController@store).
 */
class CoordinadorController extends Controller
{
    /**
     * Lista los usuarios con rol Responsable junto a sus métricas de carga
     * de trabajo (asignadas, activas, resueltas, tasa de resolución) y la
     * incidencia que están atendiendo actualmente, si hay alguna en curso.
     */
    public function responsables()
    {
        $responsables = User::whereHas('rol', function ($query) {
                $query->where('nombre', 'Responsable');
            })
            ->where('activo', true)
            ->orderBy('name')
            ->get();

        $resultado = $responsables->map(function ($responsable) {
            $incidencias = Asignacion::with('incidencia.estado')
                ->where('usuario_id', $responsable->id)
                ->get()
                ->pluck('incidencia')
                ->filter();

            $activas = $incidencias->filter(function ($incidencia) {
                return in_array($incidencia->estado->nombre ?? '', ['Pendiente', 'En Proceso']);
            });

            $resueltas = $incidencias->filter(function ($incidencia) {
                return ($incidencia->estado->nombre ?? '') === 'Resuelto';
            });

            $enProceso = $incidencias
                ->filter(function ($incidencia) {
                    return ($incidencia->estado->nombre ?? '') === 'En Proceso';
                })
                ->sortByDesc('updated_at')
                ->first();

            return [
                'id' => $responsable->id,
                'name' => $responsable->name,
                'apellido' => $responsable->apellido,
                'email' => $responsable->email,
                'metricas' => [
                    'total_asignadas' => $incidencias->count(),
                    'activas' => $activas->count(),
                    'resueltas' => $resueltas->count(),
                    'tasa_resolucion' => $incidencias->count() > 0
                        ? round(($resueltas->count() / $incidencias->count()) * 100, 1)
                        : 0,
                ],
                'actividad_actual' => $enProceso ? [
                    'incidencia_id' => $enProceso->id,
                    'titulo' => $enProceso->titulo,
                    'desde' => $enProceso->updated_at,
                ] : null,
            ];
        });

        return response()->json($resultado->values());
    }

    /**
     * Lista las incidencias que aún no tienen ningún usuario asignado.
     * Admite filtro por prioridad/tipo y orden por fecha o por prioridad.
     */
    public function sinAsignar(Request $request)
    {
        $query = Incidencia::with(['usuario', 'tipo', 'ciudad', 'estado'])
            ->whereDoesntHave('asignaciones');

        if ($request->prioridad) {
            $query->where('prioridad', $request->prioridad);
        }

        if ($request->tipo_id) {
            $query->where('tipo_incidencia_id', $request->tipo_id);
        }

        $orden = $request->get('orden', 'fecha');

        if ($orden === 'prioridad') {
            $query->orderByRaw("CASE prioridad
                WHEN 'Crítica' THEN 1
                WHEN 'Alta' THEN 2
                WHEN 'Media' THEN 3
                WHEN 'Baja' THEN 4
                ELSE 5 END");
        } else {
            $query->orderBy('created_at', 'asc');
        }

        return response()->json($query->get());
    }
}
