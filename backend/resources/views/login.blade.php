<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Incidencias</title>

    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
</head>

<body class="hold-transition login-page dark-mode">

<div class="login-box">

    <div class="login-logo">
        <b>Sistema</b> Incidencias
    </div>

    <div class="card">

        <div class="card-body login-card-body">

            <p class="login-box-msg">
                Iniciar sesión
            </p>

            <div id="errorLogin" class="alert alert-danger d-none"></div>

            <form id="loginForm">

                <div class="input-group mb-3">
                    <input
                        type="email"
                        id="email"
                        class="form-control"
                        placeholder="Correo electrónico"
                        required>

                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>

                <div class="input-group mb-3">
                    <input
                        type="password"
                        id="password"
                        class="form-control"
                        placeholder="Contraseña"
                        required>

                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>

                <button
                    type="submit"
                    class="btn btn-primary btn-block">

                    Ingresar

                </button>

            </form>

            <hr>

            <small>
                Usuario de prueba:
                <br>
                admin@incidencias.com
                <br>
                Admin123!
            </small>

        </div>

    </div>

</div>

<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('dist/js/adminlte.min.js') }}"></script>

<script>
document.getElementById('loginForm').addEventListener('submit', async function(e) {

    e.preventDefault();

    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    const errorBox = document.getElementById('errorLogin');

    errorBox.classList.add('d-none');

    try {

        const response = await fetch('/api/login', {

            method: 'POST',

            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },

            body: JSON.stringify({
                email,
                password
            })
        });

        const data = await response.json();

        if (!response.ok) {

            errorBox.innerText = 'Correo o contraseña incorrectos';
            errorBox.classList.remove('d-none');

            return;
        }

        localStorage.setItem('token', data.token);
        localStorage.setItem('user', JSON.stringify(data.user));

        window.location.href = '/';

    } catch (error) {

        errorBox.innerText = 'Error de conexión';
        errorBox.classList.remove('d-none');
    }

});
</script>

</body>
</html>