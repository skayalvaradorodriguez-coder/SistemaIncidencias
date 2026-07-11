@extends('layouts.app')

@section('title', 'Detalle del Usuario')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Detalle del Usuario</h1>

        <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card card-primary">

        <div class="card-header">
            <h3 class="card-title">
                Información del Usuario
            </h3>
        </div>

        <div class="card-body">

            <table class="table table-bordered">

                <tr>
                    <th width="200">ID</th>
                    <td id="id"></td>
                </tr>

                <tr>
                    <th>Nombre</th>
                    <td id="name"></td>
                </tr>

                <tr>
                    <th>Apellido</th>
                    <td id="apellido"></td>
                </tr>

                <tr>
                    <th>Correo Electrónico</th>
                    <td id="email"></td>
                </tr>

                <tr>
                    <th>Rol</th>
                    <td id="rol"></td>
                </tr>

                <tr>
                    <th>Estado</th>
                    <td id="activo"></td>
                </tr>

            </table>

        </div>

        <div class="card-footer">

            <a id="editar" class="btn btn-warning">
                <i class="fas fa-edit"></i>
                Editar
            </a>

            <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Volver
            </a>

        </div>

    </div>

</div>

@endsection

@section('scripts')

<script>

requireAuth();

const id = window.location.pathname.split('/')[2];

async function cargarUsuario() {

    const respuesta = await authFetch('/api/usuarios/' + id);

    const usuario = await respuesta.json();

    document.getElementById('id').textContent = usuario.id;
    document.getElementById('name').textContent = usuario.name;
    document.getElementById('apellido').textContent = usuario.apellido;
    document.getElementById('email').textContent = usuario.email;
    document.getElementById('rol').textContent = usuario.rol ? usuario.rol.nombre : '';

    document.getElementById('activo').innerHTML =
        usuario.activo
        ? '<span class="badge badge-success">Activo</span>'
        : '<span class="badge badge-danger">Inactivo</span>';

    document.getElementById('editar').href = '/usuarios/' + usuario.id + '/editar';

}

cargarUsuario();

</script>

@endsection