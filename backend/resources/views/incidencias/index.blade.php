@extends('layouts.app')

@section('title', 'Incidencias')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Incidencias</h1>

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
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Ciudad</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Prioridad</th>
                        <th>Usuario</th>
                        <th>Fecha</th>
                        <th>Antigüedad</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody id="tablaIncidencias">
                    <tr>
                        <td colspan="10" class="text-center">
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

    const tabla = document.getElementById('tablaIncidencias');
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

    try {
        const response = await authFetch('/api/incidencias?' + params.toString());

        if (!response.ok) {
            tabla.innerHTML = '<tr><td colspan="10" class="text-center text-danger">Error al cargar las incidencias.</td></tr>';
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
            tabla.innerHTML = '<tr><td colspan="10" class="text-center">No se encontraron incidencias con los filtros aplicados.</td></tr>';
            return;
        }

        tabla.innerHTML = '';

        incidencias.forEach(inc => {

            const fecha = new Date(inc.created_at).toLocaleDateString('es-EC');

            const botonEliminar = esAdmin
                ? `<button type="button" class="btn btn-sm btn-danger btn-eliminar" data-id="${inc.id}">
                       <i class="fas fa-trash"></i>
                   </button>`
                : '';

            const fila = document.createElement('tr');
            fila.innerHTML = `
                <td>${inc.id}</td>
                <td>${escaparHtml(inc.titulo)}</td>
                <td>${escaparHtml(inc.ciudad ? inc.ciudad.nombre : 'Sin ciudad')}</td>
                <td>${escaparHtml(inc.tipo ? inc.tipo.nombre : 'Sin tipo')}</td>
                <td>
                    <span class="badge badge-${inc.estado ? inc.estado.color : 'secondary'}">
                        ${escaparHtml(inc.estado ? inc.estado.nombre : 'Sin estado')}
                    </span>
                </td>
                <td>${escaparHtml(inc.prioridad)}</td>
                <td>${escaparHtml(inc.usuario ? inc.usuario.name : 'Sin usuario')}</td>
                <td>${fecha}</td>
                <td>${badgeAntiguedad(inc)}</td>
                <td>
                    <a href="/incidencias/${inc.id}" class="btn btn-sm btn-info">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="/incidencias/${inc.id}/editar" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit"></i>
                    </a>
                    ${botonEliminar}
                </td>
            `;
            tabla.appendChild(fila);
        });

        activarBotonesEliminar();

    } catch (error) {
        tabla.innerHTML = '<tr><td colspan="10" class="text-center text-danger">Error de conexión con el servidor.</td></tr>';
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