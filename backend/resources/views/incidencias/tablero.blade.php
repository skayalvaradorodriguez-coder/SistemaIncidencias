@extends('layouts.app')

@section('title', 'Tablero Kanban')

@section('styles')
<style>
    .tablero {
        display: flex;
        gap: 16px;
        align-items: flex-start;
        overflow-x: auto;
        padding-bottom: 12px;
    }

    .columna {
        flex: 1;
        min-width: 260px;
        background: rgba(0, 0, 0, 0.15);
        border-radius: 8px;
        padding: 12px;
    }

    .columna-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 700;
        font-size: 0.95rem;
        padding: 6px 8px 12px;
        border-bottom: 3px solid var(--color-columna, #6c757d);
        margin-bottom: 12px;
    }

    .columna-contador {
        background: var(--color-columna, #6c757d);
        color: #fff;
        border-radius: 12px;
        padding: 1px 10px;
        font-size: 0.78rem;
    }

    .columna.arrastre-encima {
        outline: 2px dashed var(--color-columna, #6c757d);
        outline-offset: -6px;
    }

    .tarjeta {
        position: relative;
        background: #343a40;
        border-radius: 6px;
        border-left: 4px solid var(--color-columna, #6c757d);
        padding: 10px 12px;
        margin-bottom: 10px;
        cursor: grab;
        transition: transform 0.15s, box-shadow 0.15s;
    }

    .tarjeta:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.35);
    }

    .tarjeta.arrastrando {
        opacity: 0.5;
    }

    .tarjeta-titulo {
        font-weight: 600;
        font-size: 0.88rem;
        margin-bottom: 6px;
        padding-right: 28px;
    }

    .tarjeta-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.75rem;
        opacity: 0.8;
    }

    .btn-mover {
        position: absolute;
        top: 8px;
        right: 8px;
        background: rgba(255, 255, 255, 0.12);
        border: none;
        color: #fff;
        border-radius: 50%;
        width: 26px;
        height: 26px;
        font-size: 0.72rem;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .btn-mover:hover {
        background: rgba(255, 255, 255, 0.25);
    }

    .prioridad-pill {
        border-radius: 10px;
        padding: 1px 8px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .prioridad-Crítica { background: #dc3545; color: #fff; }
    .prioridad-Alta    { background: #fd7e14; color: #fff; }
    .prioridad-Media   { background: #ffc107; color: #212529; }
    .prioridad-Baja    { background: #6c757d; color: #fff; }

    .columna-vacia {
        text-align: center;
        opacity: 0.4;
        font-size: 0.8rem;
        padding: 20px 0;
    }

    .ayuda-escritorio { display: inline; }
    .ayuda-movil { display: none; }

    /* ===== Selector de estado (alternativa táctil) ===== */
    #capaEstado {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.6);
        display: none;
        align-items: flex-end;
        justify-content: center;
        z-index: 2000;
    }

    #capaEstado.visible {
        display: flex;
    }

    .panel-estado {
        background: #2b3035;
        width: 100%;
        max-width: 460px;
        border-radius: 14px 14px 0 0;
        padding: 18px 18px 24px;
        box-shadow: 0 -6px 24px rgba(0, 0, 0, 0.5);
    }

    .panel-estado h5 {
        font-size: 0.95rem;
        margin-bottom: 4px;
    }

    .panel-estado .panel-sub {
        font-size: 0.78rem;
        opacity: 0.6;
        margin-bottom: 14px;
    }

    .opcion-estado {
        display: flex;
        align-items: center;
        gap: 10px;
        width: 100%;
        background: rgba(255, 255, 255, 0.06);
        border: none;
        border-left: 4px solid var(--color-opcion, #6c757d);
        color: #fff;
        text-align: left;
        padding: 12px 14px;
        border-radius: 6px;
        margin-bottom: 8px;
        font-size: 0.9rem;
    }

    .opcion-estado:disabled {
        opacity: 0.4;
    }

    @media (max-width: 768px) {
        .tablero {
            flex-direction: column;
            overflow-x: visible;
        }

        .columna {
            width: 100%;
            min-width: 0;
        }

        .columna-cuerpo {
            max-height: 340px;
            overflow-y: auto;
        }

        .tarjeta {
            cursor: default;
        }

        .ayuda-escritorio { display: none; }
        .ayuda-movil { display: inline; }
    }
</style>
@endsection

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
        <h1>Tablero Kanban</h1>
        <span class="text-muted">
            <i class="fas fa-hand-paper mr-1"></i>
            <span class="ayuda-escritorio">Arrastra una tarjeta a otra columna para cambiar su estado</span>
            <span class="ayuda-movil">Toca el botón <i class="fas fa-exchange-alt"></i> de una tarjeta para cambiar su estado</span>
        </span>
    </div>

    <div id="alertTablero" class="alert d-none"></div>

    <div class="tablero" id="tablero">
        <div class="text-center w-100 py-5">
            <i class="fas fa-spinner fa-spin mr-2"></i>Cargando tablero...
        </div>
    </div>

</div>

<div id="capaEstado">
    <div class="panel-estado">
        <h5 id="panelTitulo">Cambiar estado</h5>
        <div class="panel-sub" id="panelSub"></div>
        <div id="panelOpciones"></div>
        <button type="button" class="btn btn-secondary btn-block mt-2" id="btnCerrarPanel">Cancelar</button>
    </div>
</div>

@endsection

@section('scripts')
<script>
const COLORES = {
    'warning': '#ffc107',
    'primary': '#007bff',
    'success': '#28a745',
    'danger': '#dc3545',
    'secondary': '#6c757d',
    'info': '#17a2b8'
};

let incidenciaArrastrada = null;
let estadosDisponibles = [];

function escaparHtml(texto) {
    const div = document.createElement('div');
    div.textContent = texto ?? '';
    return div.innerHTML;
}

function abrirPanelEstado(incidencia) {

    const capa = document.getElementById('capaEstado');
    const opciones = document.getElementById('panelOpciones');

    document.getElementById('panelSub').textContent =
        '#' + incidencia.id + ' · ' + incidencia.titulo;

    opciones.innerHTML = '';

    estadosDisponibles.forEach(estado => {

        const color = COLORES[estado.color] || '#6c757d';
        const esActual = estado.id === incidencia.estado_incidencia_id;

        const boton = document.createElement('button');
        boton.type = 'button';
        boton.className = 'opcion-estado';
        boton.style.setProperty('--color-opcion', color);
        boton.disabled = esActual;
        boton.innerHTML = `
            <i class="fas fa-flag" style="color:${color};"></i>
            <span>${escaparHtml(estado.nombre)}</span>
            ${esActual ? '<span class="ml-auto" style="font-size:0.72rem; opacity:0.7;">Estado actual</span>' : ''}
        `;

        boton.addEventListener('click', async function () {
            cerrarPanelEstado();
            await moverIncidencia(incidencia.id, estado.id, estado.nombre);
        });

        opciones.appendChild(boton);
    });

    capa.classList.add('visible');
}

function cerrarPanelEstado() {
    document.getElementById('capaEstado').classList.remove('visible');
}

document.getElementById('btnCerrarPanel').addEventListener('click', cerrarPanelEstado);

document.getElementById('capaEstado').addEventListener('click', function (e) {
    if (e.target === this) cerrarPanelEstado();
});

async function cargarTablero() {

    const tablero = document.getElementById('tablero');

    try {
        const [resEstados, resIncidencias] = await Promise.all([
            authFetch('/api/estados'),
            authFetch('/api/incidencias')
        ]);

        if (!resEstados.ok || !resIncidencias.ok) {
            tablero.innerHTML = '<div class="text-danger">Error al cargar el tablero.</div>';
            return;
        }

        const estados = await resEstados.json();
        const incidencias = await resIncidencias.json();

        estadosDisponibles = estados;

        tablero.innerHTML = '';

        estados.forEach(estado => {

            const color = COLORES[estado.color] || '#6c757d';
            const propias = incidencias.filter(i => i.estado_incidencia_id === estado.id);

            const columna = document.createElement('div');
            columna.className = 'columna';
            columna.style.setProperty('--color-columna', color);
            columna.dataset.estadoId = estado.id;
            columna.dataset.estadoNombre = estado.nombre;

            columna.innerHTML = `
                <div class="columna-header">
                    <span>${escaparHtml(estado.nombre)}</span>
                    <span class="columna-contador">${propias.length}</span>
                </div>
                <div class="columna-cuerpo"></div>
            `;

            const cuerpo = columna.querySelector('.columna-cuerpo');

            if (propias.length === 0) {
                cuerpo.innerHTML = '<div class="columna-vacia">Sin incidencias</div>';
            }

            propias.forEach(inc => {

                const tarjeta = document.createElement('div');
                tarjeta.className = 'tarjeta';
                tarjeta.draggable = true;
                tarjeta.dataset.id = inc.id;

                tarjeta.innerHTML = `
                    <button type="button" class="btn-mover" title="Cambiar estado">
                        <i class="fas fa-exchange-alt"></i>
                    </button>
                    <div class="tarjeta-titulo">#${inc.id} · ${escaparHtml(inc.titulo)}</div>
                    <div class="tarjeta-meta">
                        <span><i class="fas fa-map-marker-alt mr-1"></i>${escaparHtml(inc.ciudad ? inc.ciudad.nombre : 'N/A')}</span>
                        <span class="prioridad-pill prioridad-${inc.prioridad}">${inc.prioridad}</span>
                    </div>
                `;

                tarjeta.querySelector('.btn-mover').addEventListener('click', function (e) {
                    e.stopPropagation();
                    abrirPanelEstado(inc);
                });

                tarjeta.addEventListener('dragstart', () => {
                    incidenciaArrastrada = inc.id;
                    tarjeta.classList.add('arrastrando');
                });

                tarjeta.addEventListener('dragend', () => {
                    tarjeta.classList.remove('arrastrando');
                });

                tarjeta.addEventListener('dblclick', () => {
                    window.location.href = '/incidencias/' + inc.id;
                });

                cuerpo.appendChild(tarjeta);
            });

            columna.addEventListener('dragover', e => {
                e.preventDefault();
                columna.classList.add('arrastre-encima');
            });

            columna.addEventListener('dragleave', () => {
                columna.classList.remove('arrastre-encima');
            });

            columna.addEventListener('drop', async e => {
                e.preventDefault();
                columna.classList.remove('arrastre-encima');

                if (!incidenciaArrastrada) return;

                await moverIncidencia(incidenciaArrastrada, columna.dataset.estadoId, columna.dataset.estadoNombre);
                incidenciaArrastrada = null;
            });

            tablero.appendChild(columna);
        });

    } catch (error) {
        tablero.innerHTML = '<div class="text-danger">Error de conexión con el servidor.</div>';
    }
}

async function moverIncidencia(id, estadoId, estadoNombre) {

    const alerta = document.getElementById('alertTablero');
    alerta.className = 'alert d-none';

    try {
        const response = await authFetch(`/api/incidencias/${id}/estado`, {
            method: 'POST',
            body: JSON.stringify({
                estado_incidencia_id: estadoId,
                observacion: 'Estado actualizado desde el tablero Kanban'
            })
        });

        if (response.ok) {
            cargarTablero();
            return;
        }

        const data = await response.json();

        if (response.status !== 422) {
            alerta.textContent = data.message || 'No se pudo mover la incidencia';
            alerta.classList.remove('d-none');
            alerta.classList.add('alert-danger');
        }

    } catch (error) {
        alerta.textContent = 'Error de conexión con el servidor';
        alerta.classList.remove('d-none');
        alerta.classList.add('alert-danger');
    }
}

cargarTablero();
</script>
@endsection