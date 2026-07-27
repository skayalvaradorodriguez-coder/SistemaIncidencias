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

    .leyenda-pendiente::before { background: #C9A961; }
    .leyenda-proceso::before   { background: #16233F; }
    .leyenda-resuelto::before  { background: #2F7A4D; }
    .leyenda-otro::before      { background: #6c757d; }

    .small-box { cursor: pointer; }

    .btn-vista-mapa { font-size: 0.78rem; }
    .btn-vista-mapa.activo { background: #C9A961; color: #0A1128; border-color: #A9863F; }

    /* Filtros del mapa general */
    .filtros-mapa {
        gap: 12px;
        border-top: 1px solid var(--border-subtle, rgba(255,255,255,0.08));
        padding-top: 12px;
    }
    .filtro-item {
        display: flex;
        flex-direction: column;
        min-width: 150px;
    }
    .filtro-item label {
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: .03em;
        margin-bottom: 3px;
        color: var(--text-muted, #9ca3af);
    }
    .filtro-contador { font-size: 0.8rem; align-self: center; }

    /* Botón Limpiar filtros: alineado con los selects (no debe heredar flex-column) */
    .btn-limpiar-filtros {
        height: calc(1.8125rem + 2px); /* misma altura que .form-control-sm */
        padding: 0 12px;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        border: 1px solid #C9A961;
        color: #C9A961;
        background: transparent;
        border-radius: 4px;
        font-size: 0.8rem;
        transition: background-color .15s ease, color .15s ease;
    }
    .btn-limpiar-filtros:hover,
    .btn-limpiar-filtros:focus {
        background: #C9A961;
        color: #0A1128;
        border-color: #A9863F;
    }

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

    /* Panel solo visible para Administrador (Responsable ya no lo ve: solo mapa + historial) */
    .solo-admin { display: none; }

    /* Empareja la altura de las tarjetas de analítica */
    .solo-admin .info-box {
        min-height: 105px;
        align-items: center;
    }
    .solo-admin .info-box-text {
        font-size: 0.82rem;
        line-height: 1.2;
    }
    .solo-admin .info-box .progress {
        margin: 6px 0;
    }

        /* ===== Tarjetas de analítica (info-box) con paleta elite ===== */
    .solo-admin .bg-gradient-teal   { background: linear-gradient(135deg, #2F7A4D, #1F5636) !important; }
    .solo-admin .bg-gradient-indigo { background: linear-gradient(135deg, #1E2E52, #0A1128) !important; }
    .solo-admin .bg-gradient-info   { background: linear-gradient(135deg, #16233F, #101A33) !important; }
    .solo-admin .bg-gradient-orange { background: linear-gradient(135deg, #E3CD8F, #C9A961) !important; color: #0A1128 !important; }
    .solo-admin .bg-gradient-orange .info-box-icon,
    .solo-admin .bg-gradient-orange .info-box-content * { color: #0A1128 !important; }
    .solo-admin .info-box .progress-bar { background-color: #C9A961; }
</style>
@endsection

@section('content')

<div class="container-fluid">

    <h1 class="mb-4">
        Sistema de Gestión de Incidencias
    </h1>

    <!-- ===== Dashboard PERSONAL del Ciudadano ===== -->
    <div id="dashboardCiudadano" style="display:none;">

        <!-- Accesos rápidos -->
        <div class="row mb-1">
            <div class="col-md-6 mb-3">
                <a href="{{ route('incidencias.create') }}" class="card bg-primary text-white h-100 d-block" style="text-decoration:none;">
                    <div class="card-body d-flex align-items-center">
                        <i class="fas fa-plus-circle fa-2x mr-3"></i>
                        <div>
                            <h5 class="mb-0">Reportar nueva incidencia</h5>
                            <small>Registra un problema en tu sector</small>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6 mb-3">
                <a href="{{ route('emergencias') }}" class="card bg-danger text-white h-100 d-block" style="text-decoration:none;">
                    <div class="card-body d-flex align-items-center">
                        <i class="fas fa-phone-alt fa-2x mr-3"></i>
                        <div>
                            <h5 class="mb-0">Contactos de emergencia</h5>
                            <small>ECU 911, Policía, Bomberos y más</small>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Tarjetas resumen (SOLO de sus propios reportes) -->
        <div class="row">

            <div class="col-md-3 col-sm-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3 id="mioTotal">-</h3>
                        <p>Mis Reportes</p>
                    </div>
                    <div class="icon"><i class="fas fa-clipboard-list"></i></div>
                    <a href="{{ route('incidencias.mis') }}" class="small-box-footer">
                        Ver todos <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3 id="mioPendientes">-</h3>
                        <p>Pendientes</p>
                    </div>
                    <div class="icon"><i class="fas fa-clock"></i></div>
                    <a href="{{ route('incidencias.mis') }}" class="small-box-footer">
                        Ver detalle <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3 id="mioEnProceso">-</h3>
                        <p>En Proceso</p>
                    </div>
                    <div class="icon"><i class="fas fa-spinner"></i></div>
                    <a href="{{ route('incidencias.mis') }}" class="small-box-footer">
                        Ver detalle <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3 id="mioResueltas">-</h3>
                        <p>Resueltas</p>
                    </div>
                    <div class="icon"><i class="fas fa-check-circle"></i></div>
                    <a href="{{ route('incidencias.mis') }}" class="small-box-footer">
                        Ver detalle <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

        </div>

        <!-- Mapa de MIS incidencias -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title mb-0">
                    <i class="fas fa-map-marked-alt mr-2"></i>
                    Mapa de mis incidencias
                </h3>
            </div>
            <div class="card-body">
                <div id="mapaMio" style="height: 350px; border-radius: 4px; z-index: 1;"></div>
                <p id="mapaMioVacio" class="text-muted text-center py-4 mb-0" style="display:none;">
                    <i class="fas fa-map-marker-alt d-block mb-2" style="font-size:2rem;"></i>
                    Aún no tienes incidencias con ubicación registrada.
                </p>
            </div>
        </div>

        <!-- Historial de MIS incidencias -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-history mr-2"></i>
                    Mis incidencias recientes
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
                            <th>Fecha</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="tablaMisRecientes">
                        <tr><td colspan="7" class="text-center text-muted py-3">Cargando...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    <!-- ===== Fin Dashboard personal del Ciudadano ===== -->

    <!-- Tarjetas de resumen (SOLO ADMINISTRADOR) -->
    <div class="row solo-admin">

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

    <!-- Segunda fila de indicadores (SOLO ADMINISTRADOR) -->
    <div class="row solo-admin">

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
    <div class="card" id="cardMapaGeneral">
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

            @if($conUbicacion->count() > 0)
            <!-- Filtros del mapa -->
            <div class="filtros-mapa mt-3 d-flex flex-wrap align-items-end">
                <div class="filtro-item">
                    <label for="filtroEstado">Estado</label>
                    <select id="filtroEstado" class="form-control form-control-sm">
                        <option value="">Todos</option>
                        <option value="Pendiente">Pendiente</option>
                        <option value="En Proceso">En Proceso</option>
                        <option value="Resuelto">Resuelto</option>
                        <option value="Rechazado">Rechazado</option>
                    </select>
                </div>

                <div class="filtro-item">
                    <label for="filtroTipo">Tipo</label>
                    <select id="filtroTipo" class="form-control form-control-sm">
                        <option value="">Todos</option>
                    </select>
                </div>

                <div class="filtro-item">
                    <label for="filtroProvincia">Provincia</label>
                    <select id="filtroProvincia" class="form-control form-control-sm">
                        <option value="">Todas</option>
                    </select>
                </div>

                <div class="filtro-item">
                    <label for="filtroCiudad">Ciudad</label>
                    <select id="filtroCiudad" class="form-control form-control-sm">
                        <option value="">Todas</option>
                    </select>
                </div>

                <div class="filtro-item">
                    <label for="filtroPrioridad">Prioridad</label>
                    <select id="filtroPrioridad" class="form-control form-control-sm">
                        <option value="">Todas</option>
                        <option value="Baja">Baja</option>
                        <option value="Media">Media</option>
                        <option value="Alta">Alta</option>
                        <option value="Crítica">Crítica</option>
                    </select>
                </div>

                <button type="button" id="btnLimpiarFiltros" class="btn btn-sm btn-limpiar-filtros">
                    <i class="fas fa-times mr-1"></i>Limpiar
                </button>

                <span class="filtro-contador ml-auto text-muted" id="filtroContador"></span>
            </div>
            @endif
        </div>
        <div class="card-body">
            @if($conUbicacion->count() > 0)
                <div id="mapaGeneral"></div>

                <p id="mapaGeneralVacio" class="text-muted text-center py-4 mb-0" style="display:none;">
                    <i class="fas fa-filter d-block mb-2" style="font-size:2rem;"></i>
                    Ninguna incidencia coincide con los filtros seleccionados.
                </p>

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

    <!-- Gráficos (SOLO ADMINISTRADOR) -->
    <div class="row solo-admin">

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

    <!-- Analítica adicional: tendencia (SOLO ADMINISTRADOR) -->
    <div class="row solo-admin">

        <div class="col-md-12">
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

    </div>

    <!-- Analítica adicional: ciudades y provincias (SOLO ADMINISTRADOR) -->
    <div class="row solo-admin">

        <div class="col-md-6">
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

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-map mr-2"></i>
                        Top Provincias con más Incidencias
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="graficoProvincias" style="max-height: 280px;"></canvas>
                </div>
            </div>
        </div>

    </div>

    <!-- Incidencias recientes -->
    <div class="card" id="cardHistorialGeneral">
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
    (function () {
        const usuario = getUser();
        const rol = usuario && usuario.rol ? usuario.rol.nombre : null;

        if (rol === 'Ciudadano') {
            // Oculta TODO lo global (tarjetas, gráficos, mapa y tabla de todos)
            document.getElementById('cardMapaGeneral')?.remove();
            document.getElementById('cardHistorialGeneral')?.remove();

            // Muestra su panel personal y carga sus datos
            document.getElementById('dashboardCiudadano').style.display = 'block';
            cargarDashboardCiudadano();
            return;
        }

        // Solo el Administrador ve las tarjetas y gráficos completos.
        // El Responsable solo ve el mapa y el historial de incidencias.
        if (rol === 'Administrador') {
            document.querySelectorAll('.solo-admin').forEach(el => {
                el.style.display = 'flex';
            });
        }
    })();

    // ================== DASHBOARD PERSONAL (Ciudadano) ==================
    async function cargarDashboardCiudadano() {
        try {
            const response = await authFetch('/api/dashboard/mias');
            if (!response.ok) return;

            const datos = await response.json();

            document.getElementById('mioTotal').textContent = datos.total;
            document.getElementById('mioPendientes').textContent = datos.pendientes;
            document.getElementById('mioEnProceso').textContent = datos.en_proceso;
            document.getElementById('mioResueltas').textContent = datos.resueltas;

            // ---- Tabla de sus incidencias recientes ----
            const tbody = document.getElementById('tablaMisRecientes');

            if (datos.recientes.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-3">Aún no has reportado ninguna incidencia.</td></tr>';
            } else {
                tbody.innerHTML = '';
                datos.recientes.forEach(inc => {
                    const fecha = new Date(inc.created_at).toLocaleDateString('es-EC');
                    const fila = document.createElement('tr');
                    fila.innerHTML = `
                        <td>${inc.id}</td>
                        <td>${inc.titulo}</td>
                        <td>${inc.ciudad ? inc.ciudad.nombre : 'N/A'}</td>
                        <td><span class="badge badge-${inc.estado ? inc.estado.color : 'secondary'}">${inc.estado ? inc.estado.nombre : 'N/A'}</span></td>
                        <td>${inc.prioridad}</td>
                        <td>${fecha}</td>
                        <td><a href="/incidencias/${inc.id}" class="btn btn-sm btn-outline-info"><i class="fas fa-eye"></i></a></td>
                    `;
                    tbody.appendChild(fila);
                });
            }

            // ---- Mapa de sus incidencias ----
            const ubicaciones = datos.con_ubicacion;

            if (ubicaciones.length === 0) {
                document.getElementById('mapaMioVacio').style.display = 'block';
                return;
            }

            const coloresEstadoMio = { 'Pendiente': '#C9A961', 'En Proceso': '#16233F', 'Resuelto': '#2F7A4D' };

            const mapaMio = L.map('mapaMio').setView([-2.2276, -80.8585], 11);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap'
            }).addTo(mapaMio);

            const marcadoresMio = ubicaciones.map(inc => {
                const nombreEstado = inc.estado ? inc.estado.nombre : 'Otro';
                const color = coloresEstadoMio[nombreEstado] || '#6c757d';

                return L.circleMarker([inc.latitud, inc.longitud], {
                    radius: 9, fillColor: color, color: '#ffffff', weight: 2, opacity: 1, fillOpacity: 0.85
                }).bindPopup(`
                    <strong>#${inc.id} - ${inc.titulo}</strong><br>
                    Estado: <b style="color:${color}">${nombreEstado}</b><br>
                    Prioridad: ${inc.prioridad}<br>
                    <a href="/incidencias/${inc.id}">Ver detalle →</a>
                `);
            });

            const grupoMio = L.featureGroup(marcadoresMio).addTo(mapaMio);
            mapaMio.fitBounds(grupoMio.getBounds().pad(0.2));

        } catch (error) {
            console.log(error);
        }
    }

    // ================== COLORES DEL TEMA ACTIVO ==================
    // Se leen de las variables CSS (--text-muted / --border-subtle) para que
    // las gráficas se vean bien tanto en modo oscuro como en modo claro.
    const temaVars = getComputedStyle(document.documentElement);
    const colorTexto = (temaVars.getPropertyValue('--text-muted') || '#9ca3af').trim();
    const colorGrid = (temaVars.getPropertyValue('--border-subtle') || 'rgba(255,255,255,0.08)').trim();

    // Paleta elite para las gráficas
    const dorado = '#C9A961';
    const doradoClaro = '#E3CD8F';
    const doradoOscuro = '#A9863F';
    const navy = '#16233F';
    const navyOscuro = '#0A1128';
    const verde = '#2F7A4D';

    // ================== MAPA GENERAL ==================
    const incidencias = @json($conUbicacion);

    const coloresEstado = {
        'Pendiente': dorado,
        'En Proceso': navy,
        'Resuelto': verde,
        'Rechazado': '#dc3545'
    };

    let mapaGeneral = null;
    let capaMarcadores = null;
    let capaCalor = null;
    let vistaActiva = 'marcadores'; // 'marcadores' | 'calor'

    if (incidencias.length > 0) {

        mapaGeneral = L.map('mapaGeneral').setView([-2.2276, -80.8585], 11);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(mapaGeneral);

        // ---- Poblar el filtro de Tipo dinámicamente a partir de los datos reales ----
        const selectTipo = document.getElementById('filtroTipo');
        const tiposUnicos = [...new Set(
            incidencias.map(inc => inc.tipo ? inc.tipo.nombre : null).filter(Boolean)
        )].sort();

        tiposUnicos.forEach(nombreTipo => {
            const opt = document.createElement('option');
            opt.value = nombreTipo;
            opt.textContent = nombreTipo;
            selectTipo.appendChild(opt);
        });

        // ---- Poblar el filtro de Provincia dinámicamente a partir de los datos reales ----
        const selectProvincia = document.getElementById('filtroProvincia');
        const provinciasUnicas = [...new Set(
            incidencias.map(inc => (inc.ciudad && inc.ciudad.provincia) ? inc.ciudad.provincia.nombre : null).filter(Boolean)
        )].sort();

        provinciasUnicas.forEach(nombreProvincia => {
            const opt = document.createElement('option');
            opt.value = nombreProvincia;
            opt.textContent = nombreProvincia;
            selectProvincia.appendChild(opt);
        });

        // ---- Filtro de Ciudad en cascada: depende de la Provincia seleccionada ----
        const selectCiudad = document.getElementById('filtroCiudad');

        function ciudadesDe(nombreProvincia) {
            const ciudades = incidencias
                .filter(inc => inc.ciudad && (!nombreProvincia || (inc.ciudad.provincia && inc.ciudad.provincia.nombre === nombreProvincia)))
                .map(inc => inc.ciudad.nombre);
            return [...new Set(ciudades)].sort();
        }

        function actualizarCiudades(nombreProvincia) {
            const seleccionActual = selectCiudad.value;
            selectCiudad.innerHTML = '<option value="">Todas</option>';

            ciudadesDe(nombreProvincia).forEach(nombreCiudad => {
                const opt = document.createElement('option');
                opt.value = nombreCiudad;
                opt.textContent = nombreCiudad;
                selectCiudad.appendChild(opt);
            });

            // Conserva la ciudad elegida si sigue siendo válida para la nueva provincia
            const sigueValida = [...selectCiudad.options].some(o => o.value === seleccionActual);
            selectCiudad.value = sigueValida ? seleccionActual : '';
        }

        // Poblado inicial de Ciudad (todas, sin provincia seleccionada aún)
        actualizarCiudades('');

        selectProvincia.addEventListener('change', function () {
            actualizarCiudades(selectProvincia.value);
            aplicarFiltros();
        });

        // ---- Poblar el filtro de Tipo dinámicamente a partir de los datos reales ----
        // (declarado más arriba)

        // ---- Dibuja (o redibuja) las capas del mapa con el subconjunto recibido ----
        function dibujarMapa(lista) {
            if (capaMarcadores) { mapaGeneral.removeLayer(capaMarcadores); capaMarcadores = null; }
            if (capaCalor) { mapaGeneral.removeLayer(capaCalor); capaCalor = null; }

            const mapaVacio = document.getElementById('mapaGeneralVacio');
            const contenedorMapa = document.getElementById('mapaGeneral');

            if (lista.length === 0) {
                mapaVacio.style.display = 'block';
                contenedorMapa.style.display = 'none';
                return;
            }

            mapaVacio.style.display = 'none';
            contenedorMapa.style.display = 'block';
            mapaGeneral.invalidateSize();

            const marcadores = lista.map(inc => {
                const estadoNombre = inc.estado ? inc.estado.nombre : 'Otro';
                const color = coloresEstado[estadoNombre] || '#6c757d';

                return L.circleMarker([inc.latitud, inc.longitud], {
                    radius: 9,
                    fillColor: color,
                    color: '#ffffff',
                    weight: 2,
                    opacity: 1,
                    fillOpacity: 0.85
                }).bindPopup(`
                    <strong>#${inc.id} - ${inc.titulo}</strong><br>
                    Estado: <b style="color:${color}">${estadoNombre}</b><br>
                    Prioridad: ${inc.prioridad}<br>
                    Tipo: ${inc.tipo ? inc.tipo.nombre : 'N/A'}<br>
                    Ciudad: ${inc.ciudad ? inc.ciudad.nombre : 'N/A'}<br>
                    Provincia: ${(inc.ciudad && inc.ciudad.provincia) ? inc.ciudad.provincia.nombre : 'N/A'}<br>
                    <a href="/incidencias/${inc.id}">Ver detalle →</a>
                `);
            });

            capaMarcadores = L.featureGroup(marcadores);

            const puntosCalor = lista.map(inc => [inc.latitud, inc.longitud, 0.8]);
            capaCalor = L.heatLayer(puntosCalor, {
                radius: 30,
                blur: 20,
                maxZoom: 15,
                gradient: { 0.2: '#16233F', 0.5: '#C9A961', 0.8: '#E3CD8F', 1.0: '#dc3545' }
            });

            mapaGeneral.addLayer(vistaActiva === 'calor' ? capaCalor : capaMarcadores);
            mapaGeneral.fitBounds(capaMarcadores.getBounds().pad(0.2));
        }

        // ---- Lee los selects, filtra el dataset completo y vuelve a dibujar ----
        function aplicarFiltros() {
            const estado = document.getElementById('filtroEstado').value;
            const tipo = document.getElementById('filtroTipo').value;
            const ciudad = document.getElementById('filtroCiudad').value;
            const provincia = document.getElementById('filtroProvincia').value;
            const prioridad = document.getElementById('filtroPrioridad').value;

            const filtradas = incidencias.filter(inc => {
                const coincideEstado = !estado || (inc.estado && inc.estado.nombre === estado);
                const coincideTipo = !tipo || (inc.tipo && inc.tipo.nombre === tipo);
                const coincideCiudad = !ciudad || (inc.ciudad && inc.ciudad.nombre === ciudad);
                const coincideProvincia = !provincia || (inc.ciudad && inc.ciudad.provincia && inc.ciudad.provincia.nombre === provincia);
                const coincidePrioridad = !prioridad || inc.prioridad === prioridad;
                return coincideEstado && coincideTipo && coincideCiudad && coincideProvincia && coincidePrioridad;
            });

            document.getElementById('filtroContador').textContent =
                `${filtradas.length} de ${incidencias.length} incidencias`;

            dibujarMapa(filtradas);
        }

        document.getElementById('filtroEstado').addEventListener('change', aplicarFiltros);
        document.getElementById('filtroTipo').addEventListener('change', aplicarFiltros);
        document.getElementById('filtroCiudad').addEventListener('change', aplicarFiltros);
        document.getElementById('filtroPrioridad').addEventListener('change', aplicarFiltros);

        document.getElementById('btnLimpiarFiltros').addEventListener('click', function () {
            document.getElementById('filtroEstado').value = '';
            document.getElementById('filtroTipo').value = '';
            document.getElementById('filtroProvincia').value = '';
            actualizarCiudades('');
            document.getElementById('filtroPrioridad').value = '';
            aplicarFiltros();
        });

        // Dibujo inicial (sin filtros aplicados)
        aplicarFiltros();

        // ---- Toggle Marcadores / Mapa de calor (respeta el filtro activo) ----
        const btnMarcadores = document.getElementById('btnMarcadores');
        const btnCalor = document.getElementById('btnCalor');
        const leyendaMarc = document.getElementById('leyendaMarcadores');
        const leyendaCal = document.getElementById('leyendaCalor');

        btnMarcadores.addEventListener('click', function () {
            vistaActiva = 'marcadores';
            if (capaCalor) mapaGeneral.removeLayer(capaCalor);
            if (capaMarcadores) mapaGeneral.addLayer(capaMarcadores);
            btnMarcadores.classList.add('activo');
            btnCalor.classList.remove('activo');
            leyendaMarc.style.display = 'flex';
            leyendaCal.style.display = 'none';
        });

        btnCalor.addEventListener('click', function () {
            vistaActiva = 'calor';
            if (capaMarcadores) mapaGeneral.removeLayer(capaMarcadores);
            if (capaCalor) mapaGeneral.addLayer(capaCalor);
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
                backgroundColor: [dorado, navy, verde],
                borderWidth: 2,
                borderColor: 'rgba(0,0,0,0.05)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
                position: 'bottom',
                labels: { fontColor: colorTexto, padding: 15 }
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
                backgroundColor: dorado,
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
                        fontColor: colorTexto
                    },
                    gridLines: { color: colorGrid }
                }],
                xAxes: [{
                    ticks: { fontColor: colorTexto },
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
                borderColor: dorado,
                backgroundColor: 'rgba(201, 169, 97, 0.15)',
                fill: true,
                lineTension: 0.3,
                pointBackgroundColor: dorado,
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
                        fontColor: colorTexto
                    },
                    gridLines: { color: colorGrid }
                }],
                xAxes: [{
                    ticks: { fontColor: colorTexto },
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
                backgroundColor: [doradoClaro, dorado, doradoOscuro, navy, navyOscuro],
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
                        fontColor: colorTexto
                    },
                    gridLines: { color: colorGrid }
                }],
                yAxes: [{
                    ticks: { fontColor: colorTexto },
                    gridLines: { display: false }
                }]
            }
        }
    });

    // ================== GRÁFICO TOP PROVINCIAS ==================
    const porProvincia = @json($porProvincia);

    new Chart(document.getElementById('graficoProvincias'), {
        type: 'horizontalBar',
        data: {
            labels: porProvincia.map(p => p.nombre),
            datasets: [{
                label: 'Incidencias',
                data: porProvincia.map(p => p.total),
                backgroundColor: [navyOscuro, navy, doradoOscuro, dorado, doradoClaro],
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
                        fontColor: colorTexto
                    },
                    gridLines: { color: colorGrid }
                }],
                yAxes: [{
                    ticks: { fontColor: colorTexto },
                    gridLines: { display: false }
                }]
            }
        }
    });
</script>

@endsection