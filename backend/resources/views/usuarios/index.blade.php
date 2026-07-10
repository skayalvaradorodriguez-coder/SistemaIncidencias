@extends('layouts.app')

@section('title', 'Usuarios')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h1>
            Gestión de Usuarios
        </h1>

        <a href="{{ route('usuarios.create') }}" class="btn btn-primary">
            <i class="fas fa-user-plus"></i>
            Nuevo Usuario
        </a>

    </div>

    <div class="card">

        <div class="card-header">
            <h3 class="card-title">
                Lista de Usuarios
            </h3>
        </div>

        <div class="card-body">

            <table class="table table-bordered table-hover">

                <thead class="thead-dark">

                    <tr>

                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th width="180">Acciones</th>

                    </tr>

                </thead>

                <tbody>

                    {{-- Cuando conectemos con la BD aquí irá el foreach --}}

                    <tr>

                        <td>1</td>

                        <td>Administrador</td>

                        <td>admin@upse.edu.ec</td>

                        <td>
                            <span class="badge badge-primary">
                                Administrador
                            </span>
                        </td>

                        <td>
                            <span class="badge badge-success">
                                Activo
                            </span>
                        </td>

                        <td>

                            <a href="#" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i>
                            </a>

                            <a href="#" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>

                            <button class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i>
                            </button>

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection