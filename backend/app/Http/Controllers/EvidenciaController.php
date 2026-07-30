<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evidencia;
use App\Models\Incidencia;
use App\Models\Asignacion;
use App\Services\NotificacionService;

class EvidenciaController extends Controller
{
    protected NotificacionService $notificaciones;

    public function __construct(NotificacionService $notificaciones)
    {
        $this->notificaciones = $notificaciones;
    }

    /**
     * Lista las evidencias subidas para una incidencia (fotos del avance/arreglo).
     */
    public function index($incidenciaId)
    {
        Incidencia::findOrFail($incidenciaId);

        $evidencias = Evidencia::with('usuario')
            ->where('incidencia_id', $incidenciaId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($evidencias);
    }

    /**
     * Sube una evidencia (foto + comentario opcional) para una incidencia.
     * Solo puede subirla el Responsable asignado a esa incidencia específica,
     * o un Administrador.
     */
    public function store(Request $request, $incidenciaId)
    {
        $incidencia = Incidencia::findOrFail($incidenciaId);
        $usuario = $request->user();
        $esAdministrador = $usuario->rol && $usuario->rol->nombre === 'Administrador';

        if (!$esAdministrador) {
            $estaAsignado = Asignacion::where('incidencia_id', $incidenciaId)
                ->where('usuario_id', $usuario->id)
                ->exists();

            if (!$estaAsignado) {
                return response()->json([
                    'message' => 'Solo un Responsable asignado a esta incidencia puede subir evidencia.'
                ], 403);
            }
        }

        $request->validate([
            'foto' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
            'comentario' => 'nullable|string|max:500',
        ], [
            'foto.required' => 'Debe adjuntar una foto como evidencia.',
            'foto.image' => 'El archivo debe ser una imagen.',
            'foto.max' => 'La imagen no debe superar los 4 MB.',
        ]);

        $archivo = $request->file('foto');
        $rutaFoto = 'data:' . $archivo->getMimeType() . ';base64,' . base64_encode(
            file_get_contents($archivo->getRealPath())
        );

        $evidencia = Evidencia::create([
            'incidencia_id' => $incidenciaId,
            'usuario_id' => $usuario->id,
            'ruta_foto' => $rutaFoto,
            'comentario' => $request->comentario,
        ]);

        $this->notificaciones->notificarNuevaEvidencia(
            $incidencia,
            $usuario->id,
            $request->comentario
        );

        return response()->json($evidencia->load('usuario'), 201);
    }

    /**
     * Elimina una evidencia. Solo quien la subió o un Administrador.
     */
    public function destroy(Request $request, $id)
    {
        $evidencia = Evidencia::findOrFail($id);
        $usuario = $request->user();
        $esAdministrador = $usuario->rol && $usuario->rol->nombre === 'Administrador';

        if (!$esAdministrador && $evidencia->usuario_id !== $usuario->id) {
            return response()->json([
                'message' => 'No tiene permisos para eliminar esta evidencia.'
            ], 403);
        }

        if ($evidencia->ruta_foto && !str_starts_with($evidencia->ruta_foto, 'data:')) {
            \Storage::disk('public')->delete($evidencia->ruta_foto);
        }
        $evidencia->delete();

        return response()->json(['message' => 'Evidencia eliminada correctamente.']);
    }
}