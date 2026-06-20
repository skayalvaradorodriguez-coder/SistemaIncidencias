<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Incidencia;
use App\Models\HistorialEstado;
use App\Models\EstadoIncidencia;

class IncidenciaController extends Controller
{
    public function index(Request $request)
    {
        $query = Incidencia::with(['usuario', 'ciudad', 'tipo', 'subtipo', 'estado']);

        if ($request->estado_id) {
            $query->where('estado_incidencia_id', $request->estado_id);
        }
        if ($request->tipo_id) {
            $query->where('tipo_incidencia_id', $request->tipo_id);
        }
        if ($request->prioridad) {
            $query->where('prioridad', $request->prioridad);
        }
        if ($request->ciudad_id) {
            $query->where('ciudad_id', $request->ciudad_id);
        }

        return response()->json($query->orderBy('created_at', 'desc')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:200',
            'descripcion' => 'required|string',
            'ciudad_id' => 'required|exists:ciudades,id',
            'tipo_incidencia_id' => 'required|exists:tipos_incidencia,id',
            'subtipo_incidencia_id' => 'required|exists:subtipos_incidencia,id',
            'prioridad' => 'required|in:Baja,Media,Alta,Crítica',
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
            'direccion' => 'nullable|string|max:255',
        ]);

        $estadoPendiente = EstadoIncidencia::where('nombre', 'Pendiente')->first();

        $incidencia = Incidencia::create([
            'usuario_id' => $request->user()->id,
            'ciudad_id' => $request->ciudad_id,
            'tipo_incidencia_id' => $request->tipo_incidencia_id,
            'subtipo_incidencia_id' => $request->subtipo_incidencia_id,
            'estado_incidencia_id' => $estadoPendiente->id,
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'prioridad' => $request->prioridad,
            'latitud' => $request->latitud,
            'longitud' => $request->longitud,
            'direccion' => $request->direccion,
        ]);

        HistorialEstado::create([
            'incidencia_id' => $incidencia->id,
            'estado_anterior_id' => null,
            'estado_nuevo_id' => $estadoPendiente->id,
            'usuario_id' => $request->user()->id,
            'observacion' => 'Incidencia registrada',
        ]);

        return response()->json($incidencia->load(['usuario', 'ciudad', 'tipo', 'subtipo', 'estado']), 201);
    }

    public function show($id)
    {
        $incidencia = Incidencia::with(['usuario', 'ciudad.provincia.pais', 'tipo', 'subtipo', 'estado', 'historial.estadoNuevo', 'asignaciones.usuario', 'comentarios.usuario'])->findOrFail($id);
        return response()->json($incidencia);
    }

    public function update(Request $request, $id)
    {
        $incidencia = Incidencia::findOrFail($id);

        $request->validate([
            'titulo' => 'string|max:200',
            'descripcion' => 'string',
            'prioridad' => 'in:Baja,Media,Alta,Crítica',
            'ciudad_id' => 'exists:ciudades,id',
        ]);

        $incidencia->update($request->only(['titulo', 'descripcion', 'prioridad', 'ciudad_id', 'direccion', 'latitud', 'longitud']));

        return response()->json($incidencia->load(['usuario', 'ciudad', 'tipo', 'subtipo', 'estado']));
    }

    public function cambiarEstado(Request $request, $id)
    {
        $request->validate([
            'estado_incidencia_id' => 'required|exists:estados_incidencia,id',
            'observacion' => 'nullable|string'
        ]);

        $incidencia = Incidencia::findOrFail($id);
        $estadoAnteriorId = $incidencia->estado_incidencia_id;

        $incidencia->update(['estado_incidencia_id' => $request->estado_incidencia_id]);

        HistorialEstado::create([
            'incidencia_id' => $incidencia->id,
            'estado_anterior_id' => $estadoAnteriorId,
            'estado_nuevo_id' => $request->estado_incidencia_id,
            'usuario_id' => $request->user()->id,
            'observacion' => $request->observacion,
        ]);

        return response()->json($incidencia->load(['estado', 'historial.estadoNuevo']));
    }

    public function destroy($id)
    {
        $incidencia = Incidencia::findOrFail($id);
        $incidencia->delete();
        return response()->json(['message' => 'Incidencia eliminada']);
    }
}