<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <?php
    if (isset($_SESSION["u"])) {
        echo '<title>Bienvenid@: Pista Deportiva</title>';
    } else {
        echo '<title>Inicia sesión: Pista Deportiva</title>';
    }
    ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="ico.png">
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
    <div class="container">
        <h2>Proyecto Integrado - Adrián Fdz.</h2>
        <!--<h2 style="color: red;">PÁGINA EN CONSTRUCCIÓN</h2>-->
        <hr>
        <h1>Reservas Pista Deportiva</h1>
    <?php

    date_default_timezone_set('Europe/Madrid');
    $horaActual = date("H:i");

    if ($horaActual >= "00:30" && $horaActual <= "06:30") {
        echo '<form class="form">';
        echo '<label for="mensaje">Mensaje del Administrador del sitio web: </label>';
        echo "<h3 style='color: red;'>
            Actualmente no es posible iniciar sesión 
            en horario de 00:30 a.m. a 06:30 a.m. por motivos de mantenimiento.</h3>
            <h3>Disculpe las molestias.</h3>
            <h4>Si tiene cualquier cuestión, 
            no dude ponerse en contacto con nosotros.</h4></form></div>";
    } else {

        //var_dump($_SESSION);
    if (!isset($_SESSION["u"])) {
            echo '<h2>Inicia sesión</h2>';
        if (isset($_SESSION["error"])) {
            echo '<h3 style="color:red;">Datos incorrectos, vuelve a intentarlo.</h3>';
            echo '<script>alert("Datos incorrectos, vuelve a intentarlo.");</script>';
        }
    ?>
        <form action="procesaLogin.php" method="post" name="input" onsubmit="return validarFormulario()">
            <label for="username"><b>Usuario</b></label>
            <input type="tel" maxlength="9" placeholder="Introduce tu número de teléfono"
                    name="username" id="telefono" required>

            <label for="password"><b>Contraseña</b></label>
            <input type="password" placeholder="Introduce tu contraseña"
                    name="password" required>

            <button type="submit" name="submit">Acceder</button>

            <div class="options">
                <a href="contrasenaOlvidada.php">¿Has olvidado tu contraseña?</a>
                <a href="crearCuenta.php">Crear cuenta</a>
            </div>
        </form>
    </div>
        <?php
        /*
        echo '
        <div class="container">
            <br><p><a href="/admin/" target="_self">Acceso Admin</a></p>
        </div>
        ';
        <div class="container">
        <p><a style='color: gainsboro; text-decoration: none;'
        href="/phpmyadmin/" target="_blank">DB</a></p>
        </div>
        */
        ?>
    <?php
    } else {
        echo '<div class="container">';
        echo '<hr><br>';
        //echo '(Se ha autentificado correctamente)</br>';
        echo '<h3>¡Bienvenid@ '.$_SESSION["n"].' '.$_SESSION["a"].'!</h3>';
        echo '- Usuario: '.$_SESSION["u"].' -</br>';
        
        // Realizamos consulta para ver si tiene reservas realizadas
        require 'funcionConectar.php';
        $conexion = conectar();
        
        $user = $_SESSION["u"];
        // Consulta SQL para contar las ocurrencias en la columna telefono en RESERVAS
        $consulta = "SELECT COUNT(*) AS total FROM RESERVAS
                    WHERE telefono = :user AND fechainicio >= CURRENT_DATE;";
        $stmt = $conexion->prepare($consulta);
        $stmt->bindParam(':user', $user);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Obtener el total de ocurrencias del usuario
        $totalOcurrencias = $resultado['total'];

        if ($totalOcurrencias == 0 AND $_SESSION["u"] == "999999999") {
            echo '<div><h3>Panel de Administración</h3></div>
            <form class="form">
            <label for="gestion">Pincha en alguna opción:</label>
            <div>
            <br><a href="/admin/gestionPagos.php" class="button-link">Gestión de pagos</a>
            <br><a href="/admin/cancelado.php" class="button-link">Ver Cancelaciones</a>
            <br><a href="/admin/clave/index_clave.php" class="button-link">Consultar Clave</a>
            </div>
            </form>';
        } else {
            echo '<h2>¿Qué deseas hacer?</h2>';
            echo '<form class="form">';
            echo '<label for="primera">Pincha en alguna de las siguientes opciones:</label>';
            echo '<br><div><a href="reservas.php" class="button-link">Realizar Reserva</a>';
            if ($totalOcurrencias == 0) {
                echo '<br><a href="modificarDatos.php" class="button-link">Modificar mis datos</a>';
            }
            echo '</form>';
        }
        
        // Verificar si el total de ocurrencias es igual o mayor a 1
        if ($totalOcurrencias >= 1) {

            echo '<form class="none">';
            echo '<br><a href="verReservas.php" class="button-link">Ver mis Reservas</a>';
            echo '<br><a href="cancelarReserva.php" class="button-link">Cancelar Reserva</a>';
            echo '<br><a href="modificarDatos.php" class="button-link">Modificar mis datos</a>';
            echo '</form></div></div>';
        }

        echo '</div>';
        
        echo '<div style="margin: auto; width: fit-content;">
            <form class="none" action="procesaLogin.php" method = "POST">';
        echo '<br><br><input type="submit" name="cerrar" value="Cerrar sesión"><br>';
        echo '</form></div>';
        
    }
    };
    ?>
        <div class="container">
            <br><p>Contacto:
            <a href="mailto:info@pistadeportiva.es">info@pistadeportiva.es</a></p>
        </div>
    <script>
        function validarFormulario() {
        var telefono = document.getElementById("telefono").value;

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