<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Incidencia;
use App\Models\HistorialEstado;
use App\Models\EstadoIncidencia;
use App\Models\Pais;
use App\Models\TipoIncidencia;

class IncidenciaController extends Controller
{
    public function index(Request $request)
    {
        $query = Incidencia::with([
            'usuario',
            'ciudad',
            'tipo',
            'subtipo',
            'estado'
        ]);

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

        return response()->json(
            $query->orderBy('created_at', 'desc')->get()
        );
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
        'latitud' => 'nullable|numeric|between:-90,90',
        'longitud' => 'nullable|numeric|between:-180,180',
        'direccion' => 'nullable|string|max:255',
    ], [
        'titulo.required' => 'El título es obligatorio.',
        'descripcion.required' => 'La descripción es obligatoria.',
        'ciudad_id.required' => 'Debe seleccionar una ciudad.',
        'tipo_incidencia_id.required' => 'Debe seleccionar un tipo de incidencia.',
        'subtipo_incidencia_id.required' => 'Debe seleccionar un subtipo de incidencia.',
        'prioridad.required' => 'Debe seleccionar una prioridad.',

        'latitud.numeric' => 'La latitud debe ser un valor numérico.',
        'latitud.between' => 'La latitud debe estar entre -90 y 90.',

        'longitud.numeric' => 'La longitud debe ser un valor numérico.',
        'longitud.between' => 'La longitud debe estar entre -180 y 180.',
    ]);

    $estadoPendiente = EstadoIncidencia::where('nombre', 'Pendiente')->first();

    if (!$estadoPendiente) {
        return response()->json([
            'message' => 'No existe el estado Pendiente en la base de datos.'
        ], 500);
    }

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

    return response()->json(
        $incidencia->load([
            'usuario',
            'ciudad',
            'tipo',
            'subtipo',
            'estado'
        ]),
        201
    );
}

    public function show($id)
    {
        $incidencia = Incidencia::with([
            'usuario',
            'ciudad.provincia.pais',
            'tipo',
            'subtipo',
            'estado',
            'historial.estadoNuevo',
            'asignaciones.usuario',
            'comentarios.usuario'
        ])->findOrFail($id);

        return response()->json($incidencia);
    }

    public function update(Request $request, $id)
    {
        $incidencia = Incidencia::findOrFail($id);

        $request->validate([
            'titulo' => 'required|string|max:200',
            'descripcion' => 'required|string',
            'prioridad' => 'required|in:Baja,Media,Alta,Crítica',
            'ciudad_id' => 'required|exists:ciudades,id',
            'tipo_incidencia_id' => 'nullable|exists:tipos_incidencia,id',
            'subtipo_incidencia_id' => 'nullable|exists:subtipos_incidencia,id',
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
            'direccion' => 'nullable|string|max:255',
        ]);

        $incidencia->update(
            $request->only([
                'titulo',
                'descripcion',
                'prioridad',
                'ciudad_id',
                'tipo_incidencia_id',
                'subtipo_incidencia_id',
                'direccion',
                'latitud',
                'longitud'
            ])
        );

        return response()->json(
            $incidencia->load([
                'usuario',
                'ciudad',
                'tipo',
                'subtipo',
                'estado'
            ])
        );
    }

    public function cambiarEstado(Request $request, $id)
    {
        $request->validate([
            'estado_incidencia_id' => 'required|exists:estados_incidencia,id',
            'observacion' => 'nullable|string'
        ]);

        $incidencia = Incidencia::findOrFail($id);

        $estadoAnteriorId = $incidencia->estado_incidencia_id;

        if ($estadoAnteriorId == $request->estado_incidencia_id) {
            return response()->json([
                'message' => 'La incidencia ya se encuentra en ese estado.'
            ], 422);
        }

        $incidencia->update([
            'estado_incidencia_id' => $request->estado_incidencia_id
        ]);

        HistorialEstado::create([
            'incidencia_id' => $incidencia->id,
            'estado_anterior_id' => $estadoAnteriorId,
            'estado_nuevo_id' => $request->estado_incidencia_id,
            'usuario_id' => $request->user()->id,
            'observacion' => $request->observacion ?? 'Cambio de estado',
        ]);

        return response()->json(
            $incidencia->load([
                'estado',
                'historial.estadoNuevo'
            ])
        );
    }

    public function destroy($id)
    {
        $incidencia = Incidencia::findOrFail($id);

        $incidencia->delete();

        return response()->json([
            'message' => 'Incidencia eliminada correctamente.'
        ]);
    }

    public function vistaIndex()
    {
        $incidencias = Incidencia::with([
            'usuario',
            'ciudad',
            'tipo',
            'subtipo',
            'estado'
        ])
        ->orderBy('created_at', 'desc')
        ->get();

        return view('incidencias.index', compact('incidencias'));
    }

    public function vistaCreate()
    {
        $paises = Pais::with('provincias.ciudades')->get();

        $tipos = TipoIncidencia::with('subtipos')->get();

        return view('incidencias.create', compact('paises', 'tipos'));
    }

    public function vistaShow($id)
    {
        $incidencia = Incidencia::with([
            'usuario',
            'ciudad.provincia.pais',
            'tipo',
            'subtipo',
            'estado',
            'historial.estadoNuevo',
            'asignaciones.usuario',
            'comentarios.usuario'
        ])->findOrFail($id);

        $estados = EstadoIncidencia::all();

        return view('incidencias.show', compact('incidencia', 'estados'));
    }

    public function vistaEdit($id)
    {
        $incidencia = Incidencia::with([
            'ciudad.provincia.pais',
            'tipo',
            'subtipo',
            'estado'
        ])->findOrFail($id);

        $paises = Pais::with('provincias.ciudades')->get();

        $tipos = TipoIncidencia::with('subtipos')->get();

        return view('incidencias.edit', compact(
            'incidencia',
            'paises',
            'tipos'
        ));
    }
}