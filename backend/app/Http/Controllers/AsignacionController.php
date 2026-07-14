<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asignacion;
use App\Models\Incidencia;
use App\Services\NotificacionService;

class AsignacionController extends Controller
{
    protected NotificacionService $notificaciones;

    public function __construct(NotificacionService $notificaciones)
    {
        $this->notificaciones = $notificaciones;
    }

    /**
     * Lista los usuarios asignados a una incidencia.
     */
    public function index($incidenciaId)
    {
        Incidencia::findOrFail($incidenciaId);

        $asignaciones = Asignacion::with('usuario.rol')
            ->where('incidencia_id', $incidenciaId)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($asignaciones);
    }

    /**
     * Asigna un usuario a una incidencia con un rol (Responsable / Apoyo).
     */
    public function store(Request $request, $incidenciaId)
    {
        $incidencia = Incidencia::findOrFail($incidenciaId);

        $request->validate([
            'usuario_id' => 'required|exists:users,id',
            'rol' => 'required|in:Responsable,Apoyo',
        ], [
            'usuario_id.required' => 'Debe seleccionar un usuario.',
            'usuario_id.exists' => 'El usuario seleccionado no existe.',
            'rol.required' => 'Debe indicar el rol de la asignación.',
            'rol.in' => 'El rol debe ser Responsable o Apoyo.',
        ]);

        $yaAsignado = Asignacion::where('incidencia_id', $incidenciaId)
            ->where('usuario_id', $request->usuario_id)
            ->exists();

        if ($yaAsignado) {
            return response()->json([
                'message' => 'Este usuario ya está asignado a la incidencia.'
            ], 422);
        }

        $asignacion = Asignacion::create([
            'incidencia_id' => $incidenciaId,
            'usuario_id' => $request->usuario_id,
            'rol' => $request->rol,
        ]);

        $this->notificaciones->notificarNuevaAsignacion(
            $incidencia,
            $request->usuario_id,
            $request->rol,
            $request->user()->id
        );

        return response()->json($asignacion->load('usuario.rol'), 201);
    }

    /**
     * Cambia el rol de una asignación existente.
     */
    public function update(Request $request, $id)
    {
        $asignacion = Asignacion::findOrFail($id);

        $request->validate([
            'rol' => 'required|in:Responsable,Apoyo',
        ]);

        $asignacion->update(['rol' => $request->rol]);

        return response()->json($asignacion->load('usuario.rol'));
    }

    /**
     * Quita a un usuario de una incidencia.
     */
    public function destroy($id)
    {
        $asignacion = Asignacion::findOrFail($id);
        $asignacion->delete();

        return response()->json(['message' => 'Asignación eliminada correctamente.']);
    }
}