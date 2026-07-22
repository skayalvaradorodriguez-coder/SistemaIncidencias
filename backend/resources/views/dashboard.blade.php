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

    .btn-vista-mapa { font-size: 0.78rem; }
    .btn-vista-mapa.activo { background: #007bff; color: #fff; }

    /* Leyenda del mapa de calor */
    .leyenda-calor {
        display: none;
        align-items: center;
        gap: 10px;
        font-size: 0.82rem;
        margin-top: 10px;
    }
    .barra-calor {
        width: 180px;
        height: 12px;
        border-radius: 6px;
        background: linear-gradient(to right, #0dcaf0, #ffc107, #fd7e14, #dc3545);
    }

    /* Panel solo visible para gestión */
    .solo-gestion { display: none; }

    /* Empareja la altura de las tarjetas de analítica */
    .solo-gestion .info-box {
        min-height: 105px;
        align-items: center;
    }
    .solo-gestion .info-box-text {
        font-size: 0.82rem;
        line-height: 1.2;
    }
    .solo-gestion .info-box .progress {
        margin: 6px 0;
    }
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

    <!-- Segunda fila de indicadores (SOLO GESTIÓN) -->
    <div class="row solo-gestion">

        <div class="col-md-3 col-sm-6">
            <div class="info-box bg-gradient-teal">
                <span class="info-box-icon"><i class="fas fa-percentage"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Tasa de Resolución</span>
                    <span class="info-box-number">{{ $tasaResolucion }}%</span>
                    <div class="progress">
                        <div class="progress-bar" style="width: {{ $tasaResolucion }}%"></div>
                    </div>
                    <span class="progress-description">
                        {{ $resueltas }} de {{ $totalIncidencias }} incidencias
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="info-box bg-gradient-indigo">
                <span class="info-box-icon"><i class="fas fa-stopwatch"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Tiempo Promedio de Resolución</span>
                    <span class="info-box-number">
                        @if($tiempoPromedio <= 0)
                            —
                        @elseif($tiempoPromedio >= 48)
                            {{ round($tiempoPromedio / 24, 1) }} días
                        @else
                            {{ $tiempoPromedio }} h
                        @endif
                    </span>
                    <span class="progress-description">
                        Desde el reporte hasta "Resuelto"
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="info-box bg-gradient-info">
                <span class="info-box-icon"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Usuarios Activos</span>
                    <span class="info-box-number">{{ $totalUsuarios }}</span>
                    <span class="progress-description">
                        Registrados en el sistema
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="info-box bg-gradient-orange">
                <span class="info-box-icon"><i class="fas fa-hourglass-half"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">En Atención</span>
                    <span class="info-box-number">{{ $pendientes + $enProceso }}</span>
                    <span class="progress-description">
                        Pendientes + En Proceso
                    </span>
                </div>
            </div>
        </div>

    </div>

    <!-- Mapa general -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <h3 class="card-title mb-0">
                    <i class="fas fa-map-marked-alt mr-2"></i>
                    Mapa de Incidencias Georreferenciadas
                </h3>
                @if($conUbicacion->count() > 0)
                    <div class="btn-group btn-group-sm mt-1 mt-sm-0" role="group">
                        <button type="button" id="btnMarcadores" class="btn btn-outline-light btn-vista-mapa activo">
                            <i class="fas fa-map-pin mr-1"></i>Marcadores
                        </button>
                        <button type="button" id="btnCalor" class="btn btn-outline-light btn-vista-mapa">
                            <i class="fas fa-fire mr-1"></i>Mapa de calor
                        </button>
                    </div>
                @endif
            </div>
        </div>
        <div class="card-body">
            @if($conUbicacion->count() > 0)
                <div id="mapaGeneral"></div>

                <!-- Leyenda de marcadores -->
                <div class="leyenda-mapa" id="leyendaMarcadores">
                    <span class="leyenda-pendiente">Pendiente</span>
                    <span class="leyenda-proceso">En Proceso</span>
                    <span class="leyenda-resuelto">Resuelto</span>
                    <span class="leyenda-otro">Otro estado</span>
                </div>

                <!-- Leyenda del mapa de calor -->
                <div class="leyenda-calor" id="leyendaCalor">
                    <span>Menor concentración</span>
                    <div class="barra-calor"></div>
                    <span>Mayor concentración</span>
                    <span class="text-muted ml-2" style="font-size:0.78rem;">
                        (las zonas más cálidas tienen más incidencias acumuladas)
                    </span>
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

    <!-- Analítica adicional: tendencia y ciudades (SOLO GESTIÓN) -->
    <div class="row solo-gestion">

        <div class="col-md-7">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line mr-2"></i>
                        Tendencia de Incidencias (últimos 6 meses)
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="graficoTendencia" style="max-height: 280px;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-city mr-2"></i>
                        Top Ciudades con más Incidencias
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="graficoCiudades" style="max-height: 280px;"></canvas>
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
<script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>
<script src="{{ asset('plugins/chart.js/Chart.min.js') }}"></script>
<script>
    // ================== CONTROL POR ROL ==================
    // Muestra los paneles de gestión solo a Administrador y Responsable
    (function () {
        const usuario = getUser();
        const rol = usuario && usuario.rol ? usuario.rol.nombre : null;

        if (rol === 'Administrador' || rol === 'Responsable') {
            document.querySelectorAll('.solo-gestion').forEach(el => {
                el.style.display = 'flex';
            });
        }
    })();

    // ================== MAPA GENERAL ==================
    const incidencias = @json($conUbicacion);

    const coloresEstado = {
        'Pendiente': '#ffc107',
        'En Proceso': '#007bff',
        'Resuelto': '#28a745'
    };

    let mapaGeneral = null;
    let capaMarcadores = null;
    let capaCalor = null;

    if (incidencias.length > 0) {

        mapaGeneral = L.map('mapaGeneral').setView([-2.2276, -80.8585], 11);

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
            });

            marcador.bindPopup(`
                <strong>#${inc.id} - ${inc.titulo}</strong><br>
                Estado: <b style="color:${color}">${estadoNombre}</b><br>
                Prioridad: ${inc.prioridad}<br>
                Tipo: ${inc.tipo ? inc.tipo.nombre : 'N/A'}<br>
                <a href="/incidencias/${inc.id}">Ver detalle →</a>
            `);

            marcadores.push(marcador);
        });

        capaMarcadores = L.featureGroup(marcadores).addTo(mapaGeneral);

        const puntosCalor = incidencias.map(inc => [inc.latitud, inc.longitud, 0.8]);
        capaCalor = L.heatLayer(puntosCalor, {
            radius: 30,
            blur: 20,
            maxZoom: 15,
            gradient: { 0.2: '#0dcaf0', 0.5: '#ffc107', 0.8: '#fd7e14', 1.0: '#dc3545' }
        });

        mapaGeneral.fitBounds(capaMarcadores.getBounds().pad(0.2));

        const btnMarcadores = document.getElementById('btnMarcadores');
        const btnCalor = document.getElementById('btnCalor');
        const leyendaMarc = document.getElementById('leyendaMarcadores');
        const leyendaCal = document.getElementById('leyendaCalor');

        btnMarcadores.addEventListener('click', function () {
            mapaGeneral.removeLayer(capaCalor);
            mapaGeneral.addLayer(capaMarcadores);
            btnMarcadores.classList.add('activo');
            btnCalor.classList.remove('activo');
            leyendaMarc.style.display = 'flex';
            leyendaCal.style.display = 'none';
        });

        btnCalor.addEventListener('click', function () {
            mapaGeneral.removeLayer(capaMarcadores);
            mapaGeneral.addLayer(capaCalor);
            btnCalor.classList.add('activo');
            btnMarcadores.classList.remove('activo');
            leyendaMarc.style.display = 'none';
            leyendaCal.style.display = 'flex';
        });
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

    // ================== GRÁFICO DE TENDENCIA (por mes) ==================
    const porMes = @json($porMes);

    const nombresMes = {
        '01': 'Ene', '02': 'Feb', '03': 'Mar', '04': 'Abr', '05': 'May', '06': 'Jun',
        '07': 'Jul', '08': 'Ago', '09': 'Sep', '10': 'Oct', '11': 'Nov', '12': 'Dic'
    };

    const etiquetasMes = porMes.map(m => {
        const partes = m.mes.split('-');
        return nombresMes[partes[1]] + ' ' + partes[0];
    });

    new Chart(document.getElementById('graficoTendencia'), {
        type: 'line',
        data: {
            labels: etiquetasMes,
            datasets: [{
                label: 'Incidencias registradas',
                data: porMes.map(m => m.total),
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.15)',
                fill: true,
                lineTension: 0.3,
                pointBackgroundColor: '#007bff',
                pointRadius: 5
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

    // ================== GRÁFICO TOP CIUDADES ==================
    const porCiudad = @json($porCiudad);

    new Chart(document.getElementById('graficoCiudades'), {
        type: 'horizontalBar',
        data: {
            labels: porCiudad.map(c => c.nombre),
            datasets: [{
                label: 'Incidencias',
                data: porCiudad.map(c => c.total),
                backgroundColor: ['#dc3545', '#fd7e14', '#ffc107', '#20c997', '#0dcaf0'],
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: { display: false },
            scales: {
                xAxes: [{
                    ticks: {
                        beginAtZero: true,
                        stepSize: 1,
                        fontColor: '#c2c7d0'
                    },
                    gridLines: { color: 'rgba(255,255,255,0.08)' }
                }],
                yAxes: [{
                    ticks: { fontColor: '#c2c7d0' },
                    gridLines: { display: false }
                }]
            }
        }
    });
</script>

@endsection