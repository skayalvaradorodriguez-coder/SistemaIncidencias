<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Sistema de Incidencias')</title>

    <script>
        if (!localStorage.getItem('token')) {
            window.location.href = '/login';
        }
    </script>

    <script>
        // Aplica el tema guardado ANTES de pintar la página (evita el "flash" de color)
        (function () {
            const tema = localStorage.getItem('tema') || 'dark';
            document.documentElement.setAttribute('data-theme', tema);
        })();
    </script>

    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">

    <style>
        /* =========================================================
           Paleta compartida con la pantalla de login
           ========================================================= */
        :root, [data-theme="dark"] {
            --brand-900: #0A1128;
            --brand-700: #16233F;
            --brand-400: #C9A961;
            --brand-gradient: linear-gradient(135deg, var(--brand-900) 0%, var(--brand-700) 62%, var(--brand-400) 145%);
            --bg-app: #1f2937;      /* mismo fondo que el <body> del login */
            --bg-panel: #111827;    /* mismo tono de los paneles del login */
            --bg-card: #1a2333;     /* un peldaño más claro, para tarjetas/columnas */
            --bg-card-hover: #222f45;
            --bg-card-tarjeta: #222f45; /* tono de las tarjetas dentro del kanban */
            --border-subtle: rgba(255, 255, 255, 0.08);
            --text-main: #f1f5f9;
            --text-muted: #9ca3af;
            --input-bg: var(--bg-panel);
        }

        /* ===== Tema claro: mismos azules de marca, fondo/tarjetas claros ===== */
        [data-theme="light"] {
            --brand-900: #0A1128;
            --brand-700: #16233F;
            --brand-400: #C9A961;
            --brand-gradient: linear-gradient(135deg, var(--brand-900) 0%, var(--brand-700) 62%, var(--brand-400) 145%);
            --bg-app: #f1f5f9;
            --bg-panel: #ffffff;
            --bg-card: #ffffff;
            --bg-card-hover: #eef2ff;
            --bg-card-tarjeta: #f8fafc;
            --border-subtle: rgba(15, 23, 42, 0.1);
            --text-main: #1f2937;
            --text-muted: #64748b;
            --input-bg: #ffffff;
        }

        body,
        .content-wrapper {
            background: var(--bg-app) !important;
        }

        /* ===== Barra lateral: SIEMPRE el degradado de marca, en ambos temas ===== */
        .main-sidebar {
            background: var(--brand-gradient) !important;
        }

        /* Texto del sidebar con contraste garantizado sobre el degradado,
           sin importar el tema activo (evita el problema de texto ilegible) */
        .main-sidebar .brand-text,
        .main-sidebar .nav-sidebar .nav-link,
        .main-sidebar .nav-sidebar .nav-link p,
        .main-sidebar .nav-sidebar .nav-icon {
            color: rgba(255, 255, 255, 0.92) !important;
        }

        .main-sidebar .nav-header {
            color: rgba(255, 255, 255, 0.55) !important;
        }

        .main-sidebar .nav-sidebar .nav-link:hover {
            background: rgba(201, 169, 97, 0.18) !important;
            color: #E3CD8F !important;
        }

        .main-sidebar .nav-sidebar .nav-link.active {
            background: rgba(201, 169, 97, 0.26) !important;
            color: #fff !important;
            box-shadow: inset 3px 0 0 #C9A961 !important;
        }

        /* ===== Barra superior a juego con los paneles oscuros/claros del login ===== */
        .main-header.navbar {
            background: var(--bg-panel) !important;
            border-bottom: 1px solid var(--border-subtle);
        }

        .main-header .nav-link {
            color: var(--text-main) !important;
        }

        .main-header .nav-link:hover {
            color: var(--brand-400) !important;
        }

        .main-header #usuarioLogueado {
            color: var(--text-main) !important;
        }

        .content-wrapper, .content-wrapper h1, .content-wrapper h2,
        .content-wrapper h3, .content-wrapper h4, .content-wrapper h5 {
            color: var(--text-main);
        }

        /* ===== Tarjetas / paneles genéricos (AdminLTE .card) ===== */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25);
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--border-subtle);
            border-radius: 12px 12px 0 0 !important;
        }

        /* ===== Botones primarios: degradado dorado, acento de marca ===== */
        .btn-primary {
            background: linear-gradient(to bottom, #E3CD8F, #C9A961);
            border: 1px solid #A9863F;
            color: #0A1128;
            font-weight: 600;
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background: linear-gradient(to bottom, #C9A961, #A9863F);
            border-color: #A9863F;
            color: #0A1128;
        }

        /* ===== Tablas dentro de tarjetas ===== */
        .table {
            color: var(--text-main);
        }

        .table thead th {
            border-bottom: 1px solid var(--border-subtle);
            color: var(--text-muted);
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .table td, .table th {
            border-top-color: var(--border-subtle);
        }

        /* AdminLTE agrega franjas claras a las tablas "striped"; las adaptamos al tema */
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(128, 128, 128, 0.06);
        }

        /* ===== Inputs / selects ===== */
        .form-control, .custom-select {
            background: var(--input-bg);
            border: 1px solid var(--border-subtle);
            color: var(--text-main);
        }

        .form-control:focus, .custom-select:focus {
            background: var(--input-bg);
            color: var(--text-main);
            border-color: var(--brand-400);
            box-shadow: 0 0 0 0.15rem rgba(14, 165, 233, 0.25);
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        .main-footer {
            background: var(--bg-panel);
            color: var(--text-muted);
            border-top: 1px solid var(--border-subtle);
        }

        /* ===== Tarjetas resumen del dashboard (small-box) ===== */
        .small-box {
            border-radius: 10px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.18);
            overflow: hidden;
        }
        .small-box.bg-info    { background: linear-gradient(135deg, #16233F, #0A1128) !important; }
        .small-box.bg-primary { background: linear-gradient(135deg, #1E2E52, #101A33) !important; }
        .small-box.bg-warning {
            background: linear-gradient(135deg, #E3CD8F, #C9A961) !important;
            color: #0A1128 !important;
        }
        .small-box.bg-warning .icon,
        .small-box.bg-warning h3,
        .small-box.bg-warning p,
        .small-box.bg-warning a { color: #0A1128 !important; }
        .small-box.bg-success  { background: linear-gradient(135deg, #2F7A4D, #1F5636) !important; }

        /* Fondos oscuros fijos: forzar texto blanco sin importar el tema claro/oscuro */
        .small-box.bg-info    .icon, .small-box.bg-info    h3, .small-box.bg-info    p, .small-box.bg-info    a,
        .small-box.bg-primary .icon, .small-box.bg-primary h3, .small-box.bg-primary p, .small-box.bg-primary a,
        .small-box.bg-success .icon, .small-box.bg-success h3, .small-box.bg-success p, .small-box.bg-success a {
            color: #fff !important;
        }

        .small-box .icon { opacity: 0.25; }
        .small-box-footer { background: rgba(0,0,0,0.15) !important; }

        .main-sidebar .brand-link {
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        }

        /* ===== Notificaciones ===== */
        #listaNotificaciones {
            min-width: 360px;
            max-width: 360px;
            max-height: 420px;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 0;
        }

        #listaNotificaciones .notif-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 10px 14px;
            white-space: normal;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            text-decoration: none;
        }

        #listaNotificaciones .notif-item:last-child {
            border-bottom: none;
        }

        #listaNotificaciones .notif-item.no-leida {
            background: rgba(0, 123, 255, 0.10);
            border-left: 3px solid #007bff;
        }

        #listaNotificaciones .notif-icono {
            flex-shrink: 0;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
        }

        #listaNotificaciones .notif-titulo {
            font-size: 0.85rem;
            font-weight: 600;
            line-height: 1.25;
            margin-bottom: 2px;
        }

        #listaNotificaciones .notif-mensaje {
            font-size: 0.78rem;
            line-height: 1.3;
            opacity: 0.75;
            margin-bottom: 2px;
        }

        #listaNotificaciones .notif-tiempo {
            font-size: 0.7rem;
            opacity: 0.55;
        }

        #listaNotificaciones .notif-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 14px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
            position: sticky;
            top: 0;
            background: inherit;
            z-index: 2;
        }

        #listaNotificaciones .notif-header a {
            font-size: 0.72rem;
            font-weight: 400;
            text-transform: none;
            letter-spacing: 0;
            cursor: pointer;
        }

        #listaNotificaciones .notif-vacio {
            padding: 28px 14px;
            text-align: center;
            opacity: 0.6;
            font-size: 0.85rem;
        }
    </style>

    @yield('styles')
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<script>
    // Sincroniza la clase dark-mode de AdminLTE (dropdowns, modales, etc.)
    // con el tema guardado, apenas se abre el <body> para minimizar el parpadeo.
    if ((document.documentElement.getAttribute('data-theme') || 'dark') === 'dark') {
        document.body.classList.add('dark-mode');
    }
