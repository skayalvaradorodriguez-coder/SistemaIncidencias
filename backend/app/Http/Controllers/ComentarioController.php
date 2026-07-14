<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comentario;
use App\Models\Incidencia;
use App\Services\NotificacionService;

class ComentarioController extends Controller
{
    protected NotificacionService $notificaciones;

    public function __construct(NotificacionService $notificaciones)
    {
        $this->notificaciones = $notificaciones;
    }

    public function store(Request $request, $incidenciaId)
    {
        $request->validate([
            'comentario' => 'required|string'
        ]);

        $incidencia = Incidencia::findOrFail($incidenciaId);

        $comentario = Comentario::create([
            'incidencia_id' => $incidenciaId,
            'usuario_id' => $request->user()->id,
            'comentario' => $request->comentario
        ]);

        $this->notificaciones->notificarNuevoComentario($incidencia, $request->user()->id);

        return response()->json($comentario->load('usuario'), 201);
    }

    public function index($incidenciaId)
    {
        $comentarios = Comentario::with('usuario')
            ->where('incidencia_id', $incidenciaId)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($comentarios);
    }

    public function destroy($id)
    {
        $comentario = Comentario::findOrFail($id);
        $comentario->delete();
        return response()->json(['message' => 'Comentario eliminado']);
    }
}