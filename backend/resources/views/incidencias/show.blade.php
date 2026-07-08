@extends('layouts.app')

@section('title', 'Detalle de Incidencia')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Incidencia #{{ $incidencia->id }}</h1>
        <a href="{{ route('incidencias.index') }}" class="btn btn-secondary">Volver</a>
    </div>

    <div id="alertEstado" class="alert d-none"></div>
    <div id="alertComentario" class="alert d-none"></div>

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
                    <p><strong>Estado actual:</strong>
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
            <h3 class="card-title">Cambiar Estado</h3>
        </div>

        <div class="card-body">
            <form id="formEstado">

                <div class="form-group">
                    <label>Nuevo Estado</label>
                    <select id="estado_incidencia_id" class="form-control">
                        <option value="">Seleccione...</option>
                        @foreach($estados as $estado)
                            <option value="{{ $estado->id }}" {{ $incidencia->estado_incidencia_id == $estado->id ? 'selected' : '' }}>
                                {{ $estado->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Observación</label>
                    <textarea
                        id="observacion"
                        class="form-control"
                        rows="2"
                        placeholder="Ej: Se inició la revisión de la incidencia"></textarea>
                </div>

                <button type="submit" class="btn btn-success">
                    <i class="fas fa-sync-alt"></i> Actualizar Estado
                </button>

            </form>
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
                        <tr>
                            <td colspan="3" class="text-center">
                                Sin historial registrado.
                            </td>
                        </tr>
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

            <form id="formComentario" class="mb-3">
                <div class="form-group">
                    <label>Nuevo Comentario</label>
                    <textarea
                        id="comentario"
                        class="form-control"
                        rows="2"
                        placeholder="Escriba un comentario sobre la incidencia"></textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-comment"></i> Guardar Comentario
                </button>
            </form>

            <hr>

            @forelse($incidencia->comentarios as $c)
                <p>
                    <strong>{{ $c->usuario->name ?? 'N/A' }}:</strong>
                    {{ $c->comentario }}
                    <small class="text-muted">
                        ({{ $c->created_at->format('d/m/Y H:i') }})
                    </small>
                </p>
            @empty
                <p class="text-muted">Sin comentarios aún.</p>
            @endforelse

        </div>
    </div>

</div>

@endsection

@section('scripts')

<script>
document.getElementById('formEstado').addEventListener('submit', async function (e) {
    e.preventDefault();

    const alertEstado = document.getElementById('alertEstado');
    alertEstado.className = 'alert d-none';

    const payload = {
        estado_incidencia_id: document.getElementById('estado_incidencia_id').value,
        observacion: document.getElementById('observacion').value
    };

    try {
        const response = await authFetch('/api/incidencias/{{ $incidencia->id }}/estado', {
            method: 'POST',
            body: JSON.stringify(payload)
        });

        const data = await response.json();

        if (!response.ok) {
            alertEstado.textContent = data.message || 'No se pudo cambiar el estado';
            alertEstado.classList.remove('d-none');
            alertEstado.classList.add('alert-danger');
            return;
        }

        location.reload();

    } catch (error) {
        alertEstado.textContent = 'Error de conexión con el servidor';
        alertEstado.classList.remove('d-none');
        alertEstado.classList.add('alert-danger');
    }
});

document.getElementById('formComentario').addEventListener('submit', async function (e) {
    e.preventDefault();

    const alertComentario = document.getElementById('alertComentario');
    alertComentario.className = 'alert d-none';

    const payload = {
        comentario: document.getElementById('comentario').value
    };

    try {
        const response = await authFetch('/api/incidencias/{{ $incidencia->id }}/comentarios', {
            method: 'POST',
            body: JSON.stringify(payload)
        });

        const data = await response.json();

        if (!response.ok) {
            alertComentario.textContent = data.message || 'No se pudo guardar el comentario';
            alertComentario.classList.remove('d-none');
            alertComentario.classList.add('alert-danger');
            return;
        }

        location.reload();

    } catch (error) {
        alertComentario.textContent = 'Error de conexión con el servidor';
        alertComentario.classList.remove('d-none');
        alertComentario.classList.add('alert-danger');
    }
});
</script>

@endsection