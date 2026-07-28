<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tarea;
use App\Models\Incidencia;
use App\Services\NotificacionService;

class TareaController extends Controller
{
    protected NotificacionService $notificaciones;

    public function __construct(NotificacionService $notificaciones)
    {
        $this->notificaciones = $notificaciones;
    }

    /**
     * Lista las tareas de una incidencia. Solo Administrador (ver routes/api.php).
     */
    public function index($incidenciaId)
    {
        Incidencia::findOrFail($incidenciaId);

        $tareas = Tarea::with(['usuario.rol', 'creador'])
            ->where('incidencia_id', $incidenciaId)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($tareas);
    }

    /**
     * Crea una tarea dentro de una incidencia y la asigna a un Responsable.
     * Solo Administrador (ver routes/api.php).
     */
    public function store(Request $request, $incidenciaId)
    {
        $incidencia = Incidencia::findOrFail($incidenciaId);

        $request->validate([
            'usuario_id' => 'required|exists:users,id',
            'titulo' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
        ], [
            'usuario_id.required' => 'Debe seleccionar a quién asignar la tarea.',
            'usuario_id.exists' => 'El usuario seleccionado no existe.',
            'titulo.required' => 'La tarea necesita un título.',
            'titulo.max' => 'El título no puede superar los 150 caracteres.',
        ]);

        $tarea = Tarea::create([
            'incidencia_id' => $incidenciaId,
            'usuario_id' => $request->usuario_id,
            'creado_por' => $request->user()->id,
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'estado' => 'Pendiente',
        ]);

        $this->notificaciones->crear(
            $request->usuario_id,
            'Nueva tarea asignada',
            'Se te asignó la tarea "' . $request->titulo . '" en la incidencia #' . $incidencia->id . '.',
            $incidencia->id
        );

        return response()->json($tarea->load(['usuario.rol', 'creador']), 201);
    }

    /**
     * Muestra el detalle de una tarea.
     * Puede verla: el Responsable asignado, quien la creó, o un Administrador.
     */
    public function show(Request $request, $id)
    {
        $tarea = Tarea::with(['usuario.rol', 'creador', 'incidencia'])->findOrFail($id);

        $usuarioAutenticado = $request->user();
        $esAdmin = $usuarioAutenticado->rol && $usuarioAutenticado->rol->nombre === 'Administrador';
        $esAsignado = $tarea->usuario_id === $usuarioAutenticado->id;
        $esCreador = $tarea->creado_por === $usuarioAutenticado->id;

        if (!$esAdmin && !$esAsignado && !$esCreador) {
            return response()->json([
                'message' => 'No tiene permisos para ver esta tarea.'
            ], 403);
        }

        return response()->json($tarea);
    }

    /**
     * Lista las tareas asignadas al usuario autenticado.
     */
    public function misTareas(Request $request)
    {
        $query = Tarea::with(['incidencia.tipo', 'incidencia.ciudad', 'incidencia.estado'])
            ->where('usuario_id', $request->user()->id);

        if ($request->estado) {
            $query->where('estado', $request->estado);
        }

        $tareas = $query->orderBy('created_at', 'desc')->get();

        return response()->json($tareas);
    }

    /**
     * Actualiza el estado y la nota de avance de una tarea.
     * Administrador o Responsable (ver routes/api.php); el Responsable
     * solo puede actualizar sus propias tareas asignadas.
     */
    public function actualizarEstado(Request $request, $id)
    {
        $tarea = Tarea::findOrFail($id);

        $usuarioAutenticado = $request->user();
        $esAdmin = $usuarioAutenticado->rol && $usuarioAutenticado->rol->nombre === 'Administrador';
        $esAsignado = $tarea->usuario_id === $usuarioAutenticado->id;

        if (!$esAdmin && !$esAsignado) {
            return response()->json([
                'message' => 'Solo el Responsable asignado o un Administrador pueden actualizar esta tarea.'
            ], 403);
        }

        $request->validate([
            'estado' => 'required|in:Pendiente,En Proceso,Completada',
            'nota_avance' => 'nullable|string',
        ], [
            'estado.required' => 'Debe indicar el nuevo estado de la tarea.',
            'estado.in' => 'El estado debe ser Pendiente, En Proceso o Completada.',
        ]);

        $tarea->update([
            'estado' => $request->estado,
            'nota_avance' => $request->nota_avance,
        ]);

        if ($tarea->creado_por !== $usuarioAutenticado->id) {
            $this->notificaciones->crear(
                $tarea->creado_por,
                'Actualización de tarea',
                'La tarea "' . $tarea->titulo . '" cambió a estado "' . $request->estado . '".',
                $tarea->incidencia_id
            );
        }

        return response()->json($tarea->load(['usuario.rol', 'creador']));
    }

    /**
     * Elimina una tarea. Solo Administrador (ver routes/api.php).
     */
    public function destroy($id)
    {
        $tarea = Tarea::findOrFail($id);
        $tarea->delete();

        return response()->json(['message' => 'Tarea eliminada correctamente.']);
    }
}
