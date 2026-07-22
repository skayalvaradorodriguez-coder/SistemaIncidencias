@extends('layouts.app')

@section('title', 'Editar Usuario')

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
        <h1 class="mb-0"><i class="fas fa-user-edit mr-2"></i>Editar Usuario</h1>
    </div>

    <div class="row justify-content-center">

        <div class="col-md-8">

            <div class="card">

                <div class="card-header">
                    <h3 class="card-title">
                        Datos del usuario
                    </h3>
                </div>

                <form id="formEditar">

                    <div class="card-body">

                        <div class="form-group">
                            <label>Nombre</label>
                            <input
                                type="text"
                                id="name"
                                class="form-control"
                                required>
                        </div>

                        <div class="form-group">
                            <label>Apellido</label>
                            <input
                                type="text"
                                id="apellido"
                                class="form-control"
                                required>
                        </div>

                        <div class="form-group">
                            <label>Correo electrónico</label>
                            <input
                                type="email"
                                id="email"
                                class="form-control"
                                required>
                        </div>

                        <div class="form-group">
                            <label>Nueva contraseña</label>
                            <input
                                type="password"
                                id="password"
                                class="form-control"
                                placeholder="Dejar en blanco para no cambiarla">
                        </div>

                        <div class="form-group">
                            <label>Rol</label>
                            <select
                                id="rol_id"
                                class="form-control">

                                <option value="2">Administrador</option>
                                <option value="3">Responsable</option>
                                <option value="4">Ciudadano</option>

                            </select>
                        </div>

                        <div class="form-group">
                            <label>Estado</label>

                            <select
                                id="activo"
                                class="form-control">

                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>

                            </select>

                        </div>

                    </div>

                    <div class="card-footer d-flex" style="gap:8px;">

                        <button
                            type="submit"
                            class="btn btn-primary">

                            <i class="fas fa-save"></i>
                            Actualizar

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

    </div>

</div>

@endsection

@section('scripts')

<script>

requireRole(["Administrador"]);

const id = window.location.pathname.split('/')[2];

async function cargarUsuario(){

    const respuesta = await authFetch('/api/usuarios/' + id);

    const usuario = await respuesta.json();

    document.getElementById('name').value = usuario.name;
    document.getElementById('apellido').value = usuario.apellido;
    document.getElementById('email').value = usuario.email;
    document.getElementById('rol_id').value = usuario.rol_id;
    document.getElementById('activo').value = usuario.activo ? 1 : 0;

}

cargarUsuario();

document.getElementById('formEditar').addEventListener('submit', async function(e){

    e.preventDefault();

    const datos = {

        name: document.getElementById('name').value,
        apellido: document.getElementById('apellido').value,
        email: document.getElementById('email').value,
        rol_id: document.getElementById('rol_id').value,
        activo: document.getElementById('activo').value

    };

    const password = document.getElementById('password').value;

    if(password !== ""){
        datos.password = password;
    }

    const respuesta = await authFetch('/api/usuarios/' + id, {

        method: 'PUT',
        body: JSON.stringify(datos)

    });

    if(respuesta.ok){

        alert('Usuario actualizado correctamente');

        window.location = '/usuarios';

    }else{

        const error = await respuesta.json();

        alert(error.message ?? 'No se pudo actualizar');

    }

});

</script>

@endsection