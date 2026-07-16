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

        <a href="{{ route('incidencias.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Reportar Incidencia
        </a>
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

cargarMisReportes();
</script>
@endsection