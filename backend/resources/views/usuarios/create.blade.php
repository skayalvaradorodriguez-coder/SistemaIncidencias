@extends('layouts.app')

@section('title', 'Nuevo Usuario')

@section('content')

<div class="container-fluid">

    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Nuevo Usuario</h1>
        </div>
    </div>

    <div id="alerta"></div>

    <div class="card card-dark">

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
                        placeholder="********"
                        required>
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

                        <option value="1">
                            Activo
                        </option>

                        <option value="0">
                            Inactivo
                        </option>

                    </select>

                </div>

            </div>

            <div class="card-footer">

                <button
                    type="submit"
                    class="btn btn-primary">

                    <i class="fas fa-save"></i>
                    Guardar Usuario

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

@endsection

@section('scripts')

<script>

requireAuth();

document
.getElementById('formUsuario')
.addEventListener('submit', async function(e){

    e.preventDefault();

    const datos = {

        name: document.getElementById('name').value,

        apellido: document.getElementById('apellido').value,

        email: document.getElementById('email').value,

        password: document.getElementById('password').value,

        rol_id: document.getElementById('rol_id').value,

        activo: document.getElementById('activo').value

    };

    const respuesta = await authFetch('/api/usuarios',{

        method:'POST',

        body:JSON.stringify(datos)

    });

    if(respuesta.ok){

        document.getElementById('alerta').innerHTML=`

            <div class="alert alert-success">

                Usuario creado correctamente.

            </div>

        `;

        setTimeout(()=>{

            window.location='{{ route("usuarios.index") }}';

        },1200);

    }else{

        const error=await respuesta.json();

        document.getElementById('alerta').innerHTML=`

            <div class="alert alert-danger">

                ${JSON.stringify(error)}

            </div>

        `;

    }

});

</script>

@endsection