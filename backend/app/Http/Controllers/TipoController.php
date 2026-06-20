<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TipoIncidencia;
use App\Models\SubtipoIncidencia;

class TipoController extends Controller
{
    public function index()
    {
        return response()->json(TipoIncidencia::with('subtipos')->get());
    }

    public function store(Request $request)
    {
        $request->validate(['nombre' => 'required|string|max:100|unique:tipos_incidencia']);
        $tipo = TipoIncidencia::create($request->only(['nombre', 'descripcion']));
        return response()->json($tipo, 201);
    }

    public function update(Request $request, $id)
    {
        $tipo = TipoIncidencia::findOrFail($id);
        $tipo->update($request->only(['nombre', 'descripcion']));
        return response()->json($tipo);
    }

    public function destroy($id)
    {
        TipoIncidencia::findOrFail($id)->delete();
        return response()->json(['message' => 'Tipo eliminado']);
    }

    public function storeSubtipo(Request $request, $tipoId)
    {
        $request->validate(['nombre' => 'required|string|max:100']);
        $subtipo = SubtipoIncidencia::create([
            'tipo_incidencia_id' => $tipoId,
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion
        ]);
        return response()->json($subtipo, 201);
    }
}