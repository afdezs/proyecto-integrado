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
    <div>
        <h3>Estas son todas tus reservas realizadas<br>
            ¿Qué reserva deseas cancelar?</h3>
    </div>

    <form class='form'>
        <!--<label for="reservas">Listado:</label><br>-->
        <div>
    </form>
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
                <th>Hora Fin</th>
                <th>Pagado</th>
            </tr>";

        // Recorremos los resultados y los mostramos en la tabla
        foreach ($resultado as $fila) {
            echo "<tr>
                <td>" . date('d M Y', strtotime($fila["fechainicio"])) . "</td>
                <td>" . date("H:i", strtotime($fila["fechainicio"])) . "</td>
                <td>" . date("H:i", strtotime($fila["fechafin"])) . "</td>
                <td>" . $fila["pagado"] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<h4>Selecciona la reserva a cancelar:</h4>";
        echo "<form action='confirmarCancelar.php' method='POST' class='none'>";
        echo "<select style='border: 3.5px solid; border-radius: 4px;'
                class='container' name='cancelareserva'>";

        foreach ($resultado as $fila) {
            $seleccionarFecha = $fila["fechainicio"];
            $fechaReserva = date('d M Y', strtotime($fila["fechainicio"]));
            $horaInicio = date("H:i", strtotime($fila["fechainicio"]));
            $horaFin = date("H:i", strtotime($fila["fechafin"]));
            $pagado = $fila["pagado"];

            echo "<option value='$seleccionarFecha'>".
            "$fechaReserva - $horaInicio a $horaFin</option>";
            
        }
        echo "</select>";
        echo "<div><br><button type='submit' name='submit'>Cancelar reserva seleccionada</button>";
        echo "</form>";

    } else {
        echo "<h3 style='color: red;'>Ha ocurrido un error, vuelve a la página principal.</h3>";
    }

    ?>
    </form>

    <form class="none" action="index.php" method="post">
        <button type="submit">Volver atrás y seleccionar otra opción</button>
    </form>
    </div>
    </div>

</body>
<footer>
    <?php require 'cookies.php'; ?>
</footer>
</html>