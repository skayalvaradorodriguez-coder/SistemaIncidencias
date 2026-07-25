<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer contraseña - Sistema de Incidencias</title>

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
            align-items: center;
            justify-content: center;
            background: #1f2937;
            padding: 24px;
        }

        .caja-form {
            width: 100%;
            max-width: 400px;
        }

        .marca-logo {
            font-size: 2.2rem;
            color: #C9A961;
            margin-bottom: 10px;
            text-align: center;
        }

        .titulo-form {
            color: #f3f4f6;
            font-size: 1.35rem;
            font-weight: 700;
            margin-bottom: 4px;
            text-align: center;
        }

        .sub-form {
            color: #9ca3af;
            font-size: 0.86rem;
            margin-bottom: 22px;
            text-align: center;
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
            border-color: #C9A961;
        }

        .campo-input input:read-only {
            color: #9ca3af;
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
            background: linear-gradient(to bottom, #E3CD8F, #C9A961);
            border: none;
            border-radius: 9px;
            color: #0A1128;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            transition: filter 0.2s;
        }

        .btn-principal:hover { filter: brightness(1.05); }
        .btn-principal:disabled { opacity: 0.7; cursor: not-allowed; }

        .volver {
            display: block;
            text-align: center;
            margin-top: 18px;
            color: #9ca3af;
            font-size: 0.84rem;
            text-decoration: none;
        }

        .volver:hover { color: #C9A961; }

        .alerta {
            display: none;
            padding: 10px 14px;
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
    </style>
</head>

<body>

    <div class="caja-form">
        <div class="marca-logo"><i class="fas fa-map-marked-alt"></i></div>
        <div class="titulo-form">Crear nueva contraseña</div>
        <div class="sub-form">Ingresa y confirma tu nueva contraseña</div>

        <div id="alerta" class="alerta"></div>

        <form id="formReset">

            <div class="campo">
                <label>Correo electrónico</label>
                <div class="campo-input">
                    <i class="fas fa-envelope icono"></i>
                    <input type="email" id="resetEmail" value="{{ $email }}" required>
                </div>
            </div>

            <div class="campo">
                <label>Nueva contraseña</label>
                <div class="campo-input">
                    <i class="fas fa-lock icono"></i>
                    <input type="password" id="resetPassword" placeholder="Mínimo 8 caracteres" required>
                    <i class="fas fa-eye ver-pass" onclick="togglePassword('resetPassword', this)"></i>
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

            <div class="campo">
                <label>Confirmar contraseña</label>
                <div class="campo-input">
                    <i class="fas fa-lock icono"></i>
                    <input type="password" id="resetPasswordConfirm" placeholder="Repite la contraseña" required>
                    <i class="fas fa-eye ver-pass" onclick="togglePassword('resetPasswordConfirm', this)"></i>
                </div>
            </div>

            <button type="submit" class="btn-principal" id="btnReset">
                Guardar nueva contraseña
            </button>

            <a href="{{ route('login') }}" class="volver">
                <i class="fas fa-arrow-left"></i> Volver a iniciar sesión
            </a>

        </form>
    </div>

<script>
const token = @json($token);

const alerta = document.getElementById('alerta');

function mostrarAlerta(mensaje, tipo) {
    alerta.textContent = mensaje;
    alerta.className = 'alerta ' + tipo;
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

// Fortaleza de contraseña (mismo criterio que registro)
const resetPassword = document.getElementById('resetPassword');

resetPassword.addEventListener('input', function () {
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

document.getElementById('formReset').addEventListener('submit', async function (e) {
    e.preventDefault();

    const password = resetPassword.value;
    const passwordConfirm = document.getElementById('resetPasswordConfirm').value;

    if (password.length < 8 || !/[A-Z]/.test(password) || !/[a-z]/.test(password) || !/\d/.test(password)) {
        mostrarAlerta('La contraseña no cumple los requisitos indicados.', 'error');
        return;
    }

    if (password !== passwordConfirm) {
        mostrarAlerta('Las contraseñas no coinciden.', 'error');
        return;
    }

    const boton = document.getElementById('btnReset');
    botonCargando(boton, true, 'Guardar nueva contraseña');

    try {
        const response = await fetch('/api/reset-password', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                token: token,
                email: document.getElementById('resetEmail').value,
                password: password,
                password_confirmation: passwordConfirm
            })
        });

        const data = await response.json();

        if (!response.ok) {
            mostrarAlerta(data.message || 'No se pudo restablecer la contraseña.', 'error');
            return;
        }

        mostrarAlerta('¡Contraseña actualizada! Redirigiendo a iniciar sesión...', 'exito');
        setTimeout(() => window.location.href = '{{ route('login') }}', 1500);

    } catch (error) {
        mostrarAlerta('Error de conexión con el servidor', 'error');
    } finally {
        botonCargando(boton, false, 'Guardar nueva contraseña');
    }
});
</script>

</body>
</html>