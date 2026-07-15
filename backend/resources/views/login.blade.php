<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso - Sistema de Incidencias</title>

    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            background: #1f2937;
        }

        /* ===== Panel izquierdo (marca) ===== */
        .panel-marca {
            flex: 1.1;
            background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 45%, #0ea5e9 100%);
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px;
            position: relative;
            overflow: hidden;
        }

        .panel-marca::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.06);
            top: -180px;
            right: -180px;
        }

        .panel-marca::after {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            bottom: -120px;
            left: -100px;
        }

        .marca-logo {
            font-size: 2.6rem;
            margin-bottom: 8px;
        }

        .marca-titulo {
            font-size: 1.9rem;
            font-weight: 700;
            line-height: 1.25;
            margin-bottom: 14px;
        }

        .marca-sub {
            font-size: 1rem;
            opacity: 0.85;
            margin-bottom: 40px;
            max-width: 420px;
            line-height: 1.5;
        }

        .marca-item {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 20px;
            font-size: 0.95rem;
        }

        .marca-item i {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }

        /* ===== Panel derecho (formularios) ===== */
        .panel-form {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
        }

        .caja-form {
            width: 100%;
            max-width: 400px;
        }

        .tabs {
            display: flex;
            background: #111827;
            border-radius: 10px;
            padding: 5px;
            margin-bottom: 26px;
        }

        .tab {
            flex: 1;
            text-align: center;
            padding: 10px;
            border-radius: 8px;
            color: #9ca3af;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.25s;
            user-select: none;
        }

        .tab.activa {
            background: #2563eb;
            color: #fff;
        }

        .formulario {
            display: none;
            animation: aparecer 0.3s ease;
        }

        .formulario.visible {
            display: block;
        }

        @keyframes aparecer {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .titulo-form {
            color: #f3f4f6;
            font-size: 1.35rem;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .sub-form {
            color: #9ca3af;
            font-size: 0.86rem;
            margin-bottom: 22px;
        }

        .campo {
            margin-bottom: 16px;
        }

        .campo label {
            display: block;
            color: #d1d5db;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .campo-input {
            position: relative;
        }

        .campo-input i.icono {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
            font-size: 0.85rem;
        }

        .campo-input input {
            width: 100%;
            padding: 12px 42px 12px 40px;
            background: #111827;
            border: 1.5px solid #374151;
            border-radius: 9px;
            color: #f3f4f6;
            font-size: 0.92rem;
            outline: none;
            transition: border-color 0.2s;
        }

        .campo-input input:focus {
            border-color: #2563eb;
        }

        .ver-pass {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
            cursor: pointer;
            font-size: 0.85rem;
        }

        .ver-pass:hover { color: #d1d5db; }

        .fila-doble {
            display: flex;
            gap: 12px;
        }

        .fila-doble .campo { flex: 1; }

        /* Fortaleza de contraseña */
        .fortaleza {
            display: none;
            margin-top: 8px;
        }

        .fortaleza.visible { display: block; }

        .fortaleza-barra {
            height: 5px;
            background: #374151;
            border-radius: 3px;
            overflow: hidden;
            margin-bottom: 6px;
        }

        .fortaleza-relleno {
            height: 100%;
            width: 0;
            border-radius: 3px;
            transition: all 0.3s;
        }

        .requisitos {
            list-style: none;
            font-size: 0.74rem;
            color: #6b7280;
        }

        .requisitos li { margin-bottom: 2px; }
        .requisitos li i { width: 14px; margin-right: 4px; }
        .requisitos li.ok { color: #34d399; }

        .btn-principal {
            width: 100%;
            padding: 13px;
            background: #2563eb;
            border: none;
            border-radius: 9px;
            color: #fff;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 6px;
        }

        .btn-principal:hover { background: #1d4ed8; }
        .btn-principal:disabled { background: #374151; cursor: not-allowed; }

        .alerta {
            display: none;
            padding: 11px 14px;
            border-radius: 8px;
            font-size: 0.84rem;
            margin-bottom: 16px;
            line-height: 1.4;
        }

        .alerta.error {
            display: block;
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.4);
            color: #fca5a5;
        }

        .alerta.exito {
            display: block;
            background: rgba(52, 211, 153, 0.12);
            border: 1px solid rgba(52, 211, 153, 0.4);
            color: #6ee7b7;
        }

        .nota-prueba {
            margin-top: 22px;
            padding: 12px 14px;
            background: #111827;
            border-radius: 9px;
            border: 1px dashed #374151;
            color: #9ca3af;
            font-size: 0.78rem;
            line-height: 1.6;
        }

        .nota-prueba b { color: #d1d5db; }

        @media (max-width: 900px) {
            .panel-marca { display: none; }
        }
    </style>
</head>

<body>

    <!-- Panel de marca -->
    <div class="panel-marca">
        <div class="marca-logo"><i class="fas fa-map-marked-alt"></i></div>
        <div class="marca-titulo">Sistema de Gestión de<br>Incidencias Georreferenciadas</div>
        <div class="marca-sub">
            Reporta, gestiona y da seguimiento a incidencias urbanas con ubicación
            en el mapa y trazabilidad completa de principio a fin.
        </div>

        <div class="marca-item">
            <i class="fas fa-map-marker-alt"></i>
            <span>Reportes con ubicación exacta en el mapa</span>
        </div>
        <div class="marca-item">
            <i class="fas fa-route"></i>
            <span>Seguimiento del ciclo de vida con historial de estados</span>
        </div>
        <div class="marca-item">
            <i class="fas fa-bell"></i>
            <span>Notificaciones en tiempo real de cada avance</span>
        </div>
        <div class="marca-item">
            <i class="fas fa-comments"></i>
            <span>Conversación de seguimiento por incidencia</span>
        </div>
    </div>

    <!-- Panel de formularios -->
    <div class="panel-form">
        <div class="caja-form">

            <div class="tabs">
                <div class="tab activa" id="tabLogin" onclick="cambiarTab('login')">Iniciar sesión</div>
                <div class="tab" id="tabRegistro" onclick="cambiarTab('registro')">Crear cuenta</div>
            </div>

            <div id="alerta" class="alerta"></div>

            <!-- ========== LOGIN ========== -->
            <form id="formLogin" class="formulario visible">

                <div class="titulo-form">Bienvenido de nuevo</div>
                <div class="sub-form">Ingresa tus credenciales para continuar</div>

                <div class="campo">
                    <label>Correo electrónico</label>
                    <div class="campo-input">
                        <i class="fas fa-envelope icono"></i>
                        <input type="email" id="loginEmail" placeholder="usuario@correo.com" required>
                    </div>
                </div>

                <div class="campo">
                    <label>Contraseña</label>
                    <div class="campo-input">
                        <i class="fas fa-lock icono"></i>
                        <input type="password" id="loginPassword" placeholder="••••••••" required>
                        <i class="fas fa-eye ver-pass" onclick="togglePassword('loginPassword', this)"></i>
                    </div>
                </div>

                <button type="submit" class="btn-principal" id="btnLogin">
                    Ingresar
                </button>

                <div class="nota-prueba">
                    <b>Usuarios de prueba:</b><br>
                    admin@incidencias.com / Admin123!<br>
                    ciudadano@incidencias.com / Ciudadano123!
                </div>

            </form>

            <!-- ========== REGISTRO ========== -->
            <form id="formRegistro" class="formulario">

                <div class="titulo-form">Crea tu cuenta</div>
                <div class="sub-form">Regístrate para reportar incidencias en tu ciudad</div>

                <div class="fila-doble">
                    <div class="campo">
                        <label>Nombre</label>
                        <div class="campo-input">
                            <i class="fas fa-user icono"></i>
                            <input type="text" id="regNombre" placeholder="Nombre" required>
                        </div>
                    </div>
                    <div class="campo">
                        <label>Apellido</label>
                        <div class="campo-input">
                            <i class="fas fa-user icono"></i>
                            <input type="text" id="regApellido" placeholder="Apellido" required>
                        </div>
                    </div>
                </div>

                <div class="campo">
                    <label>Correo electrónico</label>
                    <div class="campo-input">
                        <i class="fas fa-envelope icono"></i>
                        <input type="email" id="regEmail" placeholder="usuario@correo.com" required>
                    </div>
                </div>

                <div class="campo">
                    <label>Contraseña</label>
                    <div class="campo-input">
                        <i class="fas fa-lock icono"></i>
                        <input type="password" id="regPassword" placeholder="Mínimo 8 caracteres" required>
                        <i class="fas fa-eye ver-pass" onclick="togglePassword('regPassword', this)"></i>
                    </div>

                    <div class="fortaleza" id="fortaleza">
                        <div class="fortaleza-barra">
                            <div class="fortaleza-relleno" id="fortalezaRelleno"></div>
                        </div>
                        <ul class="requisitos">
                            <li id="reqLargo"><i class="far fa-circle"></i> Al menos 8 caracteres</li>
                            <li id="reqMayus"><i class="far fa-circle"></i> Una letra mayúscula</li>
                            <li id="reqMinus"><i class="far fa-circle"></i> Una letra minúscula</li>
                            <li id="reqNumero"><i class="far fa-circle"></i> Un número</li>
                        </ul>
                    </div>
                </div>

                <button type="submit" class="btn-principal" id="btnRegistro">
                    Crear cuenta
                </button>

            </form>

        </div>
    </div>

<script>
// Si ya hay sesión, va directo al dashboard
if (localStorage.getItem('token')) {
    window.location.href = '/';
}

const alerta = document.getElementById('alerta');

function mostrarAlerta(mensaje, tipo) {
    alerta.textContent = mensaje;
    alerta.className = 'alerta ' + tipo;
}

function limpiarAlerta() {
    alerta.className = 'alerta';
}

function cambiarTab(tab) {
    limpiarAlerta();

    document.getElementById('tabLogin').classList.toggle('activa', tab === 'login');
    document.getElementById('tabRegistro').classList.toggle('activa', tab === 'registro');

    document.getElementById('formLogin').classList.toggle('visible', tab === 'login');
    document.getElementById('formRegistro').classList.toggle('visible', tab === 'registro');
}

function togglePassword(inputId, icono) {
    const input = document.getElementById(inputId);
    const visible = input.type === 'text';
    input.type = visible ? 'password' : 'text';
    icono.classList.toggle('fa-eye', visible);
    icono.classList.toggle('fa-eye-slash', !visible);
}

function botonCargando(boton, cargando, textoNormal) {
    boton.disabled = cargando;
    boton.innerHTML = cargando
        ? '<i class="fas fa-spinner fa-spin"></i> Procesando...'
        : textoNormal;
}

function guardarSesion(data) {
    localStorage.setItem('token', data.token);
    localStorage.setItem('user', JSON.stringify(data.user));
    window.location.href = '/';
}

// ================== LOGIN ==================
document.getElementById('formLogin').addEventListener('submit', async function (e) {
    e.preventDefault();
    limpiarAlerta();

    const boton = document.getElementById('btnLogin');
    botonCargando(boton, true, 'Ingresar');

    try {
        const response = await fetch('/api/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                email: document.getElementById('loginEmail').value,
                password: document.getElementById('loginPassword').value
            })
        });

        const data = await response.json();

        if (response.status === 429) {
            mostrarAlerta('Demasiados intentos fallidos. Espera un minuto e intenta de nuevo.', 'error');
            return;
        }

        if (!response.ok) {
            mostrarAlerta(data.message || 'Correo o contraseña incorrectos', 'error');
            return;
        }

        guardarSesion(data);

    } catch (error) {
        mostrarAlerta('Error de conexión con el servidor', 'error');
    } finally {
        botonCargando(boton, false, 'Ingresar');
    }
});

// ================== FORTALEZA DE CONTRASEÑA ==================
const regPassword = document.getElementById('regPassword');

regPassword.addEventListener('input', function () {
    const valor = this.value;

    document.getElementById('fortaleza').classList.toggle('visible', valor.length > 0);

    const checks = {
        reqLargo: valor.length >= 8,
        reqMayus: /[A-Z]/.test(valor),
        reqMinus: /[a-z]/.test(valor),
        reqNumero: /\d/.test(valor)
    };

    let cumplidos = 0;

    for (const [id, ok] of Object.entries(checks)) {
        const li = document.getElementById(id);
        li.classList.toggle('ok', ok);
        li.querySelector('i').className = ok ? 'fas fa-check-circle' : 'far fa-circle';
        if (ok) cumplidos++;
    }

    const relleno = document.getElementById('fortalezaRelleno');
    const colores = ['#ef4444', '#f59e0b', '#eab308', '#34d399'];
    relleno.style.width = (cumplidos * 25) + '%';
    relleno.style.background = colores[cumplidos - 1] || '#ef4444';
});

// ================== REGISTRO ==================
document.getElementById('formRegistro').addEventListener('submit', async function (e) {
    e.preventDefault();
    limpiarAlerta();

    const password = regPassword.value;

    if (password.length < 8 || !/[A-Z]/.test(password) || !/[a-z]/.test(password) || !/\d/.test(password)) {
        mostrarAlerta('La contraseña no cumple los requisitos indicados.', 'error');
        return;
    }

    const boton = document.getElementById('btnRegistro');
    botonCargando(boton, true, 'Crear cuenta');

    try {
        const response = await fetch('/api/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                name: document.getElementById('regNombre').value,
                apellido: document.getElementById('regApellido').value,
                email: document.getElementById('regEmail').value,
                password: password
            })
        });

        const data = await response.json();

        if (response.status === 429) {
            mostrarAlerta('Demasiados intentos. Espera un minuto e intenta de nuevo.', 'error');
            return;
        }

        if (!response.ok) {
            const mensaje = data.errors
                ? Object.values(data.errors).flat().join(' ')
                : (data.message || 'No se pudo crear la cuenta');
            mostrarAlerta(mensaje, 'error');
            return;
        }

        mostrarAlerta('¡Cuenta creada! Ingresando...', 'exito');
        setTimeout(() => guardarSesion(data), 800);

    } catch (error) {
        mostrarAlerta('Error de conexión con el servidor', 'error');
    } finally {
        botonCargando(boton, false, 'Crear cuenta');
    }
});
</script>

</body>
</html>