</script>

<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-dark">

        <ul class="navbar-nav">

            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#">
                    <i class="fas fa-bars"></i>
                </a>
            </li>

        </ul>

        <ul class="navbar-nav ml-auto">

            <li class="nav-item">
                <a class="nav-link" href="#" id="btnTema" title="Cambiar tema">
                    <i class="fas fa-sun" id="iconoTema"></i>
                </a>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#" id="btnNotificaciones">
                    <i class="far fa-bell"></i>
                    <span class="badge badge-danger navbar-badge d-none" id="badgeNotificaciones">0</span>
                </a>

                <div class="dropdown-menu dropdown-menu-right" id="listaNotificaciones"></div>
            </li>

            <li class="nav-item">
                <span id="usuarioLogueado" class="nav-link"></span>
            </li>

            <li class="nav-item">
                <a href="#" class="nav-link" onclick="cerrarSesion()">
                    <i class="fas fa-sign-out-alt"></i>
                    Cerrar sesión
                </a>
            </li>

        </ul>

    </nav>

    <!-- Sidebar -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">

        <a href="/" class="brand-link">
            <span class="brand-text font-weight-light">
                <i class="fas fa-map-marked-alt mr-2"></i>Sistema Incidencias
            </span>
        </a>

        <div class="sidebar">

            <nav class="mt-2">

                <ul class="nav nav-pills nav-sidebar flex-column">

                    <li class="nav-header">GENERAL</li>

                    <li class="nav-item">
                        <a href="/" class="nav-link {{ request()->is('/') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-home"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('incidencias.index') }}"
                           class="nav-link {{ request()->is('incidencias*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-exclamation-triangle"></i>
                            <p>Incidencias</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('incidencias.mis') }}"
                           class="nav-link {{ request()->is('mis-reportes') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-clipboard-list"></i>
                            <p>Mis Reportes</p>
                        </a>
                    </li>

                    <li class="nav-header" id="headerGestion" style="display:none;">GESTIÓN</li>

                    <li class="nav-item" id="menuTablero" style="display:none;">
                        <a href="{{ route('incidencias.tablero') }}"
                           class="nav-link {{ request()->is('tablero') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-columns"></i>
                            <p>Tablero Kanban</p>
                        </a>
                    </li>

                    <li class="nav-item" id="menuReportes" style="display:none;">
                        <a href="{{ route('reportes') }}"
                           class="nav-link {{ request()->is('reportes') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-chart-pie"></i>
                            <p>Reportes</p>
                        </a>
                    </li>

                    <li class="nav-item" id="menuUsuarios" style="display:none;">
                        <a href="{{ route('usuarios.index') }}"
                           class="nav-link {{ request()->is('usuarios*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Usuarios</p>
                        </a>
                    </li>

                    <li class="nav-header">CUENTA</li>

                    <li class="nav-item">
                        <a href="{{ route('emergencias') }}"
                           class="nav-link {{ request()->is('emergencias') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-phone-alt"></i>
                            <p>Emergencias</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('perfil') }}"
                           class="nav-link {{ request()->is('perfil') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-circle"></i>
                            <p>Mi Perfil</p>
                        </a>
                    </li>

                </ul>

            </nav>

        </div>

    </aside>

    <!-- Contenido -->

    <div class="content-wrapper">

        <section class="content pt-3">

            @yield('content')

        </section>

    </div>

    <footer class="main-footer">

        Sistema de Gestión de Incidencias Georreferenciadas

    </footer>

