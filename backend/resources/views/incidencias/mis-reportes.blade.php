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
    const hoy = new Date().toLocaleDateString('es-EC', { day: '2-digit', month: 'long', year: 'numeric' });

    const filas = misIncidencias.map(inc => `
        <tr>
            <td>#${inc.id}</td>
            <td>${escaparHtml(inc.titulo)}</td>
            <td>${escaparHtml(inc.ciudad ? inc.ciudad.nombre : 'N/A')}</td>
            <td>${escaparHtml(inc.tipo ? inc.tipo.nombre : 'N/A')}</td>
            <td>${escaparHtml(inc.estado ? inc.estado.nombre : 'N/A')}</td>
            <td>${inc.prioridad}</td>
            <td>${new Date(inc.created_at).toLocaleDateString('es-EC')}</td>
        </tr>
    `).join('');

    const ventana = window.open('', '_blank');
    ventana.document.write(`
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="utf-8">
            <title>Reporte de Incidencias - ${escaparHtml(u.name)}</title>
            <style>
                body { font-family: 'Segoe UI', Arial, sans-serif; color: #212529; margin: 40px; }
                .encabezado { border-bottom: 3px solid #1d4ed8; padding-bottom: 12px; margin-bottom: 20px; }
                .encabezado h1 { margin: 0; font-size: 1.4rem; color: #1e3a8a; }
                .encabezado p { margin: 4px 0 0; font-size: 0.85rem; color: #555; }
                table { width: 100%; border-collapse: collapse; font-size: 0.82rem; }
                th { background: #1e3a8a; color: #fff; padding: 8px; text-align: left; }
                td { padding: 7px 8px; border-bottom: 1px solid #ddd; }
                tr:nth-child(even) td { background: #f4f6fb; }
                .pie { margin-top: 24px; font-size: 0.75rem; color: #888; text-align: center; }
                @media print { .no-imprimir { display: none; } }
            </style>
        </head>
        <body>
            <div class="encabezado">
                <h1>Sistema de Gestión de Incidencias Georreferenciadas</h1>
                <p><strong>Reporte de incidencias del ciudadano:</strong> ${escaparHtml(u.name)} ${escaparHtml(u.apellido || '')} (${escaparHtml(u.email)})</p>
                <p><strong>Fecha de emisión:</strong> ${hoy} &nbsp;|&nbsp; <strong>Total de reportes:</strong> ${misIncidencias.length}</p>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>N.º</th><th>Título</th><th>Ciudad</th><th>Tipo</th>
                        <th>Estado</th><th>Prioridad</th><th>Fecha</th>
                    </tr>
                </thead>
                <tbody>${filas}</tbody>
            </table>

            <div class="pie">
                Documento generado automáticamente por el Sistema de Gestión de Incidencias — UPSE, Carrera de Software.
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