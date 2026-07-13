// public/js/auth.js

function getToken() {
    return localStorage.getItem('token');
}

function getUser() {
    const user = localStorage.getItem('user');
    return user ? JSON.parse(user) : null;
}

function isLoggedIn() {
    return !!getToken();
}

function logout() {
    const token = getToken();

    fetch('/api/logout', {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        }
    }).finally(() => {
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        window.location.href = '/login';
    });
}

async function authFetch(url, options = {}) {
    const token = getToken();

    const headers = {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
        ...(options.headers || {})
    };

    if (options.body && !(options.body instanceof FormData)) {
        headers['Content-Type'] = 'application/json';
    }

    const response = await fetch(url, {
        ...options,
        headers
    });

    if (response.status === 401) {
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        window.location.href = '/login';
        throw new Error('No autenticado');
    }

    return response;
}

function requireAuth() {
    if (!isLoggedIn()) {
        window.location.href = '/login';
    }
}

function esAdministrador() {

    const user = getUser();

    return user &&
           user.rol &&
           user.rol.nombre === "Administrador";

}

function esTecnico() {

    const user = getUser();

    return user &&
           user.rol &&
           user.rol.nombre === "Técnico";

}

function esUsuario() {

    const user = getUser();

    return user &&
           user.rol &&
           user.rol.nombre === "Usuario";

}

function requireRole(rolesPermitidos){

    requireAuth();

    const user = getUser();

    if(!user){

        window.location = "/login";
        return;

    }

    if(!rolesPermitidos.includes(user.rol.nombre)){

        alert("No tiene permisos para acceder a esta página.");

        window.location = "/";

    }

}