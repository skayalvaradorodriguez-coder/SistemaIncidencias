@extends('layouts.app')

@section('title', 'Detalle de Incidencia')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Incidencia #{{ $incidencia->id }}</h1>
        <a href="{{ route('incidencias.index') }}" class="btn btn-secondary">Volver</a>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ $incidencia->titulo }}</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Descripción:</strong> {{ $incidencia->descripcion }}</p>
                    <p><strong>Ciudad:</strong> {{ $incidencia->ciudad->nombre ?? 'N/A' }}</p>
                    <p><strong>Tipo:</strong> {{ $incidencia->tipo->nombre ?? 'N/A' }}</p>
                    <p><strong>Subtipo:</strong> {{ $incidencia->subtipo->nombre ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Estado:</strong>
                        <span class="badge badge-{{ $incidencia->estado->color ?? 'secondary' }}">
                            {{ $incidencia->estado->nombre ?? 'N/A' }}
                        </span>
                    </p>
                    <p><strong>Prioridad:</strong> {{ $incidencia->prioridad }}</p>
                    <p><strong>Dirección:</strong> {{ $incidencia->direccion ?? 'N/A' }}</p>
                    <p><strong>Reportado por:</strong> {{ $incidencia->usuario->name ?? 'N/A' }}</p>
                    <p><strong>Fecha:</strong> {{ $incidencia->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            <a href="{{ route('incidencias.edit', $incidencia->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar
            </a>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h3 class="card-title">Historial de Estados</h3>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Estado Nuevo</th>
                        <th>Observación</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($incidencia->historial as $h)
                        <tr>
                            <td>{{ $h->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $h->estadoNuevo->nombre ?? 'N/A' }}</td>
                            <td>{{ $h->observacion ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center">Sin historial registrado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h3 class="card-title">Comentarios</h3>
        </div>
        <div class="card-body">
            @forelse($incidencia->comentarios as $c)
                <p><strong>{{ $c->usuario->name ?? 'N/A' }}:</strong> {{ $c->comentario }}
                    <small class="text-muted">({{ $c->created_at->format('d/m/Y H:i') }})</small>
                </p>
            @empty
                <p class="text-muted">Sin comentarios aún.</p>
            @endforelse
        </div>
    </div>

</div>

@endsection