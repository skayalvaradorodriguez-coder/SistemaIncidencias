<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Crear Usuario</title>

    <script src="/js/auth.js"></script>

    <style>

        body{
            font-family: Arial;
            margin:40px;
        }

        input,select{
            display:block;
            width:300px;
            margin-bottom:15px;
            padding:8px;
        }

        button{
            padding:10px 18px;
            cursor:pointer;
        }

    </style>

</head>

<body>

<h2>Nuevo Usuario</h2>

<form id="formUsuario">

    <input
        type="text"
        id="name"
        placeholder="Nombre"
        required
    >

    <input
        type="text"
        id="apellido"
        placeholder="Apellido"
        required
    >

    <input
        type="email"
        id="email"
        placeholder="Correo"
        required
    >

    <input
        type="password"
        id="password"
        placeholder="Contraseña"
        required
    >

    <input
        type="number"
        id="rol_id"
        placeholder="Rol ID"
        required
    >

    <select id="activo">

        <option value="1">Activo</option>

        <option value="0">Inactivo</option>

    </select>

    <button type="submit">
        Guardar Usuario
    </button>

</form>

<br>

<a href="/usuarios">
    Volver
</a>

<script>

requireAuth();

document
.getElementById('formUsuario')
.addEventListener('submit',async(e)=>{

e.preventDefault();

const datos={

name:document.getElementById('name').value,

apellido:document.getElementById('apellido').value,

email:document.getElementById('email').value,

password:document.getElementById('password').value,

rol_id:document.getElementById('rol_id').value,

activo:document.getElementById('activo').value

};

const respuesta=await authFetch('/api/usuarios',{

method:'POST',

body:JSON.stringify(datos)

});

if(respuesta.ok){

alert('Usuario creado correctamente');

window.location='/usuarios';

}else{

const error=await respuesta.json();

alert(JSON.stringify(error));

}

});

</script>

</body>

</html>