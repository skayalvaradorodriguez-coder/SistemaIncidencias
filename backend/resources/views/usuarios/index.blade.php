@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Usuarios</h1>

        <a href="{{ route('usuarios.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Usuario
        </a>
    </div>

    <div class="card">

        <div class="card-header">
            <h3 class="card-title">
                Lista de Usuarios
            </h3>
        </div>

        <div class="card-body table-responsive">

            <table class="table table-bordered table-hover" id="tablaUsuarios">

                <thead>

                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th width="220">Acciones</th>
                    </tr>

                </thead>

                <tbody>

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection

@section('scripts')

<script>

requireRole(["Administrador"]);

async function cargarUsuarios(){
    

    const response = await authFetch('/api/usuarios');

    const usuarios = await response.json();

    let html = '';

    usuarios.forEach(usuario=>{

        html += `
        <tr>

            <td>${usuario.id}</td>

            <td>${usuario.name}</td>

            <td>${usuario.apellido}</td>

            <td>${usuario.email}</td>

            <td>${usuario.rol?.nombre ?? ''}</td>

            <td>

                ${
                    usuario.activo
                    ? '<span class="badge badge-success">Activo</span>'
                    : '<span class="badge badge-danger">Inactivo</span>'
                }

            </td>

            <td>

                <a href="/usuarios/${usuario.id}"
                   class="btn btn-info btn-sm">

                    Ver

                </a>

                <a href="/usuarios/${usuario.id}/editar"
                   class="btn btn-warning btn-sm">

                    Editar

                </a>

                <button
                    onclick="eliminarUsuario(${usuario.id})"
                    class="btn btn-danger btn-sm">

                    Desactivar

                </button>

            </td>

        </tr>
        `;

    });

    document.querySelector('#tablaUsuarios tbody').innerHTML = html;

}

async function eliminarUsuario(id){

    if(!confirm('¿Desea desactivar este usuario?'))
        return;

    await authFetch('/api/usuarios/'+id,{
        method:'DELETE'
    });

    cargarUsuarios();

}

cargarUsuarios();

</script>

@endsection