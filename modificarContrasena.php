<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Modificar pass: Pista Deportiva</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="ico.png">
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Proyecto Integrado - Adrián Fdz.</h2>
        <hr>
        <h1>Reservas Pista Deportiva</h1>
        <hr>
    <div><h3>Aquí puedes modificar tu contraseña</h3></div>
        <form action="procesaContrasena.php" method="post" onsubmit="return validarFormulario()">
                
        <?php
        // Preparamos la conexión con la base de datos
        include 'funcionConectar.php';
        $conexion = conectar();

        $user = $_SESSION["u"];
        
        // Consulta SQL con las columnas requeridas y el filtro por el teléfono
        $consulta = "SELECT nombre, apellidos, telefono, email 
                      FROM USUARIOS WHERE telefono = :user;";

        $stmt = $conexion->prepare($consulta);
        $stmt->bindValue(':user', $user);
        $stmt->execute();
        $resultado = $stmt->fetch();  
        //var_dump($resultado);

        $name = $resultado['nombre'];
        $surname = $resultado['apellidos'];
        $phone = $resultado['telefono'];
        $mail = $resultado['email'];
        // Verificamos si se obtuvieron resultados
        if (count($resultado) > 0) {
        ?>
            <label for="telefono">Teléfono (recuerda que es tu usuario para acceder y realizar reservas)</label>
            <input type="tel" value="<?php echo $user;?>"
                    id="telefono" name="telefono" disabled><br>
            
            <label for="contrasena">Introduce tu contraseña actual</label>
            <input type="password" placeholder="Introduce contraseña actual"
                    id="contrasena" name="contrasena" pattern=".{8,}" required><br>

            <label for="newcontrasena">Introduce una nueva contraseña (mínimo 8 caracteres)</label>
            <input type="password" placeholder="Introduce una nueva contraseña segura"
                    id="newcontrasena" name="newcontrasena" pattern=".{8,}" required><br>

            <label for="repeatcontrasena">Repite la nueva contraseña</label>
            <input type="password" placeholder="Repite la nueva contraseña segura"
                    id="repeatcontrasena" name="repeatcontrasena" pattern=".{8,}" required><br>

            <button type="submit" name="submit">Modificar contraseña</button><br>

            <div>
                <a href="modificarDatos.php" class="button-link">Volver atrás</a>
            </div>
        <?php
        } else {
          echo '<label for="mensaje">Mensaje:</label><br>';
          echo "<h3 style='color: red;'>¡Ha ocurrido un error! <br>Vuelve a la página principal.</h3><br>";
        }?>
            <div>
                <a href="index.php" class="button-link">Volver a la página principal</a>
            </div>
        </form>
        <div>
                <br><p>Contacto:
                <a href="mailto:info@pistadeportiva.es">info@pistadeportiva.es</a></p>
        </div>
    </div>

<script>
function validarFormulario() {
  var contrasena = document.getElementById("contrasena").value;
  var newcontrasena = document.getElementById("newcontrasena").value;
  var repeatcontrasena = document.getElementById("repeatcontrasena").value;

  if (contrasena == newcontrasena) {
    alert("La nueva contraseña no puede ser igual a la actual. Por favor, vuelve a introducir las contraseñas.");
    return false;
  }

  if (contrasena.length < 8) {
    alert("La contraseña actual debe tener al menos 8 caracteres.");
    return false;
  }

  if (newcontrasena.length < 8) {
    alert("La nueva contraseña debe tener al menos 8 caracteres.");
    return false;
  }

  if (newcontrasena !== repeatcontrasena) {
    alert("Las contraseñas no coinciden. Por favor, vuelve a introducir las contraseñas.");
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