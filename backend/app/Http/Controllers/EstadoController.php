<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EstadoIncidencia;

class EstadoController extends Controller
{
    public function index()
    {
        return response()->json(EstadoIncidencia::all());
    }

    public function store(Request $request)
    {
        $request->validate(['nombre' => 'required|string|max:50|unique:estados_incidencia']);
        $estado = EstadoIncidencia::create($request->only(['nombre', 'color', 'descripcion']));
        return response()->json($estado, 201);
    }

    public function update(Request $request, $id)
    {
        $estado = EstadoIncidencia::findOrFail($id);
        $estado->update($request->only(['nombre', 'color', 'descripcion']));
        return response()->json($estado);
    }
}