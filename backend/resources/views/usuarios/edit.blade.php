<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<title>Editar Usuario</title>

<script src="/js/auth.js"></script>

<style>

body{
    font-family:Arial;
    margin:40px;
}

input,select{
    display:block;
    width:300px;
    padding:8px;
    margin-bottom:15px;
}

button{
    padding:10px 20px;
}

</style>

</head>

<body>

<h2>Editar Usuario</h2>

<form id="formEditar">

<input
type="text"
id="name"
required
>

<input
type="text"
id="apellido"
required
>

<input
type="email"
id="email"
required
>

<input
type="password"
id="password"
placeholder="Nueva contraseña (opcional)"
>

<input
type="number"
id="rol_id"
required
>

<select id="activo">

<option value="1">Activo</option>

<option value="0">Inactivo</option>

</select>

<button type="submit">

Actualizar

</button>

</form>

<br>

<a href="/usuarios">

Volver

</a>

<script>

requireAuth();

const id=window.location.pathname.split('/')[2];

async function cargarUsuario(){

const respuesta=await authFetch('/api/usuarios/'+id);

const usuario=await respuesta.json();

document.getElementById('name').value=usuario.name;

document.getElementById('apellido').value=usuario.apellido;

document.getElementById('email').value=usuario.email;

document.getElementById('rol_id').value=usuario.rol_id;

document.getElementById('activo').value=usuario.activo?1:0;

}

cargarUsuario();

document.getElementById('formEditar').addEventListener('submit',async(e)=>{

e.preventDefault();

const datos={

name:document.getElementById('name').value,

apellido:document.getElementById('apellido').value,

email:document.getElementById('email').value,

rol_id:document.getElementById('rol_id').value,

activo:document.getElementById('activo').value

};

const password=document.getElementById('password').value;

if(password!=""){

datos.password=password;

}

const respuesta=await authFetch('/api/usuarios/'+id,{

method:'PUT',

body:JSON.stringify(datos)

});

if(respuesta.ok){

alert("Usuario actualizado");

window.location="/usuarios";

}else{

alert("No se pudo actualizar");

}

});

</script>

</body>

</html>