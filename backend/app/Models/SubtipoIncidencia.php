<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubtipoIncidencia extends Model
{
    protected $table = 'subtipos_incidencia';
    protected $fillable = ['tipo_incidencia_id', 'nombre', 'descripcion'];

    public function tipo()
    {
        return $this->belongsTo(TipoIncidencia::class, 'tipo_incidencia_id');
    }
}