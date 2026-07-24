<?php

namespace App\Http\Controllers;

use App\Models\Pais;
use App\Models\Provincia;
use App\Models\Ciudad;

class UbicacionController extends Controller
{
    public function paises()
    {
        return response()->json(Pais::all());
    }

    public function provincias($paisId)
    {
        return response()->json(Provincia::where('pais_id', $paisId)->get());
    }

    public function ciudades($provinciaId)
    {
        return response()->json(Ciudad::where('provincia_id', $provinciaId)->get());
    }
}