</div>

<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
<script src="{{ asset('dist/js/adminlte.min.js') }}"></script>

<script src="{{ asset('js/auth.js') }}"></script>

<script>

const usuario = getUser();

// ===== Modo oscuro / claro =====
const btnTema = document.getElementById('btnTema');
const iconoTema = document.getElementById('iconoTema');

function aplicarIconoTema(tema) {
    // En modo oscuro mostramos el ícono de sol (invita a pasar a claro), y viceversa
    iconoTema.className = tema === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
}

aplicarIconoTema(document.documentElement.getAttribute('data-theme') || 'dark');

btnTema.addEventListener('click', function (e) {
    e.preventDefault();

    const actual = document.documentElement.getAttribute('data-theme') || 'dark';
    const nuevo = actual === 'dark' ? 'light' : 'dark';

    document.documentElement.setAttribute('data-theme', nuevo);
    document.body.classList.toggle('dark-mode', nuevo === 'dark');
    localStorage.setItem('tema', nuevo);
    aplicarIconoTema(nuevo);
});

if(usuario){

    document.getElementById('usuarioLogueado').innerHTML =
        `<i class="fas fa-user"></i> ${usuario.name} (${usuario.rol.nombre})`;

    // Menús según rol
    const rolNombre = usuario.rol.nombre;

    if(rolNombre === "Administrador" || rolNombre === "Responsable"){
        document.getElementById("headerGestion").style.display = "block";
        document.getElementById("menuTablero").style.display = "block";
        document.getElementById("menuReportes").style.display = "block";
    }

    if(rolNombre === "Administrador"){
        document.getElementById("menuUsuarios").style.display = "block";
    }

}

// Devuelve icono y color según el tipo de notificación
function estiloNotificacion(titulo){

    if(titulo.includes('Nueva incidencia')){
        return { icono: 'fa-exclamation-triangle', color: '#ffc107' };
    }
    if(titulo.includes('Cambio de estado')){
        return { icono: 'fa-sync-alt', color: '#28a745' };
    }
    if(titulo.includes('asignación') || titulo.includes('asignacion')){
        return { icono: 'fa-user-plus', color: '#007bff' };
    }
    if(titulo.includes('comentario')){
        return { icono: 'fa-comment', color: '#17a2b8' };
    }
    return { icono: 'fa-info-circle', color: '#6c757d' };
}

// Convierte una fecha a texto relativo en español
function tiempoRelativo(fecha){

    const segundos = Math.floor((new Date() - new Date(fecha)) / 1000);

    if(segundos < 60) return 'Hace un momento';

    const minutos = Math.floor(segundos / 60);
    if(minutos < 60) return `Hace ${minutos} min`;

    const horas = Math.floor(minutos / 60);
    if(horas < 24) return `Hace ${horas} h`;

    const dias = Math.floor(horas / 24);
    if(dias === 1) return 'Ayer';
    if(dias < 7) return `Hace ${dias} días`;

    return new Date(fecha).toLocaleDateString('es-EC', { day: '2-digit', month: 'short' });
}

async function cargarNotificaciones(){

    try{

        const response = await authFetch('/api/notificaciones');

        if(!response.ok) return;

        const notificaciones = await response.json();

        const noLeidas = notificaciones.filter(n => !n.leida);

        const badge = document.getElementById('badgeNotificaciones');

        if(noLeidas.length > 0){
            badge.textContent = noLeidas.length > 9 ? '9+' : noLeidas.length;
            badge.classList.remove('d-none');
        }else{
            badge.classList.add('d-none');
        }

        const lista = document.getElementById('listaNotificaciones');

        let html = `
            <div class="notif-header">
                <span>Notificaciones</span>
                ${noLeidas.length > 0 ? '<a id="btnMarcarTodas" class="text-primary">Marcar todas como leídas</a>' : ''}
            </div>
        `;

        if(notificaciones.length === 0){
            html += '<div class="notif-vacio"><i class="far fa-bell-slash d-block mb-2" style="font-size:1.5rem;"></i>No tienes notificaciones</div>';
            lista.innerHTML = html;
            return;
        }

        lista.innerHTML = html;

        notificaciones.slice(0, 10).forEach(n => {

            const estilo = estiloNotificacion(n.titulo);

            const item = document.createElement('a');
            item.href = n.incidencia_id ? `/incidencias/${n.incidencia_id}` : '#';
            item.className = 'notif-item text-reset' + (n.leida ? '' : ' no-leida');
            item.innerHTML = `
                <div class="notif-icono" style="background:${estilo.color}22; color:${estilo.color};">
                    <i class="fas ${estilo.icono}"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="notif-titulo">${n.titulo}</div>
                    <div class="notif-mensaje">${n.mensaje}</div>
                    <div class="notif-tiempo"><i class="far fa-clock mr-1"></i>${tiempoRelativo(n.created_at)}</div>
                </div>
            `;

            item.addEventListener('click', async function(){
                if(!n.leida){
                    try{
                        await authFetch(`/api/notificaciones/${n.id}/leer`, { method: 'PUT' });
                    }catch(error){
                        console.log(error);
                    }
                }
            });

            lista.appendChild(item);
        });

        const btnTodas = document.getElementById('btnMarcarTodas');

        if(btnTodas){
            btnTodas.addEventListener('click', async function(e){
                e.stopPropagation();
                e.preventDefault();
                try{
                    await authFetch('/api/notificaciones/leer-todas', { method: 'PUT' });
                    cargarNotificaciones();
                }catch(error){
                    console.log(error);
                }
            });
        }

    }catch(error){
        console.log(error);
    }
}

if(usuario){
    cargarNotificaciones();
    setInterval(cargarNotificaciones, 30000);
}

async function cerrarSesion(){

    try{

        await authFetch('/api/logout',{

            method:'POST'

        });

    }catch(error){

        console.log(error);

    }

    localStorage.removeItem('token');
    localStorage.removeItem('user');

    window.location.href='/login';

}

</script>

@yield('scripts')

</body>
</html>