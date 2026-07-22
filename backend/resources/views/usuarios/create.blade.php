@extends('layouts.app')

@section('title', 'Nuevo Usuario')

@section('styles')
<style>
    .pagina-header {
        background: linear-gradient(135deg, rgba(30,58,138,0.35) 0%, rgba(29,78,216,0.25) 45%, rgba(14,165,233,0.18) 100%);
        border: 1px solid var(--border-subtle);
        border-radius: 14px;
        padding: 18px 22px;
    }

    .btn-ghost {
        background: rgba(148,163,184,0.12);
        border: 1px solid var(--border-subtle);
        color: var(--text-main);
    }

    .btn-ghost:hover,
    .btn-ghost:focus {
        background: rgba(148,163,184,0.22);
        color: var(--text-main);
        border-color: var(--border-subtle);
    }

    @media (max-width: 767.98px) {

        .pagina-header {
            padding: 16px;
        }

        .pagina-header h1 {
            font-size: 1.35rem;
        }

        .card-footer {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .card-footer .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endsection

@section('content')

<div class="container-fluid">

    <div class="pagina-header mb-4">
        <h1 class="mb-0"><i class="fas fa-user-plus mr-2"></i>Nuevo Usuario</h1>
    </div>

    <div id="alerta"></div>

    <div class="card">

        <div class="card-header">
            <h3 class="card-title">
                Registrar nuevo usuario
            </h3>
        </div>

        <form id="formUsuario">

            <div class="card-body">

                <div class="form-group">
                    <label>Nombre</label>

                    <input
                        type="text"
                        id="name"
                        class="form-control"
                        placeholder="Ingrese el nombre"
                        required>
                </div>

                <div class="form-group">
                    <label>Apellido</label>

                    <input
                        type="text"
                        id="apellido"
                        class="form-control"
                        placeholder="Ingrese el apellido"
                        required>
                </div>

                <div class="form-group">
                    <label>Correo electrónico</label>

                    <input
                        type="email"
                        id="email"
                        class="form-control"
                        placeholder="correo@ejemplo.com"
                        required>
                </div>

                <div class="form-group">
                    <label>Contraseña</label>

                    <input
                        type="password"
                        id="password"
                        class="form-control"
                        placeholder="Ingrese una contraseña (mínimo 6 caracteres)"
                        minlength="6"
                        maxlength="50"
                        required>

                    <small class="form-text text-muted">
                        La contraseña debe tener al menos 6 caracteres.
                    </small>

                </div>

                <div class="form-group">
                    <label>Rol</label>

                    <select
                        id="rol_id"
                        class="form-control"
                        required>

                        <option value="2">Administrador</option>
                        <option value="3">Responsable</option>
                        <option value="4">Ciudadano</option>

                    </select>

                </div>

                <div class="form-group">

                    <label>Estado</label>

                    <select
                        id="activo"
                        class="form-control"
                        required>

                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>

                    </select>

                </div>

            </div>

            <div class="card-footer d-flex" style="gap:8px;">

                <button
                    type="submit"
                    id="btnGuardar"
                    class="btn btn-primary">

                    <i class="fas fa-save"></i>
                    Guardar Usuario

                </button>

                <a
                    href="{{ route('usuarios.index') }}"
                    class="btn btn-ghost">

                    Cancelar

                </a>

            </div>

        </form>

    </div>

</div>

@endsection

@section('scripts')

<script>

requireRole(["Administrador"]);

document
.getElementById('formUsuario')
.addEventListener('submit', async function(e){

    e.preventDefault();

    document.getElementById('alerta').innerHTML = '';

    const boton = document.getElementById('btnGuardar');

    boton.disabled = true;

    const datos = {

        name: document.getElementById('name').value,

        apellido: document.getElementById('apellido').value,

        email: document.getElementById('email').value,

        password: document.getElementById('password').value,

        rol_id: document.getElementById('rol_id').value,

        activo: document.getElementById('activo').value

    };

    try{

        const respuesta = await authFetch('/api/usuarios',{

            method:'POST',

            body:JSON.stringify(datos)

        });

        if(respuesta.ok){

            document.getElementById('alerta').innerHTML = `

                <div class="alert alert-success">

                    <i class="fas fa-check-circle"></i>

                    Usuario creado correctamente.

                </div>

            `;

            document.getElementById('formUsuario').reset();

            setTimeout(()=>{

                window.location='{{ route("usuarios.index") }}';

            },1200);

        }else{

            const error = await respuesta.json();

            let mensaje = '';

            if(error.errors){

                mensaje = Object.values(error.errors)
                                .flat()
                                .join('<br>');

            }else{

                mensaje = error.message;

            }

            document.getElementById('alerta').innerHTML = `

                <div class="alert alert-danger">

                    <i class="fas fa-exclamation-circle"></i>

                    ${mensaje}

                </div>

            `;

        }

    }catch(error){

        document.getElementById('alerta').innerHTML = `

            <div class="alert alert-danger">

                <i class="fas fa-times-circle"></i>

                Ocurrió un error al comunicarse con el servidor.

            </div>

        `;

    }

    boton.disabled = false;

});

</script>

@endsection