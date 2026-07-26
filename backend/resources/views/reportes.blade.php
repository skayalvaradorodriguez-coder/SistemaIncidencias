@extends('layouts.app')

@section('title', 'Reportes')

@section('styles')
<style>
    /* ===== Selector de tipo de reporte ===== */
    .selector-reportes {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 22px;
    }

    .pildora-reporte {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 18px;
        border-radius: 10px;
        border: 1px solid var(--border-subtle);
        background: var(--bg-card);
        color: var(--text-main);
        cursor: pointer;
        transition: all .15s ease;
        font-size: 0.86rem;
        font-weight: 600;
    }

    .pildora-reporte i {
        font-size: 1.05rem;
        width: 22px;
        text-align: center;
        color: var(--text-muted);
    }

    .pildora-reporte:hover {
        border-color: #C9A961;
    }

    .pildora-reporte.activa {
        background: linear-gradient(135deg, #16233F, #0A1128);
        border-color: #C9A961;
        color: #fff;
    }

    .pildora-reporte.activa i {
        color: #C9A961;
    }

    /* ===== Tarjetas KPI ===== */
    .kpi-card {
        border-radius: 10px;
        border: 1px solid var(--border-subtle);
        background: var(--bg-card);
        padding: 16px 18px;
        height: 100%;
    }

    .kpi-card .kpi-numero {
        font-size: 1.7rem;
        font-weight: 700;
        line-height: 1.1;
    }

    .kpi-card .kpi-etiqueta {
        font-size: 0.76rem;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        color: var(--text-muted);
    }

    .kpi-card .kpi-icono {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.05rem;
        margin-bottom: 10px;
    }

    /* ===== Buscador de tabla ===== */
    .buscador-reporte {
        max-width: 320px;
    }

    #sinAcceso { display: none; }
</style>
@endsection

@section('content')

<div class="container-fluid">

    <!-- ===== Sin acceso (se muestra vía JS si el rol no corresponde) ===== -->
    <div id="sinAcceso" class="text-center py-5">
        <i class="fas fa-lock d-block mb-3" style="font-size:3rem; opacity:0.35;"></i>
        <h4>No tienes acceso a esta sección</h4>
        <p class="text-muted">Los reportes de gestión están disponibles solo para Administradores y Responsables.</p>
        <a href="/" class="btn btn-primary mt-2"><i class="fas fa-home mr-1"></i>Volver al inicio</a>
    </div>

    <!-- ===== Contenido principal ===== -->
    <div id="contenidoReportes" style="display:none;">

        <div class="d-flex justify-content-between align-items-center flex-wrap mb-1">
            <h1 class="mb-2">Centro de Reportes</h1>
            <button type="button" id="btnPdf" class="btn btn-danger mb-2">
                <i class="fas fa-file-pdf mr-1"></i> Descargar PDF
            </button>
        </div>
        <p class="text-muted mb-4">Selecciona el tipo de reporte que quieres consultar o exportar.</p>

        <!-- ===== Selector de tipos ===== -->
        <div class="selector-reportes" id="selectorReportes">
            <div class="pildora-reporte activa" data-tipo="todas">
                <i class="fas fa-list-ul"></i> Todas las Incidencias
            </div>
            <div class="pildora-reporte" data-tipo="Pendiente">
                <i class="fas fa-clock"></i> Pendientes
            </div>
            <div class="pildora-reporte" data-tipo="En Proceso">
                <i class="fas fa-spinner"></i> En Proceso
            </div>
            <div class="pildora-reporte" data-tipo="Resuelto">
                <i class="fas fa-check-circle"></i> Resueltas
            </div>
            <div class="pildora-reporte" data-tipo="Rechazado">
                <i class="fas fa-times-circle"></i> Rechazadas
            </div>
            <div class="pildora-reporte" data-tipo="usuarios" id="pildoraUsuarios" style="display:none;">
                <i class="fas fa-users"></i> Usuarios del Sistema
            </div>
            <div class="pildora-reporte" data-tipo="por_usuario" id="pildoraPorUsuario" style="display:none;">
                <i class="fas fa-user"></i> Reporte por Usuario
            </div>
        </div>

        <!-- ===== KPIs ===== -->
        <div class="row mb-4" id="filaKpis"></div>

        <!-- ===== Tabla ===== -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                <h3 class="card-title mb-0" id="tituloTabla">Todas las Incidencias</h3>
                <div class="d-flex align-items-center flex-wrap" style="gap:10px;">
                    <select id="selectUsuarioReporte" class="form-control form-control-sm" style="display:none; min-width:220px;">
                        <option value="">Selecciona un usuario...</option>
                    </select>
                    <input type="text" id="buscadorReporte" class="form-control form-control-sm buscador-reporte"
                           placeholder="Buscar en esta tabla...">
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover mb-0">
                    <thead id="cabeceraTabla"></thead>
                    <tbody id="cuerpoTabla">
                        <tr>
                            <td class="text-center text-muted py-4">
                                <i class="fas fa-spinner fa-spin mr-2"></i>Cargando datos...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>

@endsection

@section('scripts')
<script>

// ================== DETECTOR DE ERRORES VISIBLE EN PANTALLA ==================
// Si algo falla en este script, se muestra un aviso rojo en vez de dejar la página en blanco.
window.addEventListener('error', function (e) {
    let aviso = document.getElementById('avisoErrorReportes');
    if (!aviso) {
        aviso = document.createElement('div');
        aviso.id = 'avisoErrorReportes';
        aviso.style.cssText = 'position:fixed;top:0;left:0;right:0;z-index:99999;background:#B3413A;color:#fff;padding:14px 20px;font-family:monospace;font-size:13px;white-space:pre-wrap;';
        document.body.prepend(aviso);
    }
    aviso.textContent = 'Error en reportes.blade.php -> ' + e.message + ' (línea ' + e.lineno + ', columna ' + e.colno + ')';
});

let todasLasIncidencias = [];
let ultimosKpis = [];
let todosLosUsuarios = [];
let tipoActivo = 'todas';
let esAdminReporte = false;

const COLOR_ESTADO = {
    'Pendiente': { hex: '#C9A961', clase: 'warning' },
    'En Proceso': { hex: '#16233F', clase: 'primary' },
    'Resuelto':   { hex: '#2F7A4D', clase: 'success' },
    'Rechazado':  { hex: '#B3413A', clase: 'danger' }
};

const COLOR_PRIORIDAD = {
    'Baja': '#2F7A4D',
    'Media': '#C98A2C',
    'Alta': '#C9622E',
    'Crítica': '#B3413A'
};

function escaparHtml(texto) {
    const div = document.createElement('div');
    div.textContent = texto ?? '';
    return div.innerHTML;
}

// ================== CONTROL DE ACCESO ==================
(function () {
    const usuario = getUser();
    const rol = usuario && usuario.rol ? usuario.rol.nombre : null;

    if (rol !== 'Administrador' && rol !== 'Responsable') {
        document.getElementById('sinAcceso').style.display = 'block';
        return;
    }

    esAdminReporte  = (rol === 'Administrador');
    if (esAdminReporte) {
        document.getElementById('pildoraUsuarios').style.display = 'flex';
        document.getElementById('pildoraPorUsuario').style.display = 'flex';
    }

    document.getElementById('contenidoReportes').style.display = 'block';
    cargarDatos();
})();

// ================== CARGA DE DATOS ==================
async function cargarDatos() {
    try {
        const respIncidencias = await authFetch('/api/incidencias');
        todasLasIncidencias = respIncidencias.ok ? await respIncidencias.json() : [];

        if (esAdminReporte) {
            const respUsuarios = await authFetch('/api/usuarios');
            todosLosUsuarios = respUsuarios.ok ? await respUsuarios.json() : [];
        }

        renderizarReporte('todas');

    } catch (error) {
        document.getElementById('cuerpoTabla').innerHTML =
            '<tr><td class="text-center text-danger py-4">Error de conexión con el servidor.</td></tr>';
    }
}

// ================== SELECTOR DE TIPO ==================
document.getElementById('selectorReportes').addEventListener('click', function (e) {
    const pildora = e.target.closest('.pildora-reporte');
    if (!pildora) return;

    document.querySelectorAll('.pildora-reporte').forEach(p => p.classList.remove('activa'));
    pildora.classList.add('activa');

    renderizarReporte(pildora.dataset.tipo);
});

// ================== RENDER PRINCIPAL ==================
function renderizarReporte(tipo) {
    tipoActivo = tipo;
    document.getElementById('buscadorReporte').value = '';

    if (tipo === 'usuarios') {
        document.getElementById('selectUsuarioReporte').style.display = 'none';
        renderizarKpisUsuarios();
        renderizarTablaUsuarios(todosLosUsuarios);
        document.getElementById('tituloTabla').textContent = 'Usuarios del Sistema';
        return;
    }

    if (tipo === 'por_usuario') {
        const selector = document.getElementById('selectUsuarioReporte');
        selector.style.display = 'inline-block';
        poblarSelectUsuarios();
        if (selector.value) {
            renderizarReportePorUsuario(selector.value);
        } else {
            document.getElementById('tituloTabla').textContent = 'Reporte por Usuario';
            document.getElementById('filaKpis').innerHTML = '';
            document.getElementById('cabeceraTabla').innerHTML = '';
            document.getElementById('cuerpoTabla').innerHTML =
                '<tr><td class="text-center text-muted py-4">Selecciona un usuario arriba para ver su reporte de incidencias.</td></tr>';
        }
        return;
    }

    document.getElementById('selectUsuarioReporte').style.display = 'none';

    const datos = tipo === 'todas'
        ? todasLasIncidencias
        : todasLasIncidencias.filter(i => (i.estado ? i.estado.nombre : '') === tipo);

    const titulos = {
        'todas': 'Todas las Incidencias',
        'Pendiente': 'Incidencias Pendientes',
        'En Proceso': 'Incidencias En Proceso',
        'Resuelto': 'Incidencias Resueltas',
        'Rechazado': 'Incidencias Rechazadas'
    };

    document.getElementById('tituloTabla').textContent = titulos[tipo] || 'Incidencias';

    renderizarKpisIncidencias(datos);
    renderizarTablaIncidencias(datos);
}

// ================== KPIs: INCIDENCIAS ==================
function renderizarKpisIncidencias(datos) {

    const porPrioridad = { 'Baja': 0, 'Media': 0, 'Alta': 0, 'Crítica': 0 };
    let conUbicacion = 0;

    datos.forEach(inc => {
        if (porPrioridad[inc.prioridad] !== undefined) porPrioridad[inc.prioridad]++;
        if (inc.latitud && inc.longitud) conUbicacion++;
    });

    const prioridadMasFrecuente = Object.entries(porPrioridad)
        .sort((a, b) => b[1] - a[1])[0];

    const kpis = [
        { icono: 'fa-list-ul', color: '#16233F', numero: datos.length, etiqueta: 'Total en este reporte' },
        { icono: 'fa-exclamation-circle', color: COLOR_PRIORIDAD[prioridadMasFrecuente[0]] || '#6c757d',
          numero: prioridadMasFrecuente[1] > 0 ? prioridadMasFrecuente[0] : '—', etiqueta: 'Prioridad más frecuente' },
        { icono: 'fa-map-marker-alt', color: '#2F7A4D', numero: conUbicacion, etiqueta: 'Con ubicación registrada' },
        { icono: 'fa-percentage', color: '#C9A961',
          numero: todasLasIncidencias.length > 0 ? Math.round((datos.length / todasLasIncidencias.length) * 100) + '%' : '0%',
          etiqueta: 'Del total de incidencias' }
    ];

    pintarKpis(kpis);
}

// ================== KPIs: USUARIOS ==================
function renderizarKpisUsuarios() {
    const activos = todosLosUsuarios.filter(u => u.activo).length;
    const porRol = {};
    todosLosUsuarios.forEach(u => {
        const r = u.rol ? u.rol.nombre : 'Sin rol';
        porRol[r] = (porRol[r] || 0) + 1;
    });

    const kpis = [
        { icono: 'fa-users', color: '#16233F', numero: todosLosUsuarios.length, etiqueta: 'Total de usuarios' },
        { icono: 'fa-user-check', color: '#2F7A4D', numero: activos, etiqueta: 'Cuentas activas' },
        { icono: 'fa-user-times', color: '#B3413A', numero: todosLosUsuarios.length - activos, etiqueta: 'Cuentas inactivas' },
        { icono: 'fa-user-shield', color: '#C9A961', numero: porRol['Administrador'] || 0, etiqueta: 'Administradores' }
    ];

    pintarKpis(kpis);
}

// ================== REPORTE POR USUARIO ==================
let selectUsuariosPoblado = false;

function poblarSelectUsuarios() {
    if (selectUsuariosPoblado) return;

    const selector = document.getElementById('selectUsuarioReporte');
    const usuariosOrdenados = [...todosLosUsuarios].sort((a, b) =>
        (a.name || '').localeCompare(b.name || '')
    );

    usuariosOrdenados.forEach(u => {
        const opcion = document.createElement('option');
        opcion.value = u.id;
        opcion.textContent = `${u.name} ${u.apellido || ''}`.trim();
        selector.appendChild(opcion);
    });

    selectUsuariosPoblado = true;
}

document.getElementById('selectUsuarioReporte').addEventListener('change', function () {
    if (this.value) {
        renderizarReportePorUsuario(this.value);
    } else {
        document.getElementById('tituloTabla').textContent = 'Reporte por Usuario';
        document.getElementById('filaKpis').innerHTML = '';
        document.getElementById('cabeceraTabla').innerHTML = '';
        document.getElementById('cuerpoTabla').innerHTML =
            '<tr><td class="text-center text-muted py-4">Selecciona un usuario arriba para ver su reporte de incidencias.</td></tr>';
    }
});

function renderizarReportePorUsuario(usuarioId) {
    const usuario = todosLosUsuarios.find(u => String(u.id) === String(usuarioId));
    const datos = todasLasIncidencias.filter(i => i.usuario && String(i.usuario.id) === String(usuarioId));

    const nombreUsuario = usuario ? `${usuario.name} ${usuario.apellido || ''}`.trim() : 'Usuario';
    document.getElementById('tituloTabla').textContent = `Incidencias de ${nombreUsuario}`;

    renderizarKpisIncidencias(datos);
    renderizarTablaIncidencias(datos);
}

// Botón "Ver reporte" desde la tabla de Usuarios del Sistema: salta directo
// al reporte de incidencias de ese usuario, sin pasar por el select manual.
document.getElementById('cuerpoTabla').addEventListener('click', function (e) {
    const boton = e.target.closest('.btn-reporte-usuario');
    if (!boton) return;

    const usuarioId = boton.dataset.usuarioId;

    document.querySelectorAll('.pildora-reporte').forEach(p => p.classList.remove('activa'));
    document.getElementById('pildoraPorUsuario').classList.add('activa');

    tipoActivo = 'por_usuario';

    poblarSelectUsuarios();
    const selector = document.getElementById('selectUsuarioReporte');
    selector.value = usuarioId;
    selector.style.display = 'inline-block';

    renderizarReportePorUsuario(usuarioId);
});

function pintarKpis(kpis) {
    ultimosKpis = kpis;
    const fila = document.getElementById('filaKpis');
    fila.innerHTML = kpis.map(k => `
        <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
            <div class="kpi-card">
                <div class="kpi-icono" style="background:${k.color}22; color:${k.color};">
                    <i class="fas ${k.icono}"></i>
                </div>
                <div class="kpi-numero">${k.numero}</div>
                <div class="kpi-etiqueta">${k.etiqueta}</div>
            </div>
        </div>
    `).join('');
}

// ================== TABLA: INCIDENCIAS ==================
function renderizarTablaIncidencias(datos) {
    document.getElementById('cabeceraTabla').innerHTML = `
        <tr>
            <th>#</th><th>Título</th><th>Ciudad</th><th>Tipo</th>
            <th>Estado</th><th>Prioridad</th><th>Reportado por</th><th>Fecha</th><th></th>
        </tr>`;

    const cuerpo = document.getElementById('cuerpoTabla');

    function pintar(filtrados) {
        if (filtrados.length === 0) {
            cuerpo.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4">Sin resultados para este reporte.</td></tr>';
            return;
        }

        cuerpo.innerHTML = filtrados.map(inc => {
            const estadoNombre = inc.estado ? inc.estado.nombre : 'N/A';
            const colorEstado = (COLOR_ESTADO[estadoNombre] || {}).clase || 'secondary';
            const colorPrioridad = COLOR_PRIORIDAD[inc.prioridad] || '#6c757d';

            return `
                <tr>
                    <td>${inc.id}</td>
                    <td>${escaparHtml(inc.titulo)}</td>
                    <td>${escaparHtml(inc.ciudad ? inc.ciudad.nombre : 'N/A')}</td>
                    <td>${escaparHtml(inc.tipo ? inc.tipo.nombre : 'N/A')}</td>
                    <td><span class="badge badge-${colorEstado}">${escaparHtml(estadoNombre)}</span></td>
                    <td><span class="badge" style="background:${colorPrioridad}; color:#fff;">${escaparHtml(inc.prioridad)}</span></td>
                    <td>${escaparHtml(inc.usuario ? inc.usuario.name : 'N/A')}</td>
                    <td>${new Date(inc.created_at).toLocaleDateString('es-EC')}</td>
                    <td><a href="/incidencias/${inc.id}" class="btn btn-sm btn-outline-info"><i class="fas fa-eye"></i></a></td>
                </tr>`;
        }).join('');
    }

    pintar(datos);

    document.getElementById('buscadorReporte').oninput = function () {
        const q = this.value.toLowerCase();
        pintar(datos.filter(i =>
            (i.titulo || '').toLowerCase().includes(q) ||
            (i.ciudad ? i.ciudad.nombre : '').toLowerCase().includes(q) ||
            (i.usuario ? i.usuario.name : '').toLowerCase().includes(q)
        ));
    };
}

// ================== TABLA: USUARIOS ==================
function conteoIncidenciasPorUsuario(usuarioId) {
    return todasLasIncidencias.filter(i => i.usuario && String(i.usuario.id) === String(usuarioId)).length;
}

function renderizarTablaUsuarios(datos) {
    document.getElementById('cabeceraTabla').innerHTML = `
        <tr><th>#</th><th>Nombre</th><th>Correo</th><th>Rol</th><th>Estado</th><th>Incidencias</th><th>Registrado</th><th></th></tr>`;

    const cuerpo = document.getElementById('cuerpoTabla');

    function pintar(filtrados) {
        if (filtrados.length === 0) {
            cuerpo.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4">Sin resultados.</td></tr>';
            return;
        }

        cuerpo.innerHTML = filtrados.map(u => {
            const conteo = conteoIncidenciasPorUsuario(u.id);
            return `
            <tr>
                <td>${u.id}</td>
                <td>${escaparHtml(u.name)} ${escaparHtml(u.apellido)}</td>
                <td>${escaparHtml(u.email)}</td>
                <td>${escaparHtml(u.rol ? u.rol.nombre : 'N/A')}</td>
                <td>${u.activo ? '<span class="badge badge-success">Activo</span>' : '<span class="badge badge-danger">Inactivo</span>'}</td>
                <td><span class="badge badge-secondary">${conteo}</span></td>
                <td>${new Date(u.created_at).toLocaleDateString('es-EC')}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline-info btn-reporte-usuario"
                            data-usuario-id="${u.id}" title="Ver reporte de incidencias">
                        <i class="fas fa-file-alt"></i>
                    </button>
                </td>
            </tr>`;
        }).join('');
    }

    pintar(datos);

    document.getElementById('buscadorReporte').oninput = function () {
        const q = this.value.toLowerCase();
        pintar(datos.filter(u =>
            (u.name || '').toLowerCase().includes(q) ||
            (u.email || '').toLowerCase().includes(q)
        ));
    };
}

// ================== EXPORTAR PDF ==================
const LOGO_URL = '/images/logo.png'; // <-- 👈 CAMBIÁ ESTA RUTA POR LA DE TU LOGO
 
document.getElementById('btnPdf').addEventListener('click', function () {

    const idUsuarioReporte = document.getElementById('selectUsuarioReporte').value;

    if (tipoActivo === 'por_usuario' && !idUsuarioReporte) {
        alert('Selecciona un usuario para generar su reporte.');
        return;
    }
 
    const usuarioActual = getUser();
    const ahora = new Date();
    const fechaEmision = ahora.toLocaleDateString('es-EC', { day: '2-digit', month: 'long', year: 'numeric' });
    const horaEmision = ahora.toLocaleTimeString('es-EC', { hour: '2-digit', minute: '2-digit' });
    const sufijoReferencia = tipoActivo === 'por_usuario' ? `usuario${idUsuarioReporte}` : tipoActivo.replace(/\s+/g, '');
    const referencia = `RPT-${ahora.getFullYear()}${String(ahora.getMonth() + 1).padStart(2, '0')}${String(ahora.getDate()).padStart(2, '0')}-${sufijoReferencia}`;
 
    const titulo = document.getElementById('tituloTabla').textContent;
    const kpisHtmlPdf = ultimosKpis.map(k => `
        <div class="kpi-pdf-item" style="border-top-color:${k.color};">
            <div class="kpi-numero" style="color:${k.color};">${escaparHtml(String(k.numero))}</div>
            <div class="kpi-etiqueta">${escaparHtml(k.etiqueta)}</div>
        </div>
    `).join('');
 
    let filasTabla = '';
    let columnasTabla = [];
    let distribucion = {}; // para el gráfico de barras
 
    if (tipoActivo === 'usuarios') {
        columnasTabla = ['#', 'Nombre', 'Correo', 'Rol', 'Estado', 'Registrado'];
        filasTabla = todosLosUsuarios.map(u => `
            <tr>
                <td>${u.id}</td>
                <td>${escaparHtml(u.name)} ${escaparHtml(u.apellido)}</td>
                <td>${escaparHtml(u.email)}</td>
                <td>${escaparHtml(u.rol ? u.rol.nombre : 'N/A')}</td>
                <td>${u.activo ? 'Activo' : 'Inactivo'}</td>
                <td>${new Date(u.created_at).toLocaleDateString('es-EC')}</td>
            </tr>`).join('');
 
        // Distribución por rol
        todosLosUsuarios.forEach(u => {
            const r = u.rol ? u.rol.nombre : 'Sin rol';
            distribucion[r] = (distribucion[r] || 0) + 1;
        });
 
    } else {
        let datos;

        if (tipoActivo === 'por_usuario') {
            datos = todasLasIncidencias.filter(i => i.usuario && String(i.usuario.id) === String(idUsuarioReporte));
        } else {
            datos = tipoActivo === 'todas'
                ? todasLasIncidencias
                : todasLasIncidencias.filter(i => (i.estado ? i.estado.nombre : '') === tipoActivo);
        }
 
        columnasTabla = ['#', 'Título', 'Ciudad', 'Tipo', 'Estado', 'Prioridad', 'Reportado por', 'Fecha'];
        filasTabla = datos.map(inc => `
            <tr>
                <td>${inc.id}</td>
                <td>${escaparHtml(inc.titulo)}</td>
                <td>${escaparHtml(inc.ciudad ? inc.ciudad.nombre : 'N/A')}</td>
                <td>${escaparHtml(inc.tipo ? inc.tipo.nombre : 'N/A')}</td>
                <td>${escaparHtml(inc.estado ? inc.estado.nombre : 'N/A')}</td>
                <td>${inc.prioridad}</td>
                <td>${escaparHtml(inc.usuario ? inc.usuario.name : 'N/A')}</td>
                <td>${new Date(inc.created_at).toLocaleDateString('es-EC')}</td>
            </tr>`).join('');
 
        if (datos.length === 0) {
            filasTabla = `<tr><td colspan="${columnasTabla.length}" style="text-align:center; color:#94a3b8; padding:18px;">Sin registros para este reporte.</td></tr>`;
        }
 
        // Distribución por prioridad (más útil que por estado cuando ya está filtrado por estado)
        datos.forEach(inc => {
            distribucion[inc.prioridad] = (distribucion[inc.prioridad] || 0) + 1;
        });
    }
 
    const encabezadoTabla = columnasTabla.map(c => `<th>${c}</th>`).join('');
 
    // ---------- Construcción del gráfico de barras (100% CSS, sin librerías) ----------
    const COLOR_BARRA = {
        'Baja': '#2F7A4D', 'Media': '#C98A2C', 'Alta': '#C9622E', 'Crítica': '#B3413A',
        'Administrador': '#0A1128', 'Responsable': '#16233F', 'Ciudadano': '#C9A961', 'Sin rol': '#94a3b8'
    };
    const totalDistribucion = Object.values(distribucion).reduce((a, b) => a + b, 0);
    const filasGrafico = Object.entries(distribucion)
        .sort((a, b) => b[1] - a[1])
        .map(([etiqueta, valor]) => {
            const porcentaje = totalDistribucion > 0 ? Math.round((valor / totalDistribucion) * 100) : 0;
            const color = COLOR_BARRA[etiqueta] || '#64748b';
            return `
                <div class="chart-fila">
                    <span class="chart-etiqueta">${escaparHtml(etiqueta)}</span>
                    <span class="chart-pista"><span class="chart-relleno" style="width:${porcentaje}%; background:${color};"></span></span>
                    <span class="chart-valor">${valor}</span>
                </div>`;
        }).join('');
 
    const tituloGrafico = tipoActivo === 'usuarios' ? 'Distribución por Rol' : 'Distribución por Prioridad';
 
    const ventana = window.open('', '_blank');
    ventana.document.write(`
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>${escaparHtml(titulo)}</title>
            <style>
                :root {
                    --brand-900: #0A1128;
                    --brand-700: #16233F;
                    --brand-400: #C9A961;
                    --brand-400-suave: #E3CD8F;
                    --texto: #1f2937;
                    --texto-suave: #64748b;
                    --borde: #e2e8f0;
                }
 
                @page { margin: 12mm 10mm; }
                * { box-sizing: border-box; }
 
                html, body {
                    margin: 0;
                    background: #eef1f5;
                }
 
                body {
                    font-family: 'Segoe UI', Arial, sans-serif;
                    color: var(--texto);
                    font-size: 12.5px;
                    line-height: 1.55;
                }
 
                /* ===== Hoja centrada tipo "documento" al verla en pantalla ===== */
                .hoja {
                    position: relative;
                    max-width: 860px;
                    margin: 24px auto;
                    background: #fff;
                    box-shadow: 0 10px 34px rgba(10, 17, 40, 0.16);
                    border-radius: 12px;
                    overflow: hidden;
                }

                /* Marca de agua sutil tipo membrete oficial */
                .marca-agua {
                    position: absolute;
                    top: 48%;
                    left: 50%;
                    transform: translate(-50%, -50%) rotate(-28deg);
                    font-family: Georgia, 'Times New Roman', serif;
                    font-size: 4.6rem;
                    font-weight: 700;
                    letter-spacing: 6px;
                    color: rgba(10, 17, 40, 0.035);
                    white-space: nowrap;
                    pointer-events: none;
                    z-index: 0;
                }

                .encabezado, .contenido, .pie-documento, .no-imprimir {
                    position: relative;
                    z-index: 1;
                }
 
                .franja-superior {
                    height: 7px;
                    background: linear-gradient(90deg, var(--brand-900) 0%, var(--brand-700) 45%, var(--brand-400) 100%);
                }
 
                .encabezado {
                    display: flex;
                    justify-content: space-between;
                    align-items: flex-start;
                    gap: 16px;
                    padding: 20px 32px 16px;
                    border-bottom: 2px solid var(--brand-900);
                    box-shadow: 0 2px 0 var(--brand-400);
                    margin-bottom: 20px;
                    flex-wrap: wrap;
                }
 
                .encabezado .marca {
                    display: flex;
                    align-items: center;
                    gap: 16px;
                    min-width: 0;
                }
 
                .encabezado .logo {
                    height: 46px;
                    width: auto;
                    flex-shrink: 0;
                    object-fit: contain;
                }
 
                .encabezado .institucion {
                    font-size: 0.7rem;
                    text-transform: uppercase;
                    letter-spacing: 0.12em;
                    color: var(--brand-400);
                    font-weight: 700;
                    margin: 0 0 4px;
                }
 
                .encabezado h1 {
                    margin: 0 0 7px;
                    font-family: Georgia, 'Times New Roman', serif;
                    font-size: 1.55rem;
                    font-weight: 700;
                    color: var(--brand-900);
                    line-height: 1.2;
                    letter-spacing: -0.01em;
                }
 
                .encabezado .generado-por {
                    margin: 0;
                    color: #334155;
                    font-size: 0.82rem;
                }
 
                .encabezado .meta-derecha {
                    text-align: right;
                    font-size: 0.76rem;
                    color: #475569;
                    white-space: nowrap;
                    flex-shrink: 0;
                }
 
                .encabezado .referencia {
                    display: inline-block;
                    background: linear-gradient(135deg, var(--brand-900), var(--brand-700));
                    color: #fff;
                    font-weight: 700;
                    letter-spacing: 0.03em;
                    padding: 5px 14px;
                    border-radius: 20px;
                    margin-bottom: 8px;
                    font-size: 0.72rem;
                }
 
                .contenido { padding: 0 32px 8px; }
 
                h2.titulo-seccion {
                    display: flex;
                    align-items: center;
                    gap: 9px;
                    font-size: 0.98rem;
                    font-weight: 700;
                    color: var(--brand-900);
                    border-bottom: 1px solid var(--borde);
                    padding-bottom: 6px;
                    margin: 0 0 12px;
                }

                h2.titulo-seccion::before {
                    content: '';
                    display: inline-block;
                    width: 9px;
                    height: 9px;
                    border-radius: 2px;
                    background: var(--brand-400);
                    flex-shrink: 0;
                }
 
                /* ---------- KPIs ---------- */
                .kpis-pdf {
                    display: flex;
                    gap: 14px;
                    margin-bottom: 20px;
                    flex-wrap: wrap;
                }
 
                .kpi-card, .kpi-icono, .kpi-etiqueta { all: unset; }
 
                .kpi-pdf-item {
                    flex: 1 1 160px;
                    min-width: 140px;
                    position: relative;
                    border: 1px solid var(--borde);
                    border-top: 3px solid var(--borde);
                    border-radius: 10px;
                    padding: 12px 14px 11px;
                    background: linear-gradient(180deg, #fbfcfe 0%, #ffffff 100%);
                    box-shadow: 0 1px 4px rgba(10, 17, 40, 0.05);
                }
 
                .kpi-pdf-item .kpi-numero {
                    font-size: 1.5rem;
                    font-weight: 800;
                    color: var(--brand-900);
                    line-height: 1.15;
                    margin-bottom: 3px;
                }
 
                .kpi-pdf-item .kpi-etiqueta {
                    font-size: 0.66rem;
                    text-transform: uppercase;
                    color: var(--texto-suave);
                    letter-spacing: 0.05em;
                    font-weight: 600;
                }
 
                .kpi-icono, .kpi-card { display: none; }
 
                /* ---------- Gráfico de distribución (barras CSS) ---------- */
                .chart-barras {
                    margin-bottom: 20px;
                    padding: 14px 18px;
                    border: 1px solid var(--borde);
                    border-radius: 10px;
                    background: linear-gradient(180deg, #fbfcfe 0%, #ffffff 100%);
                    box-shadow: 0 1px 4px rgba(10, 17, 40, 0.05);
                }
 
                .chart-fila {
                    display: flex;
                    align-items: center;
                    gap: 12px;
                    margin-bottom: 8px;
                    font-size: 0.78rem;
                }
 
                .chart-fila:last-child { margin-bottom: 0; }
 
                .chart-etiqueta {
                    width: 110px;
                    flex-shrink: 0;
                    color: #334155;
                    font-weight: 600;
                }
 
                .chart-pista {
                    flex: 1;
                    background: #eef1f5;
                    border-radius: 20px;
                    overflow: hidden;
                    height: 15px;
                }
 
                .chart-relleno {
                    position: relative;
                    display: block;
                    height: 100%;
                    border-radius: 20px;
                }

                .chart-relleno::after {
                    content: '';
                    position: absolute;
                    inset: 0;
                    border-radius: inherit;
                    background-image: linear-gradient(180deg, rgba(255,255,255,0.3), rgba(255,255,255,0) 60%);
                }
 
                .chart-valor {
                    width: 32px;
                    text-align: right;
                    font-weight: 800;
                    color: var(--brand-900);
                    flex-shrink: 0;
                }
 
                /* ---------- Tabla ---------- */
                .tabla-wrapper {
                    width: 100%;
                    overflow-x: auto;
                    margin-bottom: 18px;
                    border-radius: 9px;
                    border: 1px solid var(--borde);
                }
 
                table.tabla-reporte {
                    width: 100%;
                    min-width: 620px;
                    border-collapse: collapse;
                    font-size: 0.76rem;
                }
 
                table.tabla-reporte th {
                    background: linear-gradient(135deg, var(--brand-900), var(--brand-700));
                    color: #fff;
                    padding: 8px 10px;
                    text-align: left;
                    font-weight: 600;
                    font-size: 0.66rem;
                    text-transform: uppercase;
                    letter-spacing: 0.05em;
                    white-space: nowrap;
                    border-bottom: 2px solid var(--brand-400);
                }

                table.tabla-reporte td {
                    padding: 6px 10px;
                    border-bottom: 1px solid var(--borde);
                    color: #334155;
                }

                table.tabla-reporte td:first-child {
                    font-weight: 700;
                    color: var(--brand-900);
                }
 
                table.tabla-reporte tr:nth-child(even) td { background: #f7f8fc; }
                table.tabla-reporte tbody tr:last-child td { border-bottom: none; }
 
                .pie-documento {
                    padding: 12px 32px;
                    border-top: 2px solid var(--brand-400);
                    font-size: 0.7rem;
                    color: #94a3b8;
                    display: flex;
                    justify-content: space-between;
                    flex-wrap: wrap;
                    gap: 6px;
                }
 
                .no-imprimir {
                    text-align: center;
                    padding: 18px;
                    background: #f8fafc;
                }
 
                .btn-imprimir {
                    padding: 10px 26px;
                    background: linear-gradient(to bottom, #E3CD8F, #C9A961);
                    border: none;
                    border-radius: 8px;
                    font-weight: 600;
                    color: var(--brand-900);
                    cursor: pointer;
                    font-size: 0.85rem;
                }
 
                /* ===== Responsive (pantallas chicas, antes de imprimir) ===== */
                @media (max-width: 640px) {
                    .hoja { margin: 0; border-radius: 0; box-shadow: none; }
                    .encabezado { padding: 16px 16px 14px; }
                    .encabezado .meta-derecha { text-align: left; }
                    .contenido { padding: 0 16px 4px; }
                    .kpi-pdf-item { flex: 1 1 45%; }
                    .chart-etiqueta { width: 80px; font-size: 0.72rem; }
                    table.tabla-reporte { font-size: 0.7rem; }
                }
 
                /* ===== Impresión: sin sombra, sin fondo gris, ocupa toda la hoja ===== */
                @media print {
                    html, body { background: #fff; }
                    body { font-size: 11.5px; }
                    .hoja { max-width: none; margin: 0; box-shadow: none; border-radius: 0; }
                    .no-imprimir { display: none; }
                    .tabla-wrapper { overflow-x: visible; }
                    table.tabla-reporte { min-width: 0; }

                    /* Reserva espacio abajo para que el contenido nunca quede
                    tapado por el pie de página fijo */
                    .contenido { padding-bottom: 54px; }

                    /* Pie de página fijo: el navegador lo repite automáticamente
                    en cada hoja impresa, en la misma posición. Así nunca queda
                    "huérfano" empujando una hoja extra casi en blanco. */
                    .pie-documento {
                        position: fixed;
                        left: 0;
                        right: 0;
                        bottom: 0;
                        background: #fff;
                        z-index: 5;
                    }

                    /* Fuerza a imprimir colores de fondo aunque el navegador
                    tenga desactivada la opción "Gráficos de fondo" */
                    * {
                        -webkit-print-color-adjust: exact !important;
                        print-color-adjust: exact !important;
                        color-adjust: exact !important;
                    }

                    /* Evita que se corten elementos justo en el salto de página */
                    .kpi-pdf-item,
                    .chart-barras,
                    tr {
                        page-break-inside: avoid;
                        break-inside: avoid;
                    }

                    h2.titulo-seccion {
                        page-break-after: avoid;
                        break-after: avoid;
                    }

                    /* Repite el encabezado de la tabla en cada página nueva */
                    table.tabla-reporte thead {
                        display: table-header-group;
                    }

                    /* Evita que la última fila quede sola arriba de una página nueva */
                    table.tabla-reporte tr {
                        break-after: auto;
                    }
                }
            </style>
        </head>
        <body>
            <div class="hoja">

                <div class="marca-agua">INCIDENCIAS</div>
 
                <div class="franja-superior"></div>
 
                <div class="encabezado">
                    <div class="marca">
                        ${LOGO_URL ? `<img class="logo" src="${LOGO_URL}" alt="Logo" onerror="this.style.display='none'">` : ''}
                        <div>
                            <p class="institucion">Sistema de Gestión de Incidencias Georreferenciadas</p>
                            <h1>${escaparHtml(titulo)}</h1>
                            <p class="generado-por">Generado por ${escaparHtml(usuarioActual.name)} (${escaparHtml(usuarioActual.rol.nombre)})</p>
                        </div>
                    </div>
                    <div class="meta-derecha">
                        <span class="referencia">${referencia}</span><br>
                        Emitido: ${fechaEmision}<br>
                        Hora: ${horaEmision}
                    </div>
                </div>
 
                <div class="contenido">
 
                    <h2 class="titulo-seccion">Resumen</h2>
                    <div class="kpis-pdf">
                        ${kpisHtmlPdf}
                    </div>
 
                    ${totalDistribucion > 0 ? `
                    <h2 class="titulo-seccion">${tituloGrafico}</h2>
                    <div class="chart-barras">
                        ${filasGrafico}
                    </div>` : ''}
 
                    <h2 class="titulo-seccion">Detalle</h2>
                    <div class="tabla-wrapper">
                        <table class="tabla-reporte">
                            <thead><tr>${encabezadoTabla}</tr></thead>
                            <tbody>${filasTabla}</tbody>
                        </table>
                    </div>
 
                </div>
 
                <div class="pie-documento">
                    <span>Sistema de Gestión de Incidencias Georreferenciadas</span>
                    <span>Documento generado automáticamente — ${referencia}</span>
                </div>
 
                <div class="no-imprimir">
                    <button class="btn-imprimir" onclick="window.print()">
                        Imprimir / Guardar como PDF
                    </button>
                </div>
 
            </div>
        </body>
        </html>
    `);
    ventana.document.close();
});

</script>
@endsection