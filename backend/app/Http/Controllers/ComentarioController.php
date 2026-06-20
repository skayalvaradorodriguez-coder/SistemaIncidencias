<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comentario;

class ComentarioController extends Controller
{
    public function store(Request $request, $incidenciaId)
    {
        $request->validate([
            'comentario' => 'required|string'
        ]);

        $comentario = Comentario::create([
            'incidencia_id' => $incidenciaId,
            'usuario_id' => $request->user()->id,
            'comentario' => $request->comentario
        ]);

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