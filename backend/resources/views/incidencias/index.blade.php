@extends('layouts.app')

@section('title', 'Incidencias')

@section('styles')
<style>
    .incidencias-header {
        background: linear-gradient(135deg, rgba(201,169,97,0.30) 0%, rgba(169,134,63,0.22) 45%, rgba(10,17,40,0.20) 100%);
        border: 1px solid var(--border-subtle);
        border-radius: 14px;
        padding: 18px 22px;
    }

    #tablaIncidencias td {
        vertical-align: middle;
    }

    /* =========================================================
       Celda principal: avatar circular con iniciales del tipo
       (mismo patrón visual que la vista de Usuarios)
       ========================================================= */
    .avatar-mini {
        width: 34px;
        height: 34px;
        min-width: 34px;
        border-radius: 50%;
        background: var(--brand-gradient);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.78rem;
        box-shadow: 0 2px 6px rgba(0,0,0,0.25);
    }

    .incidencia-titulo-celda {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .incidencia-titulo-celda .titulo-principal {
        font-weight: 600;
        color: var(--text-main);
        line-height: 1.2;
    }

    .incidencia-titulo-celda .id-chico {
        font-size: 0.7rem;
        color: var(--text-muted);
    }

    /* =========================================================
       Pills de Estado y Prioridad (mismo look que rol-pill)
       ========================================================= */
    .estado-pill,
    .prioridad-pill {
        display: inline-block;
        border-radius: 20px;
        padding: 2px 12px;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.2px;
        white-space: nowrap;
    }

    .estado-pill-warning   { background: rgba(245,158,11,0.15); color: #f59e0b; border: 1px solid rgba(245,158,11,0.4); }
    .estado-pill-primary   { background: rgba(13,110,253,0.15); color: #3b82f6; border: 1px solid rgba(13,110,253,0.4); }
    .estado-pill-success   { background: rgba(34,197,94,0.15);  color: #16a34a; border: 1px solid rgba(34,197,94,0.4); }
    .estado-pill-danger    { background: rgba(239,68,68,0.15);  color: #ef4444; border: 1px solid rgba(239,68,68,0.4); }
    .estado-pill-secondary { background: rgba(148,163,184,0.15); color: #94a3b8; border: 1px solid rgba(148,163,184,0.4); }
    .estado-pill-info      { background: rgba(6,182,212,0.15);  color: #06b6d4; border: 1px solid rgba(6,182,212,0.4); }

    .prioridad-pill-baja     { background: rgba(34,197,94,0.15);  color: #16a34a; border: 1px solid rgba(34,197,94,0.4); }
    .prioridad-pill-media    { background: rgba(245,158,11,0.15); color: #f59e0b; border: 1px solid rgba(245,158,11,0.4); }
    .prioridad-pill-alta     { background: rgba(253,126,20,0.15); color: #fd7e14; border: 1px solid rgba(253,126,20,0.4); }
    .prioridad-pill-critica  { background: rgba(239,68,68,0.18);  color: #ef4444; border: 1px solid rgba(239,68,68,0.5); font-weight: 800; }

    /* =========================================================
       Botones de acción: compactos, solo ícono
       ========================================================= */
    .acciones-grupo {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 6px;
        flex-wrap: nowrap;
    }

    .btn-accion {
        width: 30px;
        height: 30px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-size: 0.78rem;
        line-height: 1;
        border: none;
    }

    .btn-accion:hover,
    .btn-accion:focus {
        filter: brightness(1.1);
        color: #fff;
    }

    .btn-ver     { background: rgba(201,169,97,0.18); color: #C9A961; }
    .btn-ver:hover { background: #C9A961; color: #0A1128 !important; }

    .btn-editar  { background: rgba(245,158,11,0.18); color: #f59e0b; }
    .btn-editar:hover { background: #f59e0b; }

    .btn-eliminar-accion { background: rgba(239,68,68,0.18); color: #ef4444; }
    .btn-eliminar-accion:hover { background: #ef4444; }

    /* =========================================================
       Responsive
       ========================================================= */
    @media (max-width: 767.98px) {

        .incidencias-header {
            padding: 16px;
        }

        .incidencias-header h1 {
            font-size: 1.35rem;
        }

        .incidencias-header .btn {
            width: 100%;
            justify-content: center;
        }

        .incidencias-header > div:first-child {
            width: 100%;
        }

        #tablaIncidencias thead {
            display: none;
        }

        #tablaIncidencias,
        #tablaIncidencias tbody,
        #tablaIncidencias tr,
        #tablaIncidencias td {
            display: block;
            width: 100%;
        }

        #tablaIncidencias {
            border: none;
        }

        #tablaIncidencias tr {
            margin-bottom: 14px;
            border: 1px solid var(--border-subtle);
            border-radius: 12px;
            padding: 12px 14px;
            background: var(--bg-card);
        }

        #tablaIncidencias td {
            border: none;
            padding: 6px 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        #tablaIncidencias td[data-label]::before {
            content: attr(data-label);
            font-weight: 600;
            color: var(--text-muted);
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            flex-shrink: 0;
        }

        #tablaIncidencias td.celda-incidencia {
            padding-bottom: 10px;
            margin-bottom: 6px;
            border-bottom: 1px solid var(--border-subtle);
        }

        #tablaIncidencias td.celda-incidencia::before {
            display: none;
        }

        #tablaIncidencias td.celda-incidencia .incidencia-titulo-celda {
            width: 100%;
        }

        #tablaIncidencias td.celda-acciones {
            padding-top: 10px;
            margin-top: 6px;
            border-top: 1px solid var(--border-subtle);
        }

        #tablaIncidencias td.celda-acciones::before {
            display: none;
        }

        #tablaIncidencias td.celda-acciones .acciones-grupo {
            width: 100%;
            justify-content: flex-end;
        }
    }
</style>
@endsection

@section('content')

<div class="container-fluid">

    <div class="incidencias-header d-flex justify-content-between align-items-center flex-wrap mb-4">
        <div>
            <h1 class="mb-1"><i class="fas fa-exclamation-triangle mr-2"></i>Incidencias</h1>
            <span style="color:var(--text-muted);">Consulta, filtra y gestiona las incidencias registradas</span>
        </div>

        <a href="{{ route('incidencias.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nueva Incidencia
        </a>
    </div>

    <!-- Filtros -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-filter mr-2"></i>Filtros de búsqueda
            </h3>
        </div>
        <div class="card-body">
            <div class="form-row">

                <div class="form-group col-md-3">
                    <label>Buscar</label>
                    <input type="text" id="filtroTexto" class="form-control"
                           placeholder="Título o descripción...">
                </div>

                <div class="form-group col-md-2">
                    <label>Estado</label>
                    <select id="filtroEstado" class="form-control">
                        <option value="">Todos</option>
                        @foreach($estados as $estado)
                            <option value="{{ $estado->id }}">{{ $estado->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-2">
                    <label>Tipo</label>
                    <select id="filtroTipo" class="form-control">
                        <option value="">Todos</option>
                        @foreach($tipos as $tipo)
                            <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-2">
                    <label>Prioridad</label>
                    <select id="filtroPrioridad" class="form-control">
                        <option value="">Todas</option>
                        <option value="Baja">Baja</option>
                        <option value="Media">Media</option>
                        <option value="Alta">Alta</option>
                        <option value="Crítica">Crítica</option>
                    </select>
                </div>

                <div class="form-group col-md-2">
                    <label>Ciudad</label>
                    <select id="filtroCiudad" class="form-control">
                        <option value="">Todas</option>
                        @foreach($ciudades as $ciudad)
                            <option value="{{ $ciudad->id }}">{{ $ciudad->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-1 d-flex align-items-end">
                    <button type="button" id="btnLimpiar" class="btn btn-secondary btn-block" title="Limpiar filtros">
                        <i class="fas fa-eraser"></i>
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- Listado -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Listado de incidencias registradas</h3>
            <div class="card-tools">
                <button type="button" id="btnExportar" class="btn btn-sm btn-success mr-2">
                    <i class="fas fa-file-csv mr-1"></i>Exportar CSV
                </button>
                <span class="badge badge-info" id="contadorResultados">Cargando...</span>
            </div>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover" id="tablaIncidencias">
                <thead>
                    <tr>
                        <th>Incidencia</th>
                        <th>Ciudad</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Prioridad</th>
                        <th>Usuario</th>
                        <th>Fecha</th>
                        <th>Antigüedad</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td colspan="9" class="text-center">
                            <i class="fas fa-spinner fa-spin mr-2"></i>Cargando incidencias...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection

@section('scripts')
<script>
const usuarioActual = getUser();
const esAdmin = usuarioActual && usuarioActual.rol && usuarioActual.rol.nombre === 'Administrador';

let incidenciasVisibles = [];

function escaparHtml(texto) {
    const div = document.createElement('div');
    div.textContent = texto ?? '';
    return div.innerHTML;
}

function inicialesTipo(nombre) {
    const limpio = (nombre || '?').trim();
    const palabras = limpio.split(/\s+/);
    if (palabras.length >= 2) {
        return (palabras[0].charAt(0) + palabras[1].charAt(0)).toUpperCase();
    }
    return limpio.slice(0, 2).toUpperCase();
}

function normalizarPrioridad(prioridad) {
    return (prioridad || '')
        .normalize('NFD').replace(/[\u0300-\u036f]/g, '') // quita tildes: Crítica -> Critica
        .toLowerCase();
}

function badgeAntiguedad(inc) {
    const dias = Math.floor((new Date() - new Date(inc.created_at)) / 86400000);
    const resuelta = inc.estado && (inc.estado.nombre === 'Resuelto' || inc.estado.nombre === 'Rechazado');
    const texto = dias === 0 ? 'Hoy' : (dias === 1 ? '1 día' : dias + ' días');

    if (resuelta) return `<span class="badge badge-secondary">${texto}</span>`;
    if (dias >= 7) return `<span class="badge badge-danger" title="Atención: lleva ${dias} días sin resolverse"><i class="fas fa-exclamation-circle mr-1"></i>${texto}</span>`;
    if (dias >= 3) return `<span class="badge badge-warning">${texto}</span>`;
    return `<span class="badge badge-success">${texto}</span>`;
}

async function cargarIncidencias() {

    const contador = document.getElementById('contadorResultados');

    const params = new URLSearchParams();

    const estado = document.getElementById('filtroEstado').value;
    const tipo = document.getElementById('filtroTipo').value;
    const prioridad = document.getElementById('filtroPrioridad').value;
    const ciudad = document.getElementById('filtroCiudad').value;

    if (estado) params.append('estado_id', estado);
    if (tipo) params.append('tipo_id', tipo);
    if (prioridad) params.append('prioridad', prioridad);
    if (ciudad) params.append('ciudad_id', ciudad);

    const tbody = document.querySelector('#tablaIncidencias tbody');

    try {
        const response = await authFetch('/api/incidencias?' + params.toString());

        if (!response.ok) {
            tbody.innerHTML = '<tr><td colspan="9" class="text-center text-danger">Error al cargar las incidencias.</td></tr>';
            return;
        }

        let incidencias = await response.json();

        // Filtro de texto en el cliente (título y descripción)
        const texto = document.getElementById('filtroTexto').value.trim().toLowerCase();

        if (texto) {
            incidencias = incidencias.filter(inc =>
                (inc.titulo || '').toLowerCase().includes(texto) ||
                (inc.descripcion || '').toLowerCase().includes(texto)
            );
        }

        incidenciasVisibles = incidencias;

        contador.textContent = incidencias.length + (incidencias.length === 1 ? ' resultado' : ' resultados');

        if (incidencias.length === 0) {
            tbody.innerHTML = '<tr><td colspan="9" class="text-center">No se encontraron incidencias con los filtros aplicados.</td></tr>';
            return;
        }

        let html = '';

        incidencias.forEach(inc => {

            const fecha = new Date(inc.created_at).toLocaleDateString('es-EC');
            const tipoNombre = inc.tipo ? inc.tipo.nombre : 'Sin tipo';
            const estadoColor = inc.estado ? inc.estado.color : 'secondary';
            const prioridadClase = normalizarPrioridad(inc.prioridad);

            const botonEliminar = esAdmin
                ? `<button type="button" class="btn-accion btn-eliminar-accion btn-eliminar" data-id="${inc.id}" title="Eliminar">
                       <i class="fas fa-trash"></i>
                   </button>`
                : '';

            html += `
                <tr>
                    <td class="celda-incidencia">
                        <div class="incidencia-titulo-celda">
                            <div class="avatar-mini" title="${escaparHtml(tipoNombre)}">${inicialesTipo(tipoNombre)}</div>
                            <div>
                                <div class="titulo-principal">${escaparHtml(inc.titulo)}</div>
                                <div class="id-chico">ID #${inc.id}</div>
                            </div>
                        </div>
                    </td>
                    <td data-label="Ciudad">${escaparHtml(inc.ciudad ? inc.ciudad.nombre : 'Sin ciudad')}</td>
                    <td data-label="Tipo">${escaparHtml(tipoNombre)}</td>
                    <td data-label="Estado">
                        <span class="estado-pill estado-pill-${estadoColor}">
                            ${escaparHtml(inc.estado ? inc.estado.nombre : 'Sin estado')}
                        </span>
                    </td>
                    <td data-label="Prioridad">
                        <span class="prioridad-pill prioridad-pill-${prioridadClase}">
                            ${escaparHtml(inc.prioridad)}
                        </span>
                    </td>
                    <td data-label="Usuario">${escaparHtml(inc.usuario ? inc.usuario.name : 'Sin usuario')}</td>
                    <td data-label="Fecha">${fecha}</td>
                    <td data-label="Antigüedad">${badgeAntiguedad(inc)}</td>
                    <td class="celda-acciones">
                        <div class="acciones-grupo">
                            <a href="/incidencias/${inc.id}" class="btn-accion btn-ver" title="Ver">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="/incidencias/${inc.id}/editar" class="btn-accion btn-editar" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            ${botonEliminar}
                        </div>
                    </td>
                </tr>
            `;
        });

        tbody.innerHTML = html;

        activarBotonesEliminar();

    } catch (error) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center text-danger">Error de conexión con el servidor.</td></tr>';
    }
}

function activarBotonesEliminar() {
    document.querySelectorAll('.btn-eliminar').forEach(btn => {
        btn.addEventListener('click', async function () {
            if (!confirm('¿Seguro que deseas eliminar esta incidencia?')) return;

            const id = this.dataset.id;

            try {
                const response = await authFetch(`/api/incidencias/${id}`, { method: 'DELETE' });

                if (response.ok) {
                    cargarIncidencias();
                } else {
                    const data = await response.json();
                    alert(data.message || 'No se pudo eliminar la incidencia');
                }
            } catch (err) {
                alert('Error de conexión');
            }
        });
    });
}

// Los combos filtran al cambiar; el texto filtra al escribir (con pequeña espera)
['filtroEstado', 'filtroTipo', 'filtroPrioridad', 'filtroCiudad'].forEach(id => {
    document.getElementById(id).addEventListener('change', cargarIncidencias);
});

let esperaTexto = null;
document.getElementById('filtroTexto').addEventListener('input', function () {
    clearTimeout(esperaTexto);
    esperaTexto = setTimeout(cargarIncidencias, 400);
});

document.getElementById('btnLimpiar').addEventListener('click', function () {
    document.getElementById('filtroTexto').value = '';
    document.getElementById('filtroEstado').value = '';
    document.getElementById('filtroTipo').value = '';
    document.getElementById('filtroPrioridad').value = '';
    document.getElementById('filtroCiudad').value = '';
    cargarIncidencias();
});

document.getElementById('btnExportar').addEventListener('click', function () {
    if (incidenciasVisibles.length === 0) {
        alert('No hay datos para exportar.');
        return;
    }

    const encabezado = ['ID', 'Titulo', 'Ciudad', 'Tipo', 'Estado', 'Prioridad', 'Usuario', 'Fecha'];

    const filas = incidenciasVisibles.map(inc => [
        inc.id,
        '"' + (inc.titulo || '').replace(/"/g, '""') + '"',
        inc.ciudad ? inc.ciudad.nombre : '',
        inc.tipo ? inc.tipo.nombre : '',
        inc.estado ? inc.estado.nombre : '',
        inc.prioridad,
        inc.usuario ? inc.usuario.name : '',
        new Date(inc.created_at).toLocaleDateString('es-EC')
    ].join(','));

    const csv = '\uFEFF' + encabezado.join(',') + '\n' + filas.join('\n');
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const enlace = document.createElement('a');
    enlace.href = URL.createObjectURL(blob);
    enlace.download = 'incidencias_' + new Date().toISOString().slice(0, 10) + '.csv';
    enlace.click();
});

cargarIncidencias();
</script>
@endsection