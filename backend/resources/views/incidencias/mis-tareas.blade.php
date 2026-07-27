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
            `;

            tarjeta.addEventListener('click', () => {
                window.location.href = '/incidencias/' + inc.id;
            });

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

cargarEstadosFiltro();
cargarMisTareas();

</script>
@endsection
