<!DOCTYPE html>
<html>
<head>
    <title>Inicia sesión: Pista Deportiva</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="ico.png">
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Proyecto Integrado - Adrián Fdz.</h2>
        <hr>
        <h1>Reservas Pista Deportiva</h1>
        <h2>Crea tu cuenta</h2>
        <form action="procesaCuenta.php" method="post" onsubmit="return validarFormulario()">
            <label for="telefono">Teléfono (será tu usuario para acceder y realizar reservas)</label>
            <input type="tel" placeholder="Introduce tu número de teléfono"
                    id="telefono" name="telefono" required><br>
                    
            <label for="nombre">Nombre</label>
            <input type="text" placeholder="Introduce tu nombre"
                    id="nombre" name="nombre" required><br>

            <label for="apellidos">Apellidos</label>
            <input type="text" placeholder="Introduce tus apellidos"
                    id="apellidos" name="apellidos" required><br>

            <label for="email">Email</label>
            <input type="email" placeholder="Introduce tu email de contacto"
                    id="email" name="email" pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}" 
                    required><br>


            <label for="contrasena">Contraseña (mínimo 8 caracteres)</label>
            <input type="password" placeholder="Introduce una contraseña segura"
                    id="contrasena" name="contrasena" pattern=".{8,}" required><br>

            <button type="submit" name="submit">Crear cuenta</button>
        </form>
        <div>
                <br><p>Contacto:
                <a href="mailto:info@pistadeportiva.es">info@pistadeportiva.es</a></p>
        </div>
    </div>

<script>
function validarFormulario() {
  var nombre = document.getElementById("nombre").value;
  var apellidos = document.getElementById("apellidos").value;
  var telefono = document.getElementById("telefono").value;
  //var expresionRegular = /^[a-zA-Z\s]*$/;
  var expresionRegular = /^[a-zA-ZáéíóúÁÉÍÓÚ]+(?:\s[a-zA-ZáéíóúÁÉÍÓÚ]+)*$/u;


  if (!expresionRegular.test(nombre)) {
    alert("El nombre solo debe contener letras y un espacio entre palabras.");
    return false;
  }

  if (!expresionRegular.test(apellidos)) {
    alert("Los apellidos solo deben contener letras y un espacio entre palabras.");
    return false;
  }

  if (telefono.length != 9) {
    alert("Introduzca un teléfono válido de 9 dígitos.");
    return false;
  }

  return true;
}
</script>

<footer>
    <?php require 'cookies.php'; ?>
</footer>
</body>
</html>