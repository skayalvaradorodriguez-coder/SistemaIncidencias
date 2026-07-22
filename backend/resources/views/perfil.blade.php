@extends('layouts.app')

@section('title', 'Mi Perfil')

@section('styles')
<style>
    .pagina-header {
        background: linear-gradient(135deg, rgba(30,58,138,0.35) 0%, rgba(29,78,216,0.25) 45%, rgba(14,165,233,0.18) 100%);
        border: 1px solid var(--border-subtle);
        border-radius: 14px;
        padding: 18px 22px;
    }

    .avatar-iniciales {
        width: 90px; height: 90px; border-radius: 50%;
        background: linear-gradient(135deg, #1e3a8a, #0ea5e9);
        color: #fff; font-size: 2rem; font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 12px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.3);
    }

    /* Acción de seguridad (cambiar contraseña): ámbar en vez del
       naranja plano de Bootstrap, para que combine con el resto */
    .btn-accent {
        background: linear-gradient(135deg, #b45309 0%, #f59e0b 100%);
        border: none;
        color: #fff;
    }

    .btn-accent:hover,
    .btn-accent:focus {
        filter: brightness(1.08);
        color: #fff;
    }

    @media (max-width: 767.98px) {

        .pagina-header {
            padding: 16px;
        }

        .pagina-header h1 {
            font-size: 1.35rem;
        }

        .avatar-iniciales {
            width: 76px;
            height: 76px;
            font-size: 1.7rem;
        }

        #formDatos .btn,
        #formPassword .btn {
            width: 100%;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">

    <div class="pagina-header mb-4">
        <h1 class="mb-0"><i class="fas fa-user-circle mr-2"></i>Mi Perfil</h1>
    </div>

    <div id="alertPerfil" class="alert d-none"></div>

    <div class="row">

        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card">
                <div class="card-body text-center">
                    <div class="avatar-iniciales" id="avatarIniciales">--</div>
                    <h4 id="perfilNombre" class="mb-1">Cargando...</h4>
                    <span class="badge badge-primary" id="perfilRol"></span>
                    <p class="mt-2 mb-0" id="perfilEmail" style="font-size: 0.88rem; color: var(--text-muted);"></p>
                </div>
            </div>
        </div>

        <div class="col-md-8">

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user-edit mr-2"></i>Datos personales</h3>
                </div>
                <div class="card-body">
                    <form id="formDatos">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Nombre</label>
                                <input type="text" id="name" class="form-control" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Apellido</label>
                                <input type="text" id="apellido" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Correo electrónico</label>
                            <input type="email" id="email" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i>Guardar cambios
                        </button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-key mr-2"></i>Cambiar contraseña</h3>
                </div>
                <div class="card-body">
                    <form id="formPassword">
                        <div class="form-group">
                            <label>Contraseña actual</label>
                            <input type="password" id="password_actual" class="form-control" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Nueva contraseña</label>
                                <input type="password" id="password" class="form-control" required>
                                <small class="form-text text-muted">Mínimo 8 caracteres, con mayúscula, minúscula y número.</small>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Confirmar nueva contraseña</label>
                                <input type="password" id="password_confirmation" class="form-control" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-accent">
                            <i class="fas fa-lock mr-1"></i>Actualizar contraseña
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
const alerta = document.getElementById('alertPerfil');

function avisar(mensaje, tipo) {
    alerta.textContent = mensaje;
    alerta.className = 'alert alert-' + tipo;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function pintarPerfil(u) {
    document.getElementById('avatarIniciales').textContent =
        (u.name?.[0] || '') + (u.apellido?.[0] || '');
    document.getElementById('perfilNombre').textContent = u.name + ' ' + (u.apellido || '');
    document.getElementById('perfilRol').textContent = u.rol ? u.rol.nombre : '';
    document.getElementById('perfilEmail').textContent = u.email;

    document.getElementById('name').value = u.name || '';
    document.getElementById('apellido').value = u.apellido || '';
    document.getElementById('email').value = u.email || '';
}

async function cargarPerfil() {
    try {
        const response = await authFetch('/api/me');
        if (!response.ok) return;
        const u = await response.json();
        pintarPerfil(u);
        localStorage.setItem('user', JSON.stringify(u));
    } catch (e) { console.log(e); }
}

document.getElementById('formDatos').addEventListener('submit', async function (e) {
    e.preventDefault();
    alerta.className = 'alert d-none';

    try {
        const response = await authFetch('/api/perfil', {
            method: 'PUT',
            body: JSON.stringify({
                name: document.getElementById('name').value,
                apellido: document.getElementById('apellido').value,
                email: document.getElementById('email').value
            })
        });

        const data = await response.json();

        if (!response.ok) {
            const msj = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'No se pudo actualizar');
            avisar(msj, 'danger');
            return;
        }

        localStorage.setItem('user', JSON.stringify(data));
        pintarPerfil(data);
        avisar('Datos actualizados correctamente.', 'success');

    } catch (error) {
        avisar('Error de conexión con el servidor.', 'danger');
    }
});

document.getElementById('formPassword').addEventListener('submit', async function (e) {
    e.preventDefault();
    alerta.className = 'alert d-none';

    try {
        const response = await authFetch('/api/perfil', {
            method: 'PUT',
            body: JSON.stringify({
                name: document.getElementById('name').value,
                apellido: document.getElementById('apellido').value,
                email: document.getElementById('email').value,
                password_actual: document.getElementById('password_actual').value,
                password: document.getElementById('password').value,
                password_confirmation: document.getElementById('password_confirmation').value
            })
        });

        const data = await response.json();

        if (!response.ok) {
            const msj = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'No se pudo actualizar');
            avisar(msj, 'danger');
            return;
        }

        document.getElementById('formPassword').reset();
        avisar('Contraseña actualizada correctamente.', 'success');

    } catch (error) {
        avisar('Error de conexión con el servidor.', 'danger');
    }
});

cargarPerfil();
</script>
@endsection