@extends('layouts.app')

@section('title', 'Coordinador')

@section('styles')
<style>
    .coordinador-header {
        background: linear-gradient(135deg, rgba(10,17,40,0.35) 0%, rgba(22,35,63,0.25) 45%, rgba(201,169,97,0.18) 100%);
        border: 1px solid var(--border-subtle);
        border-radius: 14px;
        padding: 18px 22px;
    }

    .panel-columna {
        background: var(--bg-card, #1a2333);
        border: 1px solid var(--border-subtle);
        border-radius: 14px;
        padding: 16px;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.22);
        height: 100%;
    }

    .panel-columna-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 14px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--border-subtle);
    }

    .panel-columna-header h2 {
        font-size: 1rem;
        font-weight: 700;
        margin: 0;
        color: var(--text-main);
    }

    /* ===== Tarjetas de Responsables ===== */
    .grid-responsables {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
        gap: 14px;
    }

    .tarjeta-responsable {
        background: var(--bg-card-tarjeta, #222f45);
        border: 1px solid var(--border-subtle);
        border-radius: 12px;
        padding: 14px;
        transition: transform 0.15s, box-shadow 0.15s;
    }

    .tarjeta-responsable:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 18px rgba(0, 0, 0, 0.35);
    }

    .tarjeta-responsable .cabecera {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 12px;
    }

    .avatar-responsable {
        width: 42px;
        height: 42px;
        min-width: 42px;
        border-radius: 50%;
        background: var(--brand-gradient);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.9rem;
        box-shadow: 0 2px 6px rgba(0,0,0,0.25);
    }

    .tarjeta-responsable .nombre {
        font-weight: 700;
        font-size: 0.9rem;
        color: var(--text-main);
        line-height: 1.25;
    }

    .tarjeta-responsable .correo {
        font-size: 0.72rem;
        color: var(--text-muted);
    }

    .metricas-responsable {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 6px;
        text-align: center;
        margin-bottom: 10px;
    }

    .metricas-responsable .metrica .valor {
        font-weight: 700;
        font-size: 1.05rem;
        color: var(--text-main);
    }

    .metricas-responsable .metrica.activas .valor { color: #ff9f4d; }
    .metricas-responsable .metrica.resueltas .valor { color: #34d399; }
    .metricas-responsable .metrica.tasa .valor { color: var(--brand-400, #C9A961); }

    .metricas-responsable .metrica .etiqueta {
        font-size: 0.62rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        color: var(--text-muted);
    }

    .actividad-actual {
        display: flex;
        align-items: flex-start;
        gap: 6px;
        font-size: 0.75rem;
        color: var(--text-muted);
        border-top: 1px solid var(--border-subtle);
        padding-top: 10px;
    }

    .actividad-actual i {
        margin-top: 2px;
        color: var(--brand-400, #C9A961);
    }

    .actividad-actual .titulo-tarea {
        color: var(--text-main);
        font-weight: 600;
    }

    /* ===== Lista de incidencias sin asignar ===== */
    .lista-sin-asignar {
        display: flex;
        flex-direction: column;
        gap: 10px;
        max-height: 720px;
        overflow-y: auto;
        padding-right: 4px;
    }

    .fila-incidencia {
        background: var(--bg-card-tarjeta, #222f45);
        border: 1px solid var(--border-subtle);
        border-left: 4px solid var(--color-prioridad, #6c757d);
        border-radius: 10px;
        padding: 12px 14px;
    }

    .fila-incidencia .fila-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 6px;
    }

    .fila-incidencia .fila-id {
        font-size: 0.68rem;
        color: var(--text-muted);
        font-weight: 700;
    }

    .fila-incidencia .fila-titulo {
        font-weight: 600;
        font-size: 0.88rem;
        color: var(--text-main);
        margin-bottom: 6px;
    }

    .fila-incidencia .fila-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        font-size: 0.74rem;
        color: var(--text-muted);
        margin-bottom: 10px;
    }

    .prioridad-pill {
        border-radius: 20px;
        padding: 2px 10px;
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: 0.3px;
    }

    .prioridad-Crítica { background: rgba(220,53,69,0.18); color: #ff6b7d; border: 1px solid rgba(220,53,69,0.4); }
    .prioridad-Alta    { background: rgba(253,126,20,0.18); color: #ff9f4d; border: 1px solid rgba(253,126,20,0.4); }
    .prioridad-Media   { background: rgba(255,193,7,0.18); color: #ffda6a; border: 1px solid rgba(255,193,7,0.4); }
    .prioridad-Baja    { background: rgba(148,163,184,0.18); color: #cbd5e1; border: 1px solid rgba(148,163,184,0.4); }

    .lista-vacia, .grid-vacia {
        text-align: center;
        color: var(--text-muted);
        padding: 40px 0;
        border: 1px dashed var(--border-subtle);
        border-radius: 10px;
    }

    .lista-vacia i, .grid-vacia i {
        display: block;
        font-size: 1.4rem;
        margin-bottom: 8px;
        opacity: 0.5;
    }

    @media (max-width: 991px) {
        .col-responsables, .col-sin-asignar {
            margin-bottom: 20px;
        }
    }
</style>
@endsection

@section('content')

<div class="container-fluid">

    <div class="coordinador-header d-flex justify-content-between align-items-center flex-wrap mb-4">
        <div>
            <h1 class="mb-1"><i class="fas fa-user-shield mr-2"></i>Coordinador</h1>
            <span style="color:var(--text-muted) !important;">
                Carga de trabajo de los Responsables e incidencias pendientes de asignar
            </span>
        </div>
        <button type="button" class="btn btn-outline-light btn-sm mt-2 mt-md-0" id="btnRefrescar">
            <i class="fas fa-sync-alt mr-1"></i>Actualizar
        </button>
    </div>

    <div id="alertCoordinador" class="alert d-none"></div>

    <div class="row">

        <!-- ===== Columna izquierda: Responsables ===== -->
        <div class="col-lg-7 col-responsables">
            <div class="panel-columna">
                <div class="panel-columna-header">
                    <h2><i class="fas fa-users mr-2"></i>Responsables</h2>
                    <span class="badge badge-secondary" id="contadorResponsables">0</span>
                </div>

                <div class="grid-responsables" id="gridResponsables">
                    <div class="text-center w-100 py-5">
                        <i class="fas fa-spinner fa-spin mr-2"></i>Cargando responsables...
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== Columna derecha: Incidencias sin asignar ===== -->
        <div class="col-lg-5 col-sin-asignar">
            <div class="panel-columna">
                <div class="panel-columna-header flex-wrap">
                    <h2><i class="fas fa-inbox mr-2"></i>Sin asignar</h2>
                    <div class="d-flex align-items-center" style="gap:8px;">
                        <span class="badge badge-danger" id="contadorSinAsignar">0</span>
                        <select id="ordenSinAsignar" class="form-control form-control-sm" style="width:auto;">
                            <option value="fecha">Más antiguas primero</option>
                            <option value="prioridad">Por prioridad</option>
                        </select>
                    </div>
                </div>

                <div class="lista-sin-asignar" id="listaSinAsignar">
                    <div class="text-center w-100 py-5">
                        <i class="fas fa-spinner fa-spin mr-2"></i>Cargando incidencias...
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal: asignar Responsable a una incidencia -->
<div class="modal fade" id="modalAsignar" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-plus mr-2"></i>Asignar incidencia</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p class="mb-1"><strong id="modalIncidenciaTitulo"></strong></p>
                <p class="text-muted mb-3" id="modalIncidenciaSub" style="font-size:0.82rem;"></p>

                <div class="form-group">
                    <label>Responsable</label>
                    <select class="form-control" id="modalUsuarioId" required>
                        <option value="">Seleccione un usuario...</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Rol de la asignación</label>
                    <select class="form-control" id="modalRol">
                        <option value="Responsable">Responsable</option>
                        <option value="Apoyo">Apoyo</option>
                    </select>
                </div>

                <div id="alertModalAsignar" class="alert d-none"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnConfirmarAsignar">
                    <i class="fas fa-check mr-1"></i>Asignar
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>

requireRole(["Administrador"]);

let incidenciaSeleccionada = null;
let responsablesCache = [];

function escaparHtml(texto) {
    const div = document.createElement('div');
    div.textContent = texto ?? '';
    return div.innerHTML;
}

function iniciales(nombre, apellido) {
    const n = (nombre || '').trim().charAt(0);
    const a = (apellido || '').trim().charAt(0);
    return (n + a).toUpperCase() || '?';
}

function mostrarAlerta(id, mensaje, tipo = 'danger') {
    const alerta = document.getElementById(id);
    alerta.textContent = mensaje;
    alerta.className = `alert alert-${tipo}`;
}

function ocultarAlerta(id) {
    document.getElementById(id).className = 'alert d-none';
}

/* ===================== Responsables ===================== */

async function cargarResponsables() {

    const grid = document.getElementById('gridResponsables');

    try {
        const res = await authFetch('/api/coordinador/responsables');

        if (!res.ok) {
            grid.innerHTML = '<div class="grid-vacia"><i class="fas fa-exclamation-triangle"></i>No se pudo cargar la lista de responsables.</div>';
            return;
        }

        const responsables = await res.json();
        responsablesCache = responsables;

        document.getElementById('contadorResponsables').textContent = responsables.length;

        if (responsables.length === 0) {
            grid.innerHTML = '<div class="grid-vacia"><i class="fas fa-user-slash"></i>No hay usuarios con rol Responsable activos.</div>';
            return;
        }

        grid.innerHTML = '';

        responsables.forEach(r => {

            const nombreCompleto = `${r.name} ${r.apellido || ''}`.trim();

            const actividadHtml = r.actividad_actual
                ? `<i class="fas fa-spinner"></i><span>En proceso: <span class="titulo-tarea">${escaparHtml(r.actividad_actual.titulo)}</span></span>`
                : `<i class="fas fa-moon"></i><span>Sin actividad en curso</span>`;

            const tarjeta = document.createElement('div');
            tarjeta.className = 'tarjeta-responsable';
            tarjeta.innerHTML = `
                <div class="cabecera">
                    <div class="avatar-responsable">${iniciales(r.name, r.apellido)}</div>
                    <div>
                        <div class="nombre">${escaparHtml(nombreCompleto)}</div>
                        <div class="correo">${escaparHtml(r.email)}</div>
                    </div>
                </div>
                <div class="metricas-responsable">
                    <div class="metrica activas">
                        <div class="valor">${r.metricas.activas}</div>
                        <div class="etiqueta">Activas</div>
                    </div>
                    <div class="metrica resueltas">
                        <div class="valor">${r.metricas.resueltas}</div>
                        <div class="etiqueta">Resueltas</div>
                    </div>
                    <div class="metrica tasa">
                        <div class="valor">${r.metricas.tasa_resolucion}%</div>
                        <div class="etiqueta">Tasa</div>
                    </div>
                </div>
                <div class="actividad-actual">${actividadHtml}</div>
            `;

            grid.appendChild(tarjeta);
        });

    } catch (error) {
        grid.innerHTML = '<div class="grid-vacia"><i class="fas fa-plug"></i>Error de conexión con el servidor.</div>';
    }
}

/* ===================== Incidencias sin asignar ===================== */

async function cargarSinAsignar() {

    const lista = document.getElementById('listaSinAsignar');
    const orden = document.getElementById('ordenSinAsignar').value;

    try {
        const res = await authFetch(`/api/coordinador/sin-asignar?orden=${orden}`);

        if (!res.ok) {
            lista.innerHTML = '<div class="lista-vacia"><i class="fas fa-exclamation-triangle"></i>No se pudo cargar la lista.</div>';
            return;
        }

        const incidencias = await res.json();

        document.getElementById('contadorSinAsignar').textContent = incidencias.length;

        if (incidencias.length === 0) {
            lista.innerHTML = '<div class="lista-vacia"><i class="fas fa-check-circle"></i>No hay incidencias pendientes de asignar.</div>';
            return;
        }

        lista.innerHTML = '';

        incidencias.forEach(inc => {

            const fila = document.createElement('div');
            fila.className = 'fila-incidencia';
            fila.style.setProperty('--color-prioridad',
                {'Crítica': '#dc3545', 'Alta': '#fd7e14', 'Media': '#ffc107', 'Baja': '#94a3b8'}[inc.prioridad] || '#6c757d'
            );

            const reportante = inc.usuario ? `${inc.usuario.name} ${inc.usuario.apellido || ''}`.trim() : 'N/D';

            fila.innerHTML = `
                <div class="fila-top">
                    <span class="fila-id">#${inc.id}</span>
                    <span class="prioridad-pill prioridad-${inc.prioridad}">${inc.prioridad}</span>
                </div>
                <div class="fila-titulo">${escaparHtml(inc.titulo)}</div>
                <div class="fila-meta">
                    <span><i class="fas fa-user mr-1"></i>${escaparHtml(reportante)}</span>
                    <span><i class="fas fa-map-marker-alt mr-1"></i>${escaparHtml(inc.ciudad ? inc.ciudad.nombre : 'N/D')}</span>
                    <span><i class="fas fa-tag mr-1"></i>${escaparHtml(inc.tipo ? inc.tipo.nombre : 'N/D')}</span>
                </div>
                <button type="button" class="btn btn-sm btn-primary btn-block btn-asignar">
                    <i class="fas fa-user-plus mr-1"></i>Asignar
                </button>
            `;

            fila.querySelector('.btn-asignar').addEventListener('click', () => abrirModalAsignar(inc));

            lista.appendChild(fila);
        });

    } catch (error) {
        lista.innerHTML = '<div class="lista-vacia"><i class="fas fa-plug"></i>Error de conexión con el servidor.</div>';
    }
}

/* ===================== Modal de asignación ===================== */

function abrirModalAsignar(incidencia) {

    incidenciaSeleccionada = incidencia;

    document.getElementById('modalIncidenciaTitulo').textContent = `#${incidencia.id} · ${incidencia.titulo}`;
    document.getElementById('modalIncidenciaSub').textContent =
        `${incidencia.tipo ? incidencia.tipo.nombre : ''} · ${incidencia.ciudad ? incidencia.ciudad.nombre : ''} · Prioridad ${incidencia.prioridad}`;

    const select = document.getElementById('modalUsuarioId');
    select.innerHTML = '<option value="">Seleccione un usuario...</option>';

    responsablesCache.forEach(r => {
        const option = document.createElement('option');
        option.value = r.id;
        option.textContent = `${r.name} ${r.apellido || ''}`.trim();
        select.appendChild(option);
    });

    document.getElementById('modalRol').value = 'Responsable';
    ocultarAlerta('alertModalAsignar');

    $('#modalAsignar').modal('show');
}

document.getElementById('btnConfirmarAsignar').addEventListener('click', async () => {

    ocultarAlerta('alertModalAsignar');

    const usuarioId = document.getElementById('modalUsuarioId').value;
    const rol = document.getElementById('modalRol').value;

    if (!usuarioId) {
        mostrarAlerta('alertModalAsignar', 'Debe seleccionar un usuario.');
        return;
    }

    const boton = document.getElementById('btnConfirmarAsignar');
    boton.disabled = true;

    try {
        const res = await authFetch(`/api/incidencias/${incidenciaSeleccionada.id}/asignaciones`, {
            method: 'POST',
            body: JSON.stringify({ usuario_id: usuarioId, rol: rol })
        });

        const data = await res.json();

        if (!res.ok) {
            mostrarAlerta('alertModalAsignar', data.message || 'No se pudo asignar la incidencia.');
            return;
        }

        $('#modalAsignar').modal('hide');
        await Promise.all([cargarSinAsignar(), cargarResponsables()]);

    } catch (error) {
        mostrarAlerta('alertModalAsignar', 'Error de conexión con el servidor.');
    } finally {
        boton.disabled = false;
    }
});

document.getElementById('ordenSinAsignar').addEventListener('change', cargarSinAsignar);

document.getElementById('btnRefrescar').addEventListener('click', () => {
    cargarResponsables();
    cargarSinAsignar();
});

cargarResponsables();
cargarSinAsignar();

</script>
@endsection
