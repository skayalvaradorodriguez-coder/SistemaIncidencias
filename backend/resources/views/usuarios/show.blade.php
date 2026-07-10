<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<title>Detalle Usuario</title>

<script src="/js/auth.js"></script>

<style>

body{
    font-family:Arial;
    margin:40px;
}

table{
    border-collapse:collapse;
    width:500px;
}

th,td{
    border:1px solid #ccc;
    padding:10px;
    text-align:left;
}

button{
    padding:10px 18px;
    margin-top:20px;
}

a{
    text-decoration:none;
}

</style>

</head>

<body>

<h2>Detalle del Usuario</h2>

<table>

<tr>

<th>ID</th>

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

<br>

<a id="editar">

<button>

Editar

</button>

</a>

<a href="/usuarios">

<button>

Volver

</button>

</a>

<script>

requireAuth();

const id=window.location.pathname.split('/')[2];

async function cargarUsuario(){

const respuesta=await authFetch('/api/usuarios/'+id);

const usuario=await respuesta.json();

document.getElementById('id').textContent=usuario.id;

document.getElementById('name').textContent=usuario.name;

document.getElementById('apellido').textContent=usuario.apellido;

document.getElementById('email').textContent=usuario.email;

document.getElementById('rol').textContent=usuario.rol ? usuario.rol.nombre : '';

document.getElementById('activo').textContent=usuario.activo ? 'Activo' : 'Inactivo';

document.getElementById('editar').href='/usuarios/'+usuario.id+'/editar';

}

cargarUsuario();

</script>

</body>

</html>