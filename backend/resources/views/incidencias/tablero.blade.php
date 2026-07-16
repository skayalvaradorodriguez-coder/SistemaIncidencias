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
    }

    .tarjeta-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.75rem;
        opacity: 0.8;
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
</style>
@endsection

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Tablero Kanban</h1>
        <span class="text-muted">
            <i class="fas fa-hand-paper mr-1"></i>
            Arrastra una tarjeta a otra columna para cambiar su estado
        </span>
    </div>

    <div id="alertTablero" class="alert d-none"></div>

    <div class="tablero" id="tablero">
        <div class="text-center w-100 py-5">
            <i class="fas fa-spinner fa-spin mr-2"></i>Cargando tablero...
        </div>
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

function escaparHtml(texto) {
    const div = document.createElement('div');
    div.textContent = texto ?? '';
    return div.innerHTML;
}

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
                    <div class="tarjeta-titulo">#${inc.id} · ${escaparHtml(inc.titulo)}</div>
                    <div class="tarjeta-meta">
                        <span><i class="fas fa-map-marker-alt mr-1"></i>${escaparHtml(inc.ciudad ? inc.ciudad.nombre : 'N/A')}</span>
                        <span class="prioridad-pill prioridad-${inc.prioridad}">${inc.prioridad}</span>
                    </div>
                `;

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

        // 422 = misma columna; se ignora en silencio
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