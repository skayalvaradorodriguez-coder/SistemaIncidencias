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

    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
</head>

<body class="hold-transition dark-mode sidebar-mini layout-fixed">

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

            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#" id="btnNotificaciones">
                    <i class="far fa-bell"></i>
                    <span class="badge badge-danger navbar-badge d-none" id="badgeNotificaciones">0</span>
                </a>

                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" id="listaNotificaciones" style="min-width: 320px; max-height: 400px; overflow-y: auto;">
                    <span class="dropdown-item dropdown-header">Notificaciones</span>
                    <div class="dropdown-divider"></div>
                    <span class="dropdown-item text-center text-muted" id="sinNotificaciones">Sin notificaciones</span>
                </div>
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
                Sistema Incidencias
            </span>

        </a>

        <div class="sidebar">

            <nav class="mt-2">

                <ul class="nav nav-pills nav-sidebar flex-column">

                    <!-- Dashboard -->

                    <li class="nav-item">

                        <a href="/"
                           class="nav-link {{ request()->is('/') ? 'active' : '' }}">

                            <i class="nav-icon fas fa-home"></i>

                            <p>Dashboard</p>

                        </a>

                    </li>

                    <!-- Incidencias -->

                    <li class="nav-item">

                        <a href="{{ route('incidencias.index') }}"
                           class="nav-link {{ request()->is('incidencias*') ? 'active' : '' }}">

                            <i class="nav-icon fas fa-exclamation-triangle"></i>

                            <p>Incidencias</p>

                        </a>

                    </li>

                    <!-- Usuarios (solo Administrador) -->

                    <li class="nav-item"
                        id="menuUsuarios"
                        style="display:none;">

                        <a href="{{ route('usuarios.index') }}"
                           class="nav-link {{ request()->is('usuarios*') ? 'active' : '' }}">

                            <i class="nav-icon fas fa-users"></i>

                            <p>Usuarios</p>

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

if(usuario){

    document.getElementById('usuarioLogueado').innerHTML =
        `<i class="fas fa-user"></i> ${usuario.name} (${usuario.rol.nombre})`;

    // SOLO ADMINISTRADOR VE EL MENÚ DE USUARIOS
    if(usuario.rol.nombre === "Administrador"){

        document.getElementById("menuUsuarios").style.display = "block";

    }

}

async function cargarNotificaciones(){

    try{

        const response = await authFetch('/api/notificaciones');

        if(!response.ok) return;

        const notificaciones = await response.json();

        const noLeidas = notificaciones.filter(n => !n.leida);

        const badge = document.getElementById('badgeNotificaciones');

        if(noLeidas.length > 0){
            badge.textContent = noLeidas.length;
            badge.classList.remove('d-none');
        }else{
            badge.classList.add('d-none');
        }

        const lista = document.getElementById('listaNotificaciones');

        lista.innerHTML = '<span class="dropdown-item dropdown-header">Notificaciones</span><div class="dropdown-divider"></div>';

        if(notificaciones.length === 0){
            lista.innerHTML += '<span class="dropdown-item text-center text-muted">Sin notificaciones</span>';
            return;
        }

        notificaciones.slice(0, 10).forEach(n => {

            const item = document.createElement('a');
            item.href = n.incidencia_id ? `/incidencias/${n.incidencia_id}` : '#';
            item.className = 'dropdown-item' + (n.leida ? '' : ' font-weight-bold');
            item.innerHTML = `<i class="fas fa-info-circle mr-2"></i> ${n.titulo}<br><small class="text-muted">${n.mensaje}</small>`;

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
            lista.appendChild(document.createElement('div')).className = 'dropdown-divider';
        });

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