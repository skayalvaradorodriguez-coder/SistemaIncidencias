@extends('layouts.app')

@section('title', 'Detalle de Incidencia')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    #mapa { height: 320px; border-radius: 4px; z-index: 1; }
</style>
@endsection

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

            @if($incidencia->latitud && $incidencia->longitud)
                <div class="mb-3">
                    <p><strong>Ubicación:</strong></p>
                    <div id="mapa"></div>
                </div>
            @endif

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
            <h3 class="card-title">Responsables asignados</h3>
        </div>

        <div class="card-body">

            <div id="alertAsignacion" class="alert d-none"></div>

            <form id="formAsignacion" class="form-row align-items-end mb-3">

                <div class="form-group col-md-6">
                    <label>Usuario</label>
                    <select id="asignacion_usuario_id" class="form-control" required>
                        <option value="">Seleccione un usuario...</option>
                        @foreach($usuarios as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} {{ $u->apellido }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-4">
                    <label>Rol</label>
                    <select id="asignacion_rol" class="form-control" required>
                        <option value="Responsable">Responsable</option>
                        <option value="Apoyo">Apoyo</option>
                    </select>
                </div>

                <div class="form-group col-md-2">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-user-plus"></i> Asignar
                    </button>
                </div>

            </form>

            <table class="table table-sm table-bordered" id="tablaAsignaciones">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Fecha de asignación</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($incidencia->asignaciones as $a)
                        <tr data-id="{{ $a->id }}">
                            <td>{{ $a->usuario->name ?? 'N/A' }} {{ $a->usuario->apellido ?? '' }}</td>
                            <td><span class="badge badge-{{ $a->rol === 'Responsable' ? 'primary' : 'secondary' }}">{{ $a->rol }}</span></td>
                            <td>{{ $a->fecha_asignacion ? \Carbon\Carbon::parse($a->fecha_asignacion)->format('d/m/Y H:i') : '-' }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-danger btn-quitar-asignacion" data-id="{{ $a->id }}">
                                    <i class="fas fa-user-minus"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr id="sinAsignaciones">
                            <td colspan="4" class="text-center">Sin responsables asignados.</td>
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

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
@if($incidencia->latitud && $incidencia->longitud)
    const mapaDetalle = L.map('mapa', { scrollWheelZoom: false })
        .setView([{{ $incidencia->latitud }}, {{ $incidencia->longitud }}], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(mapaDetalle);

    L.marker([{{ $incidencia->latitud }}, {{ $incidencia->longitud }}])
        .addTo(mapaDetalle)
        .bindPopup(@json($incidencia->titulo))
        .openPopup();
@endif

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

document.getElementById('formAsignacion').addEventListener('submit', async function (e) {
    e.preventDefault();

    const alertAsignacion = document.getElementById('alertAsignacion');
    alertAsignacion.className = 'alert d-none';

    const payload = {
        usuario_id: document.getElementById('asignacion_usuario_id').value,
        rol: document.getElementById('asignacion_rol').value
    };

    if (!payload.usuario_id) {
        alertAsignacion.textContent = 'Debe seleccionar un usuario.';
        alertAsignacion.classList.remove('d-none');
        alertAsignacion.classList.add('alert-danger');
        return;
    }

    try {
        const response = await authFetch('/api/incidencias/{{ $incidencia->id }}/asignaciones', {
            method: 'POST',
            body: JSON.stringify(payload)
        });

        const data = await response.json();

        if (!response.ok) {
            alertAsignacion.textContent = data.message || 'No se pudo asignar el usuario';
            alertAsignacion.classList.remove('d-none');
            alertAsignacion.classList.add('alert-danger');
            return;
        }

        location.reload();

    } catch (error) {
        alertAsignacion.textContent = 'Error de conexión con el servidor';
        alertAsignacion.classList.remove('d-none');
        alertAsignacion.classList.add('alert-danger');
    }
});

document.querySelectorAll('.btn-quitar-asignacion').forEach(btn => {
    btn.addEventListener('click', async function () {
        if (!confirm('¿Quitar a este usuario de la incidencia?')) return;

        const id = this.dataset.id;

        try {
            const response = await authFetch(`/api/asignaciones/${id}`, { method: 'DELETE' });

            if (response.ok) {
                location.reload();
            } else {
                alert('No se pudo quitar la asignación');
            }
        } catch (error) {
            alert('Error de conexión');
        }
    });
});
</script>

@endsection