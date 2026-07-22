@extends('layouts.app')

@section('title', 'Usuarios')

@section('styles')
<style>
    .usuarios-header {
        background: linear-gradient(135deg, rgba(30,58,138,0.35) 0%, rgba(29,78,216,0.25) 45%, rgba(14,165,233,0.18) 100%);
        border: 1px solid var(--border-subtle);
        border-radius: 14px;
        padding: 18px 22px;
    }

    #tablaUsuarios td {
        vertical-align: middle;
    }

    .avatar-mini {
        width: 34px;
        height: 34px;
        min-width: 34px;
        border-radius: 50%;
        background: var(--brand-gradient);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.78rem;
        box-shadow: 0 2px 6px rgba(0,0,0,0.25);
    }

    .usuario-nombre-celda {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .usuario-nombre-celda .nombre-completo {
        font-weight: 600;
        color: var(--text-main);
        line-height: 1.2;
    }

    .usuario-nombre-celda .id-chico {
        font-size: 0.7rem;
        color: var(--text-muted);
    }

    .rol-pill {
        display: inline-block;
        border-radius: 20px;
        padding: 2px 12px;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.2px;
        white-space: nowrap;
    }

    .rol-Administrador { background: rgba(139,92,246,0.15); color: #8b5cf6; border: 1px solid rgba(139,92,246,0.4); }
    .rol-Responsable    { background: rgba(14,165,233,0.15); color: #0ea5e9; border: 1px solid rgba(14,165,233,0.4); }
    .rol-Ciudadano      { background: rgba(34,197,94,0.15); color: #16a34a; border: 1px solid rgba(34,197,94,0.4); }

    /* =========================================================
       Botones de acción: compactos, solo ícono (Ver / Editar / Desactivar)
       ========================================================= */
    .acciones-grupo {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 6px;
        flex-wrap: nowrap;
    }

    .btn-accion {
        width: 30px;
        height: 30px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-size: 0.78rem;
        line-height: 1;
        border: none;
    }

    .btn-accion:hover,
    .btn-accion:focus {
        filter: brightness(1.1);
        color: #fff;
    }

    .btn-ver     { background: rgba(14,165,233,0.18); color: #0ea5e9; }
    .btn-ver:hover { background: #0ea5e9; }

    .btn-editar  { background: rgba(245,158,11,0.18); color: #f59e0b; }
    .btn-editar:hover { background: #f59e0b; }

    .btn-desactivar { background: rgba(239,68,68,0.18); color: #ef4444; }
    .btn-desactivar:hover { background: #ef4444; }

    /* =========================================================
       Responsive: la tabla se convierte en tarjetas apiladas
       ========================================================= */
    @media (max-width: 767.98px) {

        .usuarios-header {
            padding: 16px;
        }

        .usuarios-header h1 {
            font-size: 1.35rem;
        }

        .usuarios-header .btn {
            width: 100%;
            justify-content: center;
        }

        .usuarios-header > div:first-child {
            width: 100%;
        }

        #tablaUsuarios thead {
            display: none;
        }

        #tablaUsuarios,
        #tablaUsuarios tbody,
        #tablaUsuarios tr,
        #tablaUsuarios td {
            display: block;
            width: 100%;
        }

        #tablaUsuarios {
            border: none;
        }

        #tablaUsuarios tr {
            margin-bottom: 14px;
            border: 1px solid var(--border-subtle);
            border-radius: 12px;
            padding: 12px 14px;
            background: var(--bg-card);
        }

        #tablaUsuarios td {
            border: none;
            padding: 6px 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        #tablaUsuarios td[data-label]::before {
            content: attr(data-label);
            font-weight: 600;
            color: var(--text-muted);
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            flex-shrink: 0;
        }

        #tablaUsuarios td.celda-usuario {
            padding-bottom: 10px;
            margin-bottom: 6px;
            border-bottom: 1px solid var(--border-subtle);
        }

        #tablaUsuarios td.celda-usuario::before {
            display: none;
        }

        #tablaUsuarios td.celda-usuario .usuario-nombre-celda {
            width: 100%;
        }

        #tablaUsuarios td.celda-acciones {
            padding-top: 10px;
            margin-top: 6px;
            border-top: 1px solid var(--border-subtle);
        }

        #tablaUsuarios td.celda-acciones::before {
            display: none;
        }

        #tablaUsuarios td.celda-acciones .acciones-grupo {
            width: 100%;
            justify-content: flex-end;
        }
    }
</style>
@endsection

@section('content')

<div class="container-fluid">

    <div class="usuarios-header d-flex justify-content-between align-items-center flex-wrap mb-4">
        <div>
            <h1 class="mb-1"><i class="fas fa-users mr-2"></i>Usuarios</h1>
            <span style="color:var(--text-muted);">Administra las cuentas y roles del sistema</span>
        </div>

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
                        <th>Usuario</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th width="140" class="text-right">Acciones</th>
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

function iniciales(nombre, apellido){
    const n = (nombre || '').trim().charAt(0);
    const a = (apellido || '').trim().charAt(0);
    return (n + a).toUpperCase() || '?';
}

async function cargarUsuarios(){
    

    const response = await authFetch('/api/usuarios');

    const usuarios = await response.json();

    let html = '';

    usuarios.forEach(usuario=>{

        const rolNombre = usuario.rol?.nombre ?? 'Sin rol';

        html += `
        <tr>

            <td class="celda-usuario">
                <div class="usuario-nombre-celda">
                    <div class="avatar-mini">${iniciales(usuario.name, usuario.apellido)}</div>
                    <div>
                        <div class="nombre-completo">${usuario.name} ${usuario.apellido}</div>
                        <div class="id-chico">ID #${usuario.id}</div>
                    </div>
                </div>
            </td>

            <td data-label="Email">${usuario.email}</td>

            <td data-label="Rol">
                <span class="rol-pill rol-${rolNombre}">${rolNombre}</span>
            </td>

            <td data-label="Estado">

                ${
                    usuario.activo
                    ? '<span class="badge badge-success">Activo</span>'
                    : '<span class="badge badge-danger">Inactivo</span>'
                }

            </td>

            <td class="celda-acciones">

                <div class="acciones-grupo">

                    <a href="/usuarios/${usuario.id}"
                       class="btn-accion btn-ver" title="Ver">
                        <i class="fas fa-eye"></i>
                    </a>

                    <a href="/usuarios/${usuario.id}/editar"
                       class="btn-accion btn-editar" title="Editar">
                        <i class="fas fa-edit"></i>
                    </a>

                    <button
                        onclick="eliminarUsuario(${usuario.id})"
                        class="btn-accion btn-desactivar" title="Desactivar">
                        <i class="fas fa-user-slash"></i>
                    </button>

                </div>

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