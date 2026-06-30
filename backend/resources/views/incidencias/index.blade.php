@extends('layouts.app')

@section('title', 'Incidencias')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Incidencias</h1>

        <a href="{{ route('incidencias.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nueva Incidencia
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Listado de incidencias registradas</h3>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Ciudad</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Prioridad</th>
                        <th>Usuario</th>
                        <th>Fecha</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($incidencias as $incidencia)
                        <tr>
                            <td>{{ $incidencia->id }}</td>
                            <td>{{ $incidencia->titulo }}</td>
                            <td>{{ $incidencia->ciudad->nombre ?? 'Sin ciudad' }}</td>
                            <td>{{ $incidencia->tipo->nombre ?? 'Sin tipo' }}</td>
                            <td>
                                <span class="badge badge-{{ $incidencia->estado->color ?? 'secondary' }}">
                                    {{ $incidencia->estado->nombre ?? 'Sin estado' }}
                                </span>
                            </td>
                            <td>{{ $incidencia->prioridad }}</td>
                            <td>{{ $incidencia->usuario->name ?? 'Sin usuario' }}</td>
                            <td>{{ $incidencia->created_at->format('d/m/Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">
                                No hay incidencias registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection