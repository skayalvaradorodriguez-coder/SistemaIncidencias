<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notificacion;

class NotificacionController extends Controller
{
    public function index(Request $request)
    {
        $notificaciones = Notificacion::where('usuario_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($notificaciones);
    }

    public function marcarLeida($id)
    {
        $notificacion = Notificacion::findOrFail($id);
        $notificacion->update(['leida' => true]);
        return response()->json($notificacion);
    }

    public function marcarTodasLeidas(Request $request)
    {
        Notificacion::where('usuario_id', $request->user()->id)
            ->update(['leida' => true]);

        return response()->json(['message' => 'Todas marcadas como leídas']);
    }
}