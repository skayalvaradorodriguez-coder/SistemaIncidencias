<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoIncidencia extends Model
{
    protected $table = 'estados_incidencia';
    protected $fillable = ['nombre', 'color', 'descripcion'];
}