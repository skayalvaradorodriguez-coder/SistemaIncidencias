<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialEstado extends Model
{
    protected $table = 'historial_estados';
    protected $fillable = [
        'incidencia_id', 'estado_anterior_id',
        'estado_nuevo_id', 'usuario_id',
        'observacion', 'fecha_cambio'
    ];

    public function incidencia()
    {
        return $this->belongsTo(Incidencia::class, 'incidencia_id');
    }

    public function estadoAnterior()
    {
        return $this->belongsTo(EstadoIncidencia::class, 'estado_anterior_id');
    }

    public function estadoNuevo()
    {
        return $this->belongsTo(EstadoIncidencia::class, 'estado_nuevo_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}