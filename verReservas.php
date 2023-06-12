<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Reservas: Pista Deportiva</title>
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
            <hr>
    <div><h3>Estas son todas tus reservas realizadas</h3></div>

    <form class='form'>
        <!--<label for="reservas">Listado:</label><br>-->
        <div>
    <?php
    require 'funcionConectar.php';
    $conexion = conectar();

    $user = $_SESSION["u"];

    // Consulta SQL con las columnas requeridas y el filtro por el teléfono
    $consulta = "SELECT * FROM RESERVAS
                WHERE telefono = :user AND fechainicio >= CURRENT_DATE
                ORDER BY fechainicio ASC;";

    /*$consulta = "SELECT DATE_FORMAT(fechainicio, '%d-%m-%Y') AS fecha, 
            DATE_FORMAT(fechainicio, '%H:%i:%s') AS horainicio,
            DATE_FORMAT(fechafin, '%H:%i:%s') AS horafin, pagado, telefono
            FROM RESERVAS WHERE telefono = :user;"; */

    $stmt = $conexion->prepare($consulta);
    $stmt->bindParam(':user', $user);
    $stmt->execute();
    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);  
    //var_dump($resultado);
    
    // Verificamos si se obtuvieron resultados
    if (count($resultado) > 0) {
    // Creamos una tabla HTML para mostrar los resultados
        echo "<table class='container' border=0>
            <tr>
                <th>Fecha de Reserva</th>
                <th>Hora Inicio</th>
                <th>Hora Fin</th>";
                //<th>Pagado</th>
            echo "<th>Clave de Acceso</th>
            </tr>";

        // Llamamos a la función que usaremos para el algoritmo:
        require 'funcionAlgoritmo.php';

        // Recorremos los resultados y los mostramos en la tabla
        foreach ($resultado as $fila) {
            echo "<tr>
                <td>" . date('d F Y', strtotime($fila["fechainicio"])) . "</td>
                <td>" . date("H:i", strtotime($fila["fechainicio"])) . "</td>
                <td>" . date("H:i", strtotime($fila["fechafin"])) . "</td>";
                //<td>" . $fila["pagado"] . "</td>";
                if ($fila["pagado"] == 'SI'){
                    $hora = strtotime($fila["fechainicio"]); //Se coge la hora de la reserva en UNIX
                    echo "<td style='color: green;'><b>".generaToken($hora)."</b></td>";
                } else {
                    echo "<td style='color: red;'><b>Pendiente de pago</b></td>";
                }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo '<label for="mensaje">Mensaje:</label><br>';
        echo "<h3 style='color: red;'>¡Ha ocurrido un error!<br>
        Es posible que actualmente no hayas realizado ninguna reserva.</h3>";
    }
        echo "<h4>NOTA INFORMATIVA:</h4><p>Es necesario pagar previamente la reserva en la
        oficina de deportes para recibir la clave de acceso para abrir las puertas de la pista.</p>";
        echo "<p><b>IMPORTANTE: <br>La clave de acceso solo es válida para la hora de la reserva.</b></p>";
    ?>
        <div>
            <a href="index.php" class="button-link">Volver atrás y seleccionar otra opción</a>
        </div>
    </form>
    </div>
    </div>

<footer>
    <?php require 'cookies.php'; ?>
</footer>
</body>
</html>