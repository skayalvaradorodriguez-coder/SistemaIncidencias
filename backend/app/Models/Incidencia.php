<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incidencia extends Model
{
    protected $table = 'incidencias';
    protected $fillable = [
        'usuario_id', 'ciudad_id', 'tipo_incidencia_id',
        'subtipo_incidencia_id', 'estado_incidencia_id',
        'titulo', 'descripcion', 'prioridad',
        'latitud', 'longitud', 'direccion', 'foto', 'fecha_reporte'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class, 'ciudad_id');
    }

    public function tipo()
    {
        return $this->belongsTo(TipoIncidencia::class, 'tipo_incidencia_id');
    }

    public function subtipo()
    {
        return $this->belongsTo(SubtipoIncidencia::class, 'subtipo_incidencia_id');
    }

    public function estado()
    {
        return $this->belongsTo(EstadoIncidencia::class, 'estado_incidencia_id');
    }

    public function historial()
    {
        return $this->hasMany(HistorialEstado::class, 'incidencia_id');
    }

    public function asignaciones()
    {
        return $this->hasMany(Asignacion::class, 'incidencia_id');
    }

    public function comentarios()
    {
        return $this->hasMany(Comentario::class, 'incidencia_id');
    }
}