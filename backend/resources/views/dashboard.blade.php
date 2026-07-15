@extends('layouts.app')

@section('title', 'Dashboard')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    #mapaGeneral { height: 450px; border-radius: 4px; z-index: 1; }

    .leyenda-mapa {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        font-size: 0.85rem;
        margin-top: 10px;
    }

    .leyenda-mapa span::before {
        content: '';
        display: inline-block;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin-right: 6px;
        vertical-align: middle;
    }

    .leyenda-pendiente::before { background: #ffc107; }
    .leyenda-proceso::before   { background: #007bff; }
    .leyenda-resuelto::before  { background: #28a745; }
    .leyenda-otro::before      { background: #6c757d; }

    .small-box { cursor: pointer; }
</style>
@endsection

@section('content')

<div class="container-fluid">

    <h1 class="mb-4">
        Sistema de Gestión de Incidencias
    </h1>

    <!-- Tarjetas de resumen -->
    <div class="row">

        <div class="col-md-3 col-sm-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $totalIncidencias }}</h3>
                    <p>Total Incidencias</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <a href="{{ route('incidencias.index') }}" class="small-box-footer">
                    Ver todas <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $pendientes }}</h3>
                    <p>Pendientes</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
                <a href="{{ route('incidencias.index') }}" class="small-box-footer">
                    Ver detalle <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $enProceso }}</h3>
                    <p>En Proceso</p>
                </div>
                <div class="icon">
                    <i class="fas fa-spinner"></i>
                </div>
                <a href="{{ route('incidencias.index') }}" class="small-box-footer">
                    Ver detalle <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $resueltas }}</h3>
                    <p>Resueltas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <a href="{{ route('incidencias.index') }}" class="small-box-footer">
                    Ver detalle <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

    </div>

    <!-- Mapa general -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-map-marked-alt mr-2"></i>
                Mapa de Incidencias Georreferenciadas
            </h3>
        </div>
        <div class="card-body">
            @if($conUbicacion->count() > 0)
                <div id="mapaGeneral"></div>
                <div class="leyenda-mapa">
                    <span class="leyenda-pendiente">Pendiente</span>
                    <span class="leyenda-proceso">En Proceso</span>
                    <span class="leyenda-resuelto">Resuelto</span>
                    <span class="leyenda-otro">Otro estado</span>
                </div>
            @else
                <p class="text-muted text-center py-4">
                    <i class="fas fa-map-marker-alt d-block mb-2" style="font-size:2rem;"></i>
                    Aún no hay incidencias con ubicación registrada.
                </p>
            @endif
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row">

        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie mr-2"></i>
                        Incidencias por Estado
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="graficoEstados" style="max-height: 280px;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar mr-2"></i>
                        Incidencias por Tipo
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="graficoTipos" style="max-height: 280px;"></canvas>
                </div>
            </div>
        </div>

    </div>

    <!-- Incidencias recientes -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-history mr-2"></i>
                Incidencias Recientes
            </h3>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Título</th>
                        <th>Ciudad</th>
                        <th>Estado</th>
                        <th>Prioridad</th>
                        <th>Reportado por</th>
                        <th>Fecha</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recientes as $inc)
                        <tr>
                            <td>{{ $inc->id }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($inc->titulo, 45) }}</td>
                            <td>{{ $inc->ciudad->nombre ?? 'N/A' }}</td>
                            <td>
                                <span class="badge badge-{{ $inc->estado->color ?? 'secondary' }}">
                                    {{ $inc->estado->nombre ?? 'N/A' }}
                                </span>
                            </td>
                            <td>{{ $inc->prioridad }}</td>
                            <td>{{ $inc->usuario->name ?? 'N/A' }}</td>
                            <td>{{ $inc->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('incidencias.show', $inc->id) }}" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-3">
                                Sin incidencias registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection

@section('scripts')

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="{{ asset('plugins/chart.js/Chart.min.js') }}"></script>
<script>
    // ================== MAPA GENERAL ==================
    const incidencias = @json($conUbicacion);

    const coloresEstado = {
        'Pendiente': '#ffc107',
        'En Proceso': '#007bff',
        'Resuelto': '#28a745'
    };

    if (incidencias.length > 0) {

        const mapaGeneral = L.map('mapaGeneral').setView([-2.2276, -80.8585], 11);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(mapaGeneral);

        const marcadores = [];

        incidencias.forEach(inc => {

            const estadoNombre = inc.estado ? inc.estado.nombre : 'Otro';
            const color = coloresEstado[estadoNombre] || '#6c757d';

            const marcador = L.circleMarker([inc.latitud, inc.longitud], {
                radius: 9,
                fillColor: color,
                color: '#ffffff',
                weight: 2,
                opacity: 1,
                fillOpacity: 0.85
            }).addTo(mapaGeneral);

            marcador.bindPopup(`
                <strong>#${inc.id} - ${inc.titulo}</strong><br>
                Estado: <b style="color:${color}">${estadoNombre}</b><br>
                Prioridad: ${inc.prioridad}<br>
                Tipo: ${inc.tipo ? inc.tipo.nombre : 'N/A'}<br>
                <a href="/incidencias/${inc.id}">Ver detalle →</a>
            `);

            marcadores.push(marcador);
        });

        // Ajusta el zoom para que se vean todos los marcadores
        const grupo = L.featureGroup(marcadores);
        mapaGeneral.fitBounds(grupo.getBounds().pad(0.2));
    }

    // ================== GRÁFICO POR ESTADO ==================
    new Chart(document.getElementById('graficoEstados'), {
        type: 'doughnut',
        data: {
            labels: ['Pendientes', 'En Proceso', 'Resueltas'],
            datasets: [{
                data: [{{ $pendientes }}, {{ $enProceso }}, {{ $resueltas }}],
                backgroundColor: ['#ffc107', '#007bff', '#28a745'],
                borderWidth: 2,
                borderColor: '#343a40'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
                position: 'bottom',
                labels: { fontColor: '#c2c7d0', padding: 15 }
            },
            cutoutPercentage: 60
        }
    });

    // ================== GRÁFICO POR TIPO ==================
    const porTipo = @json($porTipo);

    new Chart(document.getElementById('graficoTipos'), {
        type: 'bar',
        data: {
            labels: porTipo.map(t => t.nombre),
            datasets: [{
                label: 'Incidencias',
                data: porTipo.map(t => t.total),
                backgroundColor: '#17a2b8',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: { display: false },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        stepSize: 1,
                        fontColor: '#c2c7d0'
                    },
                    gridLines: { color: 'rgba(255,255,255,0.08)' }
                }],
                xAxes: [{
                    ticks: { fontColor: '#c2c7d0' },
                    gridLines: { display: false }
                }]
            }
        }
    });
</script>

@endsection