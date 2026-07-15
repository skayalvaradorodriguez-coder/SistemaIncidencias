@extends('layouts.app')

@section('title', 'Detalle de Incidencia')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    #mapa { height: 320px; border-radius: 4px; z-index: 1; }

    /* ===== Chat de seguimiento ===== */
    #chatVentana {
        height: 380px;
        overflow-y: auto;
        padding: 15px;
        background: rgba(0, 0, 0, 0.15);
        border-radius: 6px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .chat-burbuja {
        max-width: 75%;
        padding: 8px 14px;
        border-radius: 14px;
        font-size: 0.9rem;
        line-height: 1.35;
        word-wrap: break-word;
    }

    .chat-propio {
        align-self: flex-end;
        background: #007bff;
        color: #fff;
        border-bottom-right-radius: 4px;
    }

    .chat-ajeno {
        align-self: flex-start;
        background: #3f474e;
        color: #e9ecef;
        border-bottom-left-radius: 4px;
    }

    .chat-autor {
        font-size: 0.72rem;
        font-weight: 600;
        opacity: 0.85;
        margin-bottom: 2px;
    }

    .chat-hora {
        font-size: 0.68rem;
        opacity: 0.6;
        text-align: right;
        margin-top: 3px;
    }

    .chat-vacio {
        margin: auto;
        text-align: center;
        opacity: 0.5;
    }

    #chatInput {
        resize: none;
    }
</style>
@endsection

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Incidencia #{{ $incidencia->id }}</h1>
        <a href="{{ route('incidencias.index') }}" class="btn btn-secondary">Volver</a>
    </div>

    <div id="alertEstado" class="alert d-none"></div>

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

    <div class="card mt-3" id="cardCambiarEstado">
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

    <!-- Chat de seguimiento -->
    <div class="card mt-3">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-comments mr-2"></i>
                Conversación de seguimiento
            </h3>
            <div class="card-tools">
                <span class="badge badge-info" id="chatContador">0 mensajes</span>
            </div>
        </div>

        <div class="card-body">

            <div id="alertComentario" class="alert d-none"></div>

            <div id="chatVentana">
                <div class="chat-vacio">
                    <i class="far fa-comment-dots d-block mb-2" style="font-size:2rem;"></i>
                    Cargando conversación...
                </div>
            </div>

            <form id="formComentario" class="mt-3">
                <div class="input-group">
                    <textarea
                        id="chatInput"
                        class="form-control"
                        rows="1"
                        placeholder="Escribe un mensaje... (Enter para enviar)"></textarea>
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </form>

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

// ================== CHAT DE SEGUIMIENTO ==================
const INCIDENCIA_ID = {{ $incidencia->id }};
const USUARIO_ACTUAL = getUser();

// Oculta controles de gestión a usuarios con rol Ciudadano
if (typeof esCiudadano === 'function' && esCiudadano()) {
    document.getElementById('cardCambiarEstado')?.remove();
    document.getElementById('formAsignacion')?.remove();
    document.querySelectorAll('.btn-quitar-asignacion').forEach(b => b.remove());
}

// Evita inyección de HTML en los mensajes (protección XSS)
function escapeHtml(texto) {
    const div = document.createElement('div');
    div.textContent = texto;
    return div.innerHTML;
}

function horaMensaje(fecha) {
    const f = new Date(fecha);
    const hoy = new Date();
    const esHoy = f.toDateString() === hoy.toDateString();
    const hora = f.toLocaleTimeString('es-EC', { hour: '2-digit', minute: '2-digit' });
    return esHoy ? hora : f.toLocaleDateString('es-EC', { day: '2-digit', month: 'short' }) + ' ' + hora;
}

let totalMensajesPrevio = 0;

async function cargarChat(forzarScroll = false) {

    try {
        const response = await authFetch(`/api/incidencias/${INCIDENCIA_ID}/comentarios`);

        if (!response.ok) return;

        const comentarios = await response.json();

        if (comentarios.length === totalMensajesPrevio && !forzarScroll) return;

        const ventana = document.getElementById('chatVentana');
        const estabaAbajo = ventana.scrollHeight - ventana.scrollTop - ventana.clientHeight < 60;

        document.getElementById('chatContador').textContent =
            comentarios.length + (comentarios.length === 1 ? ' mensaje' : ' mensajes');

        if (comentarios.length === 0) {
            ventana.innerHTML = `
                <div class="chat-vacio">
                    <i class="far fa-comment-dots d-block mb-2" style="font-size:2rem;"></i>
                    Aún no hay mensajes. ¡Escribe el primero!
                </div>`;
            totalMensajesPrevio = 0;
            return;
        }

        ventana.innerHTML = '';

        comentarios.forEach(c => {
            const esPropio = USUARIO_ACTUAL && c.usuario_id === USUARIO_ACTUAL.id;

            const burbuja = document.createElement('div');
            burbuja.className = 'chat-burbuja ' + (esPropio ? 'chat-propio' : 'chat-ajeno');
            burbuja.innerHTML = `
                ${esPropio ? '' : `<div class="chat-autor">${escapeHtml(c.usuario ? c.usuario.name : 'Usuario')}</div>`}
                <div>${escapeHtml(c.comentario)}</div>
                <div class="chat-hora">${horaMensaje(c.created_at)}</div>
            `;
            ventana.appendChild(burbuja);
        });

        const hayNuevos = comentarios.length > totalMensajesPrevio;
        totalMensajesPrevio = comentarios.length;

        if (forzarScroll || (hayNuevos && estabaAbajo)) {
            ventana.scrollTop = ventana.scrollHeight;
        }

    } catch (error) {
        console.log(error);
    }
}

async function enviarMensaje() {

    const input = document.getElementById('chatInput');
    const alertComentario = document.getElementById('alertComentario');
    alertComentario.className = 'alert d-none';

    const texto = input.value.trim();

    if (!texto) return;

    try {
        const response = await authFetch(`/api/incidencias/${INCIDENCIA_ID}/comentarios`, {
            method: 'POST',
            body: JSON.stringify({ comentario: texto })
        });

        const data = await response.json();

        if (!response.ok) {
            alertComentario.textContent = data.message || 'No se pudo enviar el mensaje';
            alertComentario.classList.remove('d-none');
            alertComentario.classList.add('alert-danger');
            return;
        }

        input.value = '';
        await cargarChat(true);

    } catch (error) {
        alertComentario.textContent = 'Error de conexión con el servidor';
        alertComentario.classList.remove('d-none');
        alertComentario.classList.add('alert-danger');
    }
}

document.getElementById('formComentario').addEventListener('submit', function (e) {
    e.preventDefault();
    enviarMensaje();
});

// Enter envía, Shift+Enter hace salto de línea
document.getElementById('chatInput').addEventListener('keydown', function (e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        enviarMensaje();
    }
});

cargarChat(true);
setInterval(cargarChat, 10000);

// ================== CAMBIO DE ESTADO ==================
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

// ================== ASIGNACIONES ==================
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