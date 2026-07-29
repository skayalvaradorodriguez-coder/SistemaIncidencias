@extends('layouts.app')

@section('title', 'Mis Tareas')

@section('styles')
<style>
    .tareas-header {
        background: linear-gradient(135deg, rgba(10,17,40,0.35) 0%, rgba(22,35,63,0.25) 45%, rgba(201,169,97,0.18) 100%);
        border: 1px solid var(--border-subtle);
        border-radius: 14px;
        padding: 18px 22px;
    }

    .resumen-tareas {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 12px;
        margin: 18px 0 22px;
    }

    .resumen-caja {
        background: var(--bg-card, #1a2333);
        border: 1px solid var(--border-subtle);
        border-radius: 12px;
        padding: 14px 16px;
        text-align: center;
    }

    .resumen-caja .valor {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-main);
    }

    .resumen-caja .etiqueta {
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        color: var(--text-muted);
    }

    .resumen-caja.pendientes .valor { color: #ffda6a; }
    .resumen-caja.en-proceso .valor { color: #4dabf7; }
    .resumen-caja.resueltas .valor { color: #34d399; }

    .filtros-tareas {
        display: flex;
        gap: 10px;
        align-items: center;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }

    .grid-tareas {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 14px;
    }

    .tarjeta-tarea {
        background: var(--bg-card-tarjeta, #222f45);
        border: 1px solid var(--border-subtle);
        border-left: 4px solid var(--color-prioridad, #6c757d);
        border-radius: 12px;
        padding: 14px 16px;
        cursor: pointer;
        transition: transform 0.15s, box-shadow 0.15s;
    }

    .tarjeta-tarea:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 18px rgba(0, 0, 0, 0.35);
    }

    .tarjeta-tarea .fila-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .tarjeta-tarea .tarea-id {
        font-size: 0.68rem;
        font-weight: 700;
        color: var(--text-muted);
    }

    .tarjeta-tarea .tarea-titulo {
        font-weight: 600;
        font-size: 0.92rem;
        color: var(--text-main);
        margin-bottom: 8px;
        line-height: 1.3;
    }

    .tarjeta-tarea .tarea-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        font-size: 0.74rem;
        color: var(--text-muted);
        margin-bottom: 10px;
    }

    .tarjeta-tarea .tarea-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 8px;
        border-top: 1px solid var(--border-subtle);
        font-size: 0.72rem;
        color: var(--text-muted);
    }

    .estado-pill {
        border-radius: 20px;
        padding: 2px 10px;
        font-size: 0.68rem;
        font-weight: 700;
        border: 1px solid;
        color: #fff;
        background: var(--color-estado, #6c757d);
    }

    .rol-asignacion-pill {
        border-radius: 20px;
        padding: 2px 10px;
        font-size: 0.66rem;
        font-weight: 700;
        background: rgba(139,92,246,0.15);
        color: #a78bfa;
        border: 1px solid rgba(139,92,246,0.35);
    }

    .prioridad-pill {
        border-radius: 20px;
        padding: 2px 10px;
        font-size: 0.68rem;
        font-weight: 700;
    }

    .prioridad-Crítica { background: rgba(220,53,69,0.18); color: #ff6b7d; border: 1px solid rgba(220,53,69,0.4); }
    .prioridad-Alta    { background: rgba(253,126,20,0.18); color: #ff9f4d; border: 1px solid rgba(253,126,20,0.4); }
    .prioridad-Media   { background: rgba(255,193,7,0.18); color: #ffda6a; border: 1px solid rgba(255,193,7,0.4); }
    .prioridad-Baja    { background: rgba(148,163,184,0.18); color: #cbd5e1; border: 1px solid rgba(148,163,184,0.4); }

    .grid-vacia {
        text-align: center;
        color: var(--text-muted);
        padding: 50px 0;
        border: 1px dashed var(--border-subtle);
        border-radius: 10px;
    }

    .grid-vacia i {
        display: block;
        font-size: 1.6rem;
        margin-bottom: 8px;
        opacity: 0.5;
    }

    .tarea-evidencia {
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px dashed var(--border-subtle);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .btn-subir-evidencia {
        font-size: 0.75rem;
        padding: 3px 10px;
    }

    .contador-evidencias {
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    .contador-evidencias i {
        color: #34d399;
    }
</style>
@endsection

@section('content')

<div class="container-fluid">

    <div class="tareas-header d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1 class="mb-1"><i class="fas fa-clipboard-check mr-2"></i>Mis Tareas</h1>
            <span style="color:var(--text-muted) !important;">
                Incidencias que el Administrador te ha asignado
            </span>
        </div>
        <button type="button" class="btn btn-outline-light btn-sm mt-2 mt-md-0" id="btnRefrescar">
            <i class="fas fa-sync-alt mr-1"></i>Actualizar
        </button>
    </div>

    <div class="resumen-tareas">
        <div class="resumen-caja pendientes">
            <div class="valor" id="resumenPendientes">0</div>
            <div class="etiqueta">Pendientes</div>
        </div>
        <div class="resumen-caja en-proceso">
            <div class="valor" id="resumenEnProceso">0</div>
            <div class="etiqueta">En Proceso</div>
        </div>
        <div class="resumen-caja resueltas">
            <div class="valor" id="resumenResueltas">0</div>
            <div class="etiqueta">Resueltas</div>
        </div>
        <div class="resumen-caja">
            <div class="valor" id="resumenTotal">0</div>
            <div class="etiqueta">Total asignadas</div>
        </div>
    </div>

    <div class="filtros-tareas">
        <label class="mb-0 mr-1" style="font-size:0.85rem; color:var(--text-muted);">Filtrar por estado:</label>
        <select id="filtroEstado" class="form-control form-control-sm" style="width:auto;">
            <option value="">Todas</option>
        </select>
    </div>

    <div id="alertTareas" class="alert d-none"></div>

    <div class="grid-tareas" id="gridTareas">
        <div class="text-center w-100 py-5">
            <i class="fas fa-spinner fa-spin mr-2"></i>Cargando tus tareas...
        </div>
    </div>

</div>

<!-- Modal: Subir evidencia -->
<div class="modal fade" id="modalEvidencia" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-camera mr-2"></i>Subir evidencia</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="formEvidencia">
                <div class="modal-body">

                    <div id="alertEvidencia" class="alert d-none"></div>

                    <div class="form-group">
                        <label>Foto de la incidencia en proceso / resuelta</label>
                        <input type="file" id="evidencia_foto" class="form-control-file" accept="image/png,image/jpeg,image/webp" required>
                        <small class="form-text text-muted">JPG, PNG o WEBP. Máximo 4MB.</small>
                    </div>

                    <div class="form-group">
                        <label>Comentario (opcional)</label>
                        <textarea id="evidencia_comentario" class="form-control" rows="2" placeholder="Ej: Cambié el foco del poste, quedó funcionando."></textarea>
                    </div>

                    <div id="galeriaEvidencias"></div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload mr-1"></i>Subir evidencia
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>

requireRole(["Responsable"]);

const COLORES_PRIORIDAD = {'Crítica': '#dc3545', 'Alta': '#fd7e14', 'Media': '#ffc107', 'Baja': '#94a3b8'};

// Mismo mapa nombre-de-color -> hex que usa el tablero Kanban
const COLORES_ESTADO = {
    'warning': '#ffc107',
    'primary': '#007bff',
    'success': '#28a745',
    'danger': '#dc3545',
    'secondary': '#6c757d',
    'info': '#17a2b8'
};

function escaparHtml(texto) {
    const div = document.createElement('div');
    div.textContent = texto ?? '';
    return div.innerHTML;
}

function colorEstado(estado) {
    return COLORES_ESTADO[estado ? estado.color : ''] || '#6c757d';
}

async function cargarEstadosFiltro() {

    try {
        const res = await authFetch('/api/estados');
        if (!res.ok) return;

        const estados = await res.json();
        const select = document.getElementById('filtroEstado');

        estados.forEach(estado => {
            const option = document.createElement('option');
            option.value = estado.id;
            option.textContent = estado.nombre;
            select.appendChild(option);
        });

    } catch (error) {
        // El filtro simplemente queda con "Todas" si falla
    }
}

async function cargarMisTareas() {

    const grid = document.getElementById('gridTareas');
    const estadoId = document.getElementById('filtroEstado').value;
    const alerta = document.getElementById('alertTareas');
    alerta.className = 'alert d-none';

    try {
        const url = estadoId ? `/api/mis-tareas?estado_id=${estadoId}` : '/api/mis-tareas';
        const res = await authFetch(url);

        if (!res.ok) {
            grid.innerHTML = '<div class="grid-vacia"><i class="fas fa-exclamation-triangle"></i>No se pudieron cargar tus tareas.</div>';
            return;
        }

        const asignaciones = await res.json();

        actualizarResumen(asignaciones);

        if (asignaciones.length === 0) {
            grid.innerHTML = '<div class="grid-vacia"><i class="fas fa-mug-hot"></i>No tienes incidencias asignadas por el momento.</div>';
            return;
        }

        grid.innerHTML = '';

        asignaciones.forEach(asig => {

            const inc = asig.incidencia;
            if (!inc) return;

            const tarjeta = document.createElement('div');
            tarjeta.className = 'tarjeta-tarea';
            tarjeta.style.setProperty('--color-prioridad', COLORES_PRIORIDAD[inc.prioridad] || '#6c757d');

            const puedeSubirEvidencia = inc.estado && inc.estado.nombre === 'En Proceso';
            const totalEvidencias = (inc.evidencias || []).length;

            tarjeta.innerHTML = `
                <div class="fila-top">
                    <span class="tarea-id">#${inc.id}</span>
                    <span class="prioridad-pill prioridad-${inc.prioridad}">${inc.prioridad}</span>
                </div>
                <div class="tarea-titulo">${escaparHtml(inc.titulo)}</div>
                <div class="tarea-meta">
                    <span><i class="fas fa-tag mr-1"></i>${escaparHtml(inc.tipo ? inc.tipo.nombre : 'N/D')}</span>
                    <span><i class="fas fa-map-marker-alt mr-1"></i>${escaparHtml(inc.ciudad ? inc.ciudad.nombre : 'N/D')}</span>
                    <span><i class="fas fa-user mr-1"></i>${escaparHtml(inc.usuario ? inc.usuario.name : 'N/D')}</span>
                </div>
                <div class="tarea-footer">
                    <span class="estado-pill" style="--color-estado:${colorEstado(inc.estado)}; border-color:${colorEstado(inc.estado)};">${escaparHtml(inc.estado ? inc.estado.nombre : 'Sin estado')}</span>
                    <span class="rol-asignacion-pill">${escaparHtml(asig.rol)}</span>
                </div>
                <div class="tarea-evidencia">
                    <span class="contador-evidencias">
                        ${totalEvidencias > 0 ? `<i class="fas fa-check-circle mr-1"></i>${totalEvidencias} evidencia(s)` : 'Sin evidencia todavía'}
                    </span>
                    ${puedeSubirEvidencia
                        ? `<button type="button" class="btn btn-outline-light btn-subir-evidencia" data-incidencia-id="${inc.id}">
                               <i class="fas fa-camera mr-1"></i>Subir evidencia
                           </button>`
                        : ''}
                </div>
            `;

            tarjeta.addEventListener('click', (e) => {
                if (e.target.closest('.btn-subir-evidencia')) return;
                window.location.href = '/incidencias/' + inc.id;
            });

            const btnEvidencia = tarjeta.querySelector('.btn-subir-evidencia');
            if (btnEvidencia) {
                btnEvidencia.addEventListener('click', (e) => {
                    e.stopPropagation();
                    abrirModalEvidencia(inc.id, inc.evidencias || []);
                });
            }

            grid.appendChild(tarjeta);
        });

    } catch (error) {
        grid.innerHTML = '<div class="grid-vacia"><i class="fas fa-plug"></i>Error de conexión con el servidor.</div>';
    }
}

function actualizarResumen(asignaciones) {

    const incidencias = asignaciones.map(a => a.incidencia).filter(Boolean);

    const pendientes = incidencias.filter(i => (i.estado ? i.estado.nombre : '') === 'Pendiente').length;
    const enProceso = incidencias.filter(i => (i.estado ? i.estado.nombre : '') === 'En Proceso').length;
    const resueltas = incidencias.filter(i => (i.estado ? i.estado.nombre : '') === 'Resuelto').length;

    document.getElementById('resumenPendientes').textContent = pendientes;
    document.getElementById('resumenEnProceso').textContent = enProceso;
    document.getElementById('resumenResueltas').textContent = resueltas;
    document.getElementById('resumenTotal').textContent = incidencias.length;
}

document.getElementById('filtroEstado').addEventListener('change', cargarMisTareas);
document.getElementById('btnRefrescar').addEventListener('click', cargarMisTareas);

// ================== EVIDENCIAS ==================
let incidenciaEvidenciaActual = null;

function pintarGaleriaEvidencias(evidencias) {
    const cont = document.getElementById('galeriaEvidencias');

    if (!evidencias || evidencias.length === 0) {
        cont.innerHTML = '<p class="text-muted mb-0" style="font-size:0.85rem;">Todavía no hay evidencia subida para esta incidencia.</p>';
        return;
    }

    cont.innerHTML = '<label class="d-block mb-2">Evidencia ya subida</label>' +
        '<div class="d-flex flex-wrap" style="gap:8px;">' +
        evidencias.map(ev => `
            <a href="/storage/${ev.ruta_foto}" target="_blank" title="${escaparHtml(ev.comentario || '')}">
                <img src="/storage/${ev.ruta_foto}" style="width:70px; height:70px; object-fit:cover; border-radius:6px; border:1px solid var(--border-subtle);">
            </a>
        `).join('') +
        '</div>';
}

function abrirModalEvidencia(incidenciaId, evidenciasExistentes) {
    incidenciaEvidenciaActual = incidenciaId;

    document.getElementById('formEvidencia').reset();
    document.getElementById('alertEvidencia').className = 'alert d-none';
    pintarGaleriaEvidencias(evidenciasExistentes);

    $('#modalEvidencia').modal('show');
}

document.getElementById('formEvidencia').addEventListener('submit', async function (e) {
    e.preventDefault();

    const alerta = document.getElementById('alertEvidencia');
    alerta.className = 'alert d-none';

    const archivo = document.getElementById('evidencia_foto').files[0];
    if (!archivo) {
        alerta.textContent = 'Debe seleccionar una foto.';
        alerta.classList.remove('d-none');
        alerta.classList.add('alert-danger');
        return;
    }

    const formData = new FormData();
    formData.append('foto', archivo);
    formData.append('comentario', document.getElementById('evidencia_comentario').value.trim());

    const btnSubmit = this.querySelector('button[type="submit"]');
    btnSubmit.disabled = true;
    btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Subiendo...';

    try {
        const response = await authFetch(`/api/incidencias/${incidenciaEvidenciaActual}/evidencias`, {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (!response.ok) {
            alerta.textContent = data.message || 'No se pudo subir la evidencia.';
            alerta.classList.remove('d-none');
            alerta.classList.add('alert-danger');
            return;
        }

        $('#modalEvidencia').modal('hide');
        cargarMisTareas();

    } catch (error) {
        alerta.textContent = 'Error de conexión con el servidor.';
        alerta.classList.remove('d-none');
        alerta.classList.add('alert-danger');
    } finally {
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = '<i class="fas fa-upload mr-1"></i>Subir evidencia';
    }
});

cargarEstadosFiltro();
cargarMisTareas();

</script>
@endsection