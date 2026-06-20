<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoIncidencia extends Model
{
    protected $table = 'tipos_incidencia';
    protected $fillable = ['nombre', 'descripcion'];

    public function subtipos()
    {
        return $this->hasMany(SubtipoIncidencia::class, 'tipo_incidencia_id');
    }
}