<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>cPanel: Pista Deportiva</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="../ico.png">
    <link rel="stylesheet" type="text/css" href="../style.css">
</head>

<body>
    <div class="container">
        <h2>Proyecto Integrado - Adrián Fdz.</h2>
        <!--<h2 style="color: red;">PÁGINA EN CONSTRUCCIÓN</h2>-->
        <hr>
        <h1>Reservas Pista Deportiva</h1>
        <hr>

    <div><h3>Gestión de Pagos - Listado de Reservas</h3></div>
    <form action="confirmarPagos.php" class='form' method="POST">
        <!--<label for="control">Listado de reservas:</label><br>-->
    <div>
    <?php
    require '../funcionConectar.php';
    $conexion = conectar();

    $user = $_SESSION["u"];

    if ($user == '999999999') {
        // Consulta SQL con las columnas requeridas y el filtro por el teléfono
        //$consulta = "SELECT * FROM RESERVAS;";
        $consulta = "SELECT RESERVAS.fechainicio, RESERVAS.fechafin, 
            USUARIOS.nombre, USUARIOS.apellidos, 
            RESERVAS.telefono, RESERVAS.pagado, RESERVAS.fechapago
            FROM RESERVAS INNER JOIN 
            USUARIOS ON RESERVAS.telefono = USUARIOS.telefono
            WHERE fechainicio >= CURRENT_DATE
            ORDER BY fechainicio ASC;";

        $stmt = $conexion->prepare($consulta);
        $stmt->execute();
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);  
        //var_dump($resultado);

        // Verificamos si se obtuvieron resultados
        if (count($resultado) > 0) {
            // Creamos una tabla HTML para mostrar los resultados
            echo "<table class='container' border=0>
                <tr>
                    <th>Fecha y hora</th>
                    <th>Nombre y apellidos</th>
                    <th>Telefono</th>
                    <th>Pagado</th>
                </tr>";
    
            // Recorremos los resultados y los mostramos en la tabla
            foreach ($resultado as $fila) {
                echo "<tr>
                    <td>" . date('d.m.y', strtotime($fila["fechainicio"])) .
                    "-" . date("H:i", strtotime($fila["fechainicio"])) . "</td>
                    <td>" . $fila["nombre"] . " " . $fila["apellidos"] . "</td>
                    <td>" . $fila["telefono"] . "</td>";
                    if ($fila["pagado"] == 'SI'){
                        echo "<td style='color: green;'><b>".$fila["pagado"].
                        "</b><br><span style='font-size: 11px'>(".date('d.m.y', strtotime($fila["fechapago"])).
                        "-".date("H:i", strtotime($fila["fechapago"])).")</span></td>";
                    } else {
                        echo "<td style='color: red;'><b>".$fila["pagado"]."</b></td>";
                    }
                echo "</tr>";
            }
            echo "</table>";
            echo "<h4>Selecciona la reserva para modificar el estado del pago:</h4>";
            //echo "<form action='confirmarPagos.php' method='POST' class='none'>";
            echo "<select style='border: 3.5px solid; border-radius: 4px;'
                    class='container' name='confirmarpago'>";

            foreach ($resultado as $fila) {
                $seleccionarFecha = $fila["fechainicio"];
                $fechaReserva = date('d.m.y', strtotime($fila["fechainicio"]));
                $horaInicio = date("H:i", strtotime($fila["fechainicio"]));
                $horaFin = date("H:i", strtotime($fila["fechafin"]));
                $nombre = $fila["nombre"];
                $telefono = $fila["telefono"];
                $pagado = $fila["pagado"];

                echo "<option value='$seleccionarFecha'>".
                "$fechaReserva | $horaInicio | $nombre</option><br>";
            }    
            echo "</select>";
            echo "<h4>Selecciona el estado del pago: </h4>";

            echo '<label for="opcion_si">';
            echo '<input type="radio" id="opcion_si" name="opcion" value="SI" checked>';
            echo ' Pagado</label><br>';
            
            echo '<label for="opcion_no">';
            echo '<input type="radio" id="opcion_no" name="opcion" value="NO">';
            echo ' No pagado</label>';

            echo "<div><br><button type='submit' name='submit'>
                    Cambiar estado de reserva seleccionada</button>";
            //echo "</form>";

        } else {
            echo '<label for="mensaje">Mensaje: </label>';
            echo "<h3 style='color: red;'>No se encontraron resultados.</h3>";
        }
    } else {
        echo '<label for="mensaje">Mensaje: </label>';
        echo '<div style="color: red;"><h3>ACCESO DENEGADO: 
            Inicie sesión como Administrador.</h3></div>';
    }
    ?>
    </div>
    <div>
        <a href="../index.php" class="button-link">Volver atrás</a>
    </div>
    </form>
<footer>
    <?php require '../cookies.php'; ?>
</footer>
</body>
</html>