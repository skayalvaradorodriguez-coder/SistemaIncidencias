@extends('layouts.app')

@section('title', 'Editar Usuario')

@section('content')

<div class="container-fluid">

    <div class="row justify-content-center">

        <div class="col-md-8">

            <div class="card card-warning">

                <div class="card-header">
                    <h3 class="card-title">
                        Editar Usuario
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

                                <option value="1">Administrador</option>
                                <option value="2">Técnico</option>
                                <option value="3">Usuario</option>

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

                    <div class="card-footer">

                        <button
                            type="submit"
                            class="btn btn-warning">

                            <i class="fas fa-save"></i>
                            Actualizar

                        </button>

                        <a
                            href="{{ route('usuarios.index') }}"
                            class="btn btn-secondary">

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

requireAuth();

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