@extends('layouts.app')

@section('title', 'Mis Reportes')

@section('styles')
<style>
    .reporte-card {
        border-left: 4px solid var(--color-reporte, #6c757d);
    }

    .progreso-etiquetas {
        display: flex;
        justify-content: space-between;
        font-size: 0.72rem;
        opacity: 0.7;
        margin-top: 4px;
    }
</style>
@endsection

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Mis Reportes</h1>

        <div>
            <button type="button" id="btnPdf" class="btn btn-danger mr-2">
                <i class="fas fa-file-pdf"></i> Descargar PDF
            </button>
            <a href="{{ route('incidencias.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Reportar Incidencia
            </a>
        </div>
    </div>

    <p class="text-muted">Aquí puedes ver el avance de todas las incidencias que has reportado.</p>

    <div class="row" id="listaReportes">
        <div class="col-12 text-center py-5">
            <i class="fas fa-spinner fa-spin mr-2"></i>Cargando tus reportes...
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
    'secondary': '#6c757d'
};

const PROGRESO = {
    'Pendiente': 25,
    'En Proceso': 60,
    'Resuelto': 100,
    'Rechazado': 100
};

let misIncidencias = [];

function escaparHtml(texto) {
    const div = document.createElement('div');
    div.textContent = texto ?? '';
    return div.innerHTML;
}

async function cargarMisReportes() {

    const contenedor = document.getElementById('listaReportes');
    const usuario = getUser();

    try {
        const response = await authFetch('/api/incidencias');

        if (!response.ok) {
            contenedor.innerHTML = '<div class="col-12 text-danger">Error al cargar tus reportes.</div>';
            return;
        }

        const todas = await response.json();
        const mias = todas.filter(i => i.usuario_id === usuario.id);

        misIncidencias = mias;

        if (mias.length === 0) {
            contenedor.innerHTML = `
                <div class="col-12 text-center py-5 text-muted">
                    <i class="fas fa-clipboard d-block mb-3" style="font-size:3rem; opacity:0.4;"></i>
                    <p>Aún no has reportado ninguna incidencia.</p>
                    <a href="{{ route('incidencias.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Reportar mi primera incidencia
                    </a>
                </div>`;
            return;
        }

        contenedor.innerHTML = '';

        mias.forEach(inc => {

            const estadoNombre = inc.estado ? inc.estado.nombre : 'Sin estado';
            const color = COLORES[inc.estado ? inc.estado.color : 'secondary'] || '#6c757d';
            const progreso = PROGRESO[estadoNombre] ?? 25;
            const fecha = new Date(inc.created_at).toLocaleDateString('es-EC', { day: '2-digit', month: 'short', year: 'numeric' });

            const barraColor = estadoNombre === 'Rechazado' ? '#dc3545' : color;

            const col = document.createElement('div');
            col.className = 'col-md-6 col-lg-4';

            col.innerHTML = `
                <div class="card reporte-card" style="--color-reporte:${color};">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="mb-0" style="font-size:1rem;">#${inc.id} · ${escaparHtml(inc.titulo)}</h5>
                            <span class="badge" style="background:${color}; color:#fff;">${escaparHtml(estadoNombre)}</span>
                        </div>

                        <p class="text-muted mb-2" style="font-size:0.82rem;">
                            <i class="fas fa-map-marker-alt mr-1"></i>${escaparHtml(inc.ciudad ? inc.ciudad.nombre : 'N/A')}
                            &nbsp;·&nbsp;
                            <i class="far fa-calendar mr-1"></i>${fecha}
                            &nbsp;·&nbsp;
                            Prioridad: ${inc.prioridad}
                        </p>

                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar" style="width:${progreso}%; background:${barraColor};"></div>
                        </div>
                        <div class="progreso-etiquetas">
                            <span>Reportada</span>
                            <span>En atención</span>
                            <span>Finalizada</span>
                        </div>

                        <a href="/incidencias/${inc.id}" class="btn btn-sm btn-outline-info btn-block mt-3">
                            <i class="fas fa-eye mr-1"></i>Ver detalle y seguimiento
                        </a>
                    </div>
                </div>
            `;

            contenedor.appendChild(col);
        });

    } catch (error) {
        contenedor.innerHTML = '<div class="col-12 text-danger">Error de conexión con el servidor.</div>';
    }
}

document.getElementById('btnPdf').addEventListener('click', function () {

    if (misIncidencias.length === 0) {
        alert('No tienes incidencias para incluir en el reporte.');
        return;
    }

    const u = getUser();
    const ahora = new Date();
    const fechaEmision = ahora.toLocaleDateString('es-EC', { day: '2-digit', month: 'long', year: 'numeric' });
    const horaEmision = ahora.toLocaleTimeString('es-EC', { hour: '2-digit', minute: '2-digit' });
    const referencia = `RPT-${ahora.getFullYear()}${String(ahora.getMonth() + 1).padStart(2, '0')}${String(ahora.getDate()).padStart(2, '0')}-${u.id}`;

    // ---------- Resumen ejecutivo: conteos por estado y prioridad ----------
    const conteoEstados = {};
    const conteoPrioridad = { 'Baja': 0, 'Media': 0, 'Alta': 0, 'Crítica': 0 };

    misIncidencias.forEach(inc => {
        const est = inc.estado ? inc.estado.nombre : 'Sin estado';
        conteoEstados[est] = (conteoEstados[est] || 0) + 1;
        if (conteoPrioridad[inc.prioridad] !== undefined) conteoPrioridad[inc.prioridad]++;
    });

    const chipsEstado = Object.entries(conteoEstados).map(([nombre, cantidad]) => `
        <span class="chip">${escaparHtml(nombre)}: <strong>${cantidad}</strong></span>
    `).join('');

    const chipsPrioridad = Object.entries(conteoPrioridad)
        .filter(([, cantidad]) => cantidad > 0)
        .map(([nombre, cantidad]) => `
            <span class="chip chip-prioridad-${nombre.toLowerCase()}">${nombre}: <strong>${cantidad}</strong></span>
        `).join('');

    // ---------- Tabla resumen ----------
    const filas = misIncidencias.map(inc => `
        <tr>
            <td>#${inc.id}</td>
            <td>${escaparHtml(inc.titulo)}</td>
            <td>${escaparHtml(inc.ciudad ? inc.ciudad.nombre : 'N/A')}</td>
            <td>${escaparHtml(inc.tipo ? inc.tipo.nombre : 'N/A')}</td>
            <td>${escaparHtml(inc.estado ? inc.estado.nombre : 'N/A')}</td>
            <td><span class="etiqueta-prioridad etiqueta-${inc.prioridad.toLowerCase()}">${inc.prioridad}</span></td>
            <td>${new Date(inc.created_at).toLocaleDateString('es-EC')}</td>
        </tr>
    `).join('');

    // ---------- Fichas detalladas por incidencia ----------
    const fichas = misIncidencias.map((inc, i) => `
        <div class="ficha">
            <div class="ficha-cabecera">
                <h3>#${inc.id} · ${escaparHtml(inc.titulo)}</h3>
                <span class="etiqueta-prioridad etiqueta-${inc.prioridad.toLowerCase()}">${inc.prioridad}</span>
            </div>
            <table class="ficha-datos">
                <tr>
                    <td><strong>Estado</strong></td>
                    <td>${escaparHtml(inc.estado ? inc.estado.nombre : 'N/A')}</td>
                    <td><strong>Fecha de reporte</strong></td>
                    <td>${new Date(inc.created_at).toLocaleDateString('es-EC')}</td>
                </tr>
                <tr>
                    <td><strong>Ciudad</strong></td>
                    <td>${escaparHtml(inc.ciudad ? inc.ciudad.nombre : 'N/A')}</td>
                    <td><strong>Tipo / Subtipo</strong></td>
                    <td>${escaparHtml(inc.tipo ? inc.tipo.nombre : 'N/A')}${inc.subtipo ? ' / ' + escaparHtml(inc.subtipo.nombre) : ''}</td>
                </tr>
                <tr>
                    <td><strong>Dirección</strong></td>
                    <td colspan="3">${escaparHtml(inc.direccion || 'No especificada')}</td>
                </tr>
            </table>
            <p class="ficha-descripcion"><strong>Descripción:</strong> ${escaparHtml(inc.descripcion || 'Sin descripción registrada.')}</p>
        </div>
        ${i < misIncidencias.length - 1 ? '<div class="separador"></div>' : ''}
    `).join('');

    const ventana = window.open('', '_blank');
    ventana.document.write(`
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="utf-8">
            <title>Reporte de Incidencias - ${escaparHtml(u.name)}</title>
            <style>
                @page { margin: 22mm 16mm; }

                * { box-sizing: border-box; }

                body {
                    font-family: 'Segoe UI', Arial, sans-serif;
                    color: #1f2937;
                    margin: 0;
                    font-size: 13px;
                    line-height: 1.5;
                }

                /* ---------- Encabezado / letterhead ---------- */
                .franja-superior {
                    height: 6px;
                    background: linear-gradient(90deg, #1e3a8a, #1d4ed8, #3b82f6);
                }

                .encabezado {
                    display: flex;
                    justify-content: space-between;
                    align-items: flex-start;
                    padding: 18px 0 14px;
                    border-bottom: 2px solid #1e3a8a;
                    margin-bottom: 18px;
                }

                .encabezado .institucion {
                    font-size: 0.72rem;
                    text-transform: uppercase;
                    letter-spacing: 0.06em;
                    color: #64748b;
                    margin: 0 0 2px;
                }

                .encabezado h1 {
                    margin: 0 0 6px;
                    font-size: 1.35rem;
                    color: #1e3a8a;
                }

                .encabezado .subtitulo {
                    margin: 0;
                    font-size: 0.88rem;
                    color: #334155;
                }

                .encabezado .meta-derecha {
                    text-align: right;
                    font-size: 0.76rem;
                    color: #475569;
                    white-space: nowrap;
                }

                .encabezado .meta-derecha .referencia {
                    display: inline-block;
                    background: #eef2ff;
                    color: #1e3a8a;
                    font-weight: 600;
                    padding: 3px 10px;
                    border-radius: 4px;
                    margin-bottom: 6px;
                    font-size: 0.75rem;
                }

                /* ---------- Resumen ejecutivo ---------- */
                .resumen {
                    background: #f8fafc;
                    border: 1px solid #e2e8f0;
                    border-left: 4px solid #1d4ed8;
                    border-radius: 6px;
                    padding: 14px 18px;
                    margin-bottom: 22px;
                }

                .resumen h2 {
                    margin: 0 0 10px;
                    font-size: 0.95rem;
                    color: #1e3a8a;
                }

                .resumen .total-destacado {
                    font-size: 1.6rem;
                    font-weight: 700;
                    color: #1e3a8a;
                }

                .resumen .total-destacado small {
                    display: block;
                    font-size: 0.7rem;
                    font-weight: 400;
                    color: #64748b;
                    text-transform: uppercase;
                }

                .resumen-grid { display: flex; gap: 24px; align-items: flex-start; flex-wrap: wrap; }
                .resumen-bloque { flex: 1; min-width: 200px; }
                .resumen-bloque p { margin: 0 0 6px; font-size: 0.72rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em; }

                .chip {
                    display: inline-block;
                    background: #fff;
                    border: 1px solid #cbd5e1;
                    border-radius: 20px;
                    padding: 3px 10px;
                    margin: 0 6px 6px 0;
                    font-size: 0.74rem;
                    color: #334155;
                }

                .chip-prioridad-baja { border-color: #86efac; color: #166534; }
                .chip-prioridad-media { border-color: #fde68a; color: #92400e; }
                .chip-prioridad-alta { border-color: #fdba74; color: #9a3412; }
                .chip-prioridad-crítica { border-color: #fca5a5; color: #991b1b; }

                /* ---------- Tabla resumen ---------- */
                h2.titulo-seccion {
                    font-size: 0.95rem;
                    color: #1e3a8a;
                    border-bottom: 1px solid #e2e8f0;
                    padding-bottom: 6px;
                    margin: 26px 0 10px;
                }

                table.tabla-resumen { width: 100%; border-collapse: collapse; font-size: 0.78rem; }
                table.tabla-resumen th {
                    background: #1e3a8a; color: #fff; padding: 8px; text-align: left;
                    font-weight: 600; font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.03em;
                }
                table.tabla-resumen td { padding: 7px 8px; border-bottom: 1px solid #e2e8f0; }
                table.tabla-resumen tr:nth-child(even) td { background: #f8fafc; }

                .etiqueta-prioridad {
                    display: inline-block;
                    padding: 2px 9px;
                    border-radius: 10px;
                    font-size: 0.7rem;
                    font-weight: 600;
                    color: #fff;
                }
                .etiqueta-baja { background: #22c55e; }
                .etiqueta-media { background: #eab308; }
                .etiqueta-alta { background: #f97316; }
                .etiqueta-crítica { background: #dc2626; }

                /* ---------- Fichas detalladas ---------- */
                .ficha { margin-bottom: 14px; page-break-inside: avoid; }
                .ficha-cabecera {
                    display: flex; justify-content: space-between; align-items: center;
                    margin-bottom: 8px;
                }
                .ficha-cabecera h3 { margin: 0; font-size: 0.92rem; color: #1e293b; }

                table.ficha-datos { width: 100%; border-collapse: collapse; font-size: 0.76rem; margin-bottom: 8px; }
                table.ficha-datos td { padding: 4px 6px; vertical-align: top; color: #334155; }
                table.ficha-datos td:nth-child(1), table.ficha-datos td:nth-child(3) { width: 110px; color: #64748b; }

                .ficha-descripcion { font-size: 0.8rem; color: #334155; margin: 0; background: #f8fafc; padding: 8px 10px; border-radius: 4px; }

                .separador { border-top: 1px dashed #cbd5e1; margin: 14px 0; }

                /* ---------- Pie de página ---------- */
                .pie {
                    margin-top: 30px;
                    padding-top: 10px;
                    border-top: 1px solid #e2e8f0;
                    font-size: 0.68rem;
                    color: #94a3b8;
                    text-align: center;
                }

                @media print {
                    .no-imprimir { display: none; }
                    .ficha { page-break-inside: avoid; }
                }
            </style>
        </head>
        <body>
            <div class="franja-superior"></div>

            <div class="encabezado">
                <div>
                    <p class="institucion">UPSE · Carrera de Software</p>
                    <h1>Sistema de Gestión de Incidencias Georreferenciadas</h1>
                    <p class="subtitulo">Reporte de incidencias reportadas por: <strong>${escaparHtml(u.name)} ${escaparHtml(u.apellido || '')}</strong> (${escaparHtml(u.email)})</p>
                </div>
                <div class="meta-derecha">
                    <span class="referencia">${referencia}</span><br>
                    Emitido el ${fechaEmision}<br>
                    a las ${horaEmision}
                </div>
            </div>

            <div class="resumen">
                <h2>Resumen ejecutivo</h2>
                <div class="resumen-grid">
                    <div>
                        <div class="total-destacado">${misIncidencias.length}<small>Incidencias reportadas</small></div>
                    </div>
                    <div class="resumen-bloque">
                        <p>Por estado</p>
                        ${chipsEstado}
                    </div>
                    <div class="resumen-bloque">
                        <p>Por prioridad</p>
                        ${chipsPrioridad}
                    </div>
                </div>
            </div>

            <h2 class="titulo-seccion">Listado general</h2>
            <table class="tabla-resumen">
                <thead>
                    <tr>
                        <th>N.º</th><th>Título</th><th>Ciudad</th><th>Tipo</th>
                        <th>Estado</th><th>Prioridad</th><th>Fecha</th>
                    </tr>
                </thead>
                <tbody>${filas}</tbody>
            </table>

            <h2 class="titulo-seccion">Detalle de cada incidencia</h2>
            ${fichas}

            <div class="pie">
                Documento generado automáticamente por el Sistema de Gestión de Incidencias Georreferenciadas — UPSE, Carrera de Software.<br>
                Referencia del documento: ${referencia} &nbsp;|&nbsp; Este reporte refleja el estado de las incidencias al momento de su emisión.
            </div>

            <script>window.onload = () => window.print();<\/script>
        </body>
        </html>
    `);
    ventana.document.close();
});

cargarMisReportes();
</script>
@endsection