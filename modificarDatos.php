<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Modificar datos: Pista Deportiva</title>
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
    <div><h3>Aquí puedes modificar tus datos</h3></div>
        <form action="confirmarDatos.php" method="post" onsubmit="return validarFormulario()">
                
        <?php
        if (isset($_SESSION["u"])) {

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
        $mail = $resultado['email'];
        
            // Verificamos si se obtuvieron resultados
            if (count($resultado) > 0) {
            ?>
                <label for="telefono">Teléfono (es tu usuario para acceder y realizar reservas)</label>
                <input type="tel" value="<?php echo $user;?>"
                        id="telefono" name="telefono" disabled><br>
                        
                <label for="nombre">Nombre</label>
                <input type="text" value="<?php echo $name;?>" placeholder="Introduce tu nombre"
                        id="nombre" name="nombre" required><br>

                <label for="apellidos">Apellidos</label>
                <input type="text" value="<?php echo $surname;?>" placeholder="Introduce tus apellidos"
                        id="apellidos" name="apellidos" required><br>

                <label for="email">Email</label>
                <input type="email" value="<?php echo $mail;?>" placeholder="Introduce tu email de contacto"
                        id="email" name="email" pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}" 
                        required><br>

                <button type="submit" name="submit">Modificar con nuevos datos insertados</button><br>
                <hr>
                <label for="mensaje">Cambia tu contraseña pinchando aquí: </label><br>
                    <a href="modificarContrasena.php" class="button-link">Cambiar mi contraseña</a><br>
                <hr>
            <?php
            echo "<input type='hidden' name='name' value='".htmlentities($name)."'>";
            echo "<input type='hidden' name='surname' value='".htmlentities($surname)."'>";
            echo "<input type='hidden' name='mail' value='".htmlentities($mail)."'>";
            } else {
            echo '<label for="mensaje">Mensaje:</label><br>';
            echo "<h3 style='color: red;'>¡Ha ocurrido un error! <br>Vuelve a la página principal.</h3><br>";
            }
        } else {
            echo '<label for="mensaje">Mensaje:</label><br>';
            echo "<h3 style='color: red;'>¡Ha ocurrido un error! <br>Vuelve a la página principal.</h3><br>";
        }    
        ?>
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
  var nombre = document.getElementById("nombre").value;
  var apellidos = document.getElementById("apellidos").value;
  var expresionRegular = /^[a-zA-ZáéíóúÁÉÍÓÚ]+(?:\s[a-zA-ZáéíóúÁÉÍÓÚ]+)*$/u;

  if (!expresionRegular.test(nombre)) {
    alert("El nombre solo debe contener letras y un espacio entre palabras.");
    return false;
  }

  if (!expresionRegular.test(apellidos)) {
    alert("Los apellidos solo deben contener letras y un espacio entre palabras.");
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