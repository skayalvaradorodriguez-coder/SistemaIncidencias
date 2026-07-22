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

    .tablero-header-page {
        background: linear-gradient(135deg, rgba(10,17,40,0.35) 0%, rgba(22,35,63,0.25) 45%, rgba(201,169,97,0.18) 100%);
        border: 1px solid var(--border-subtle, rgba(255,255,255,0.08));
        border-radius: 14px;
        padding: 18px 22px;
    }

    .columna {
        flex: 1;
        min-width: 270px;
        background: var(--bg-card, #1a2333);
        border: 1px solid var(--border-subtle, rgba(255,255,255,0.08));
        border-top: 3px solid var(--color-columna, #6c757d);
        border-radius: 14px;
        padding: 14px;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.22);
        transition: box-shadow 0.15s;
    }

    .columna-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 700;
        font-size: 0.92rem;
        padding: 2px 2px 12px;
        margin-bottom: 10px;
        border-bottom: 1px solid var(--border-subtle, rgba(255,255,255,0.08));
    }

    .columna-header .titulo-columna {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .columna-header .titulo-columna::before {
        content: '';
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: var(--color-columna, #6c757d);
        box-shadow: 0 0 0 3px rgba(255,255,255,0.08);
        flex-shrink: 0;
    }

    .columna-contador {
        background: var(--color-columna, #6c757d);
        color: #fff;
        border-radius: 12px;
        padding: 1px 10px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .columna.arrastre-encima {
        outline: 2px dashed var(--color-columna, #6c757d);
        outline-offset: -6px;
        box-shadow: 0 0 0 4px color-mix(in srgb, var(--color-columna, #6c757d) 15%, transparent);
    }

    .columna-cuerpo {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .tarjeta {
        position: relative;
        background: var(--bg-card-tarjeta, #222f45);
        border: 1px solid var(--border-subtle, rgba(255,255,255,0.06));
        border-radius: 10px;
        border-left: 4px solid var(--color-columna, #6c757d);
        padding: 12px 14px;
        cursor: grab;
        transition: transform 0.15s, box-shadow 0.15s, background 0.15s;
    }

    .tarjeta:hover {
        transform: translateY(-2px);
        background: var(--bg-card-hover, #29344a);
        box-shadow: 0 8px 18px rgba(0, 0, 0, 0.4);
    }

    .tarjeta.arrastrando {
        opacity: 0.5;
    }

    .tarjeta-id {
        display: inline-block;
        font-size: 0.68rem;
        font-weight: 700;
        color: var(--text-muted, #9ca3af);
        letter-spacing: 0.4px;
        margin-bottom: 4px;
    }

    .tarjeta-titulo {
        font-weight: 600;
        font-size: 0.88rem;
        line-height: 1.3;
        margin-bottom: 10px;
        padding-right: 28px;
        color: var(--text-main, #f1f5f9);
    }

    .tarjeta-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.75rem;
        color: var(--text-muted, #9ca3af);
    }

    .btn-mover {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(255, 255, 255, 0.08);
        border: none;
        color: var(--text-muted, #cbd5e1);
        border-radius: 50%;
        width: 26px;
        height: 26px;
        font-size: 0.72rem;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background 0.15s, color 0.15s;
    }

    .btn-mover:hover {
        background: var(--brand-400, #C9A961);
        color: #fff;
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

    .columna-vacia {
        text-align: center;
        color: var(--text-muted, #9ca3af);
        font-size: 0.8rem;
        padding: 28px 0;
        border: 1px dashed var(--border-subtle, rgba(255,255,255,0.1));
        border-radius: 10px;
    }

    .columna-vacia i {
        display: block;
        font-size: 1.3rem;
        margin-bottom: 6px;
        opacity: 0.5;
    }

    .ayuda-escritorio { display: inline; }
    .ayuda-movil { display: none; }

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
        background: var(--bg-panel, #111827);
        border-top: 1px solid var(--border-subtle, rgba(255,255,255,0.08));
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

    <div class="tablero-header-page d-flex justify-content-between align-items-center flex-wrap mb-4">
        <div>
            <h1 class="mb-1"><i class="fas fa-columns mr-2"></i>Tablero Kanban</h1>
            <span class="text-muted" style="color:var(--text-muted) !important;">
                <i class="fas fa-hand-paper mr-1"></i>
                <span class="ayuda-escritorio">Arrastra una tarjeta a otra columna para cambiar su estado</span>
                <span class="ayuda-movil">Toca el botón <i class="fas fa-exchange-alt"></i> de una tarjeta para cambiar su estado</span>
            </span>
        </div>
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
                    <span class="titulo-columna">${escaparHtml(estado.nombre)}</span>
                    <span class="columna-contador">${propias.length}</span>
                </div>
                <div class="columna-cuerpo"></div>
            `;

            const cuerpo = columna.querySelector('.columna-cuerpo');

            if (propias.length === 0) {
                cuerpo.innerHTML = '<div class="columna-vacia"><i class="fas fa-inbox"></i>Sin incidencias</div>';
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
                    <div class="tarjeta-id">#${inc.id}</div>
                    <div class="tarjeta-titulo">${escaparHtml(inc.titulo)}</div>
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