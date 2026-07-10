@extends('layouts.app')

@section('title', 'Detalle de Usuario')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h1>Detalle del Usuario</h1>

        <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
            Volver
        </a>

    </div>

    <div class="card">

        <div class="card-header">
            <h3 class="card-title">Información del usuario</h3>
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
                    <th>Email</th>
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

            <a id="btnEditar" class="btn btn-warning">
                Editar
            </a>

        </div>

    </div>

</div>

@endsection

@section('scripts')

<script>

const id = window.location.pathname.split('/').pop();

async function cargarUsuario(){

    const response = await authFetch('/api/usuarios/'+id);

    const usuario = await response.json();

    document.getElementById('id').textContent = usuario.id;
    document.getElementById('name').textContent = usuario.name;
    document.getElementById('apellido').textContent = usuario.apellido;
    document.getElementById('email').textContent = usuario.email;
    document.getElementById('rol').textContent = usuario.rol?.nombre ?? '';

    document.getElementById('activo').innerHTML =
        usuario.activo
        ? '<span class="badge badge-success">Activo</span>'
        : '<span class="badge badge-danger">Inactivo</span>';

    document.getElementById('btnEditar').href =
        '/usuarios/'+usuario.id+'/editar';

}

cargarUsuario();

</script>

@endsection