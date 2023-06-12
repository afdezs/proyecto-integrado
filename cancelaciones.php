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

    <div><h3>Listado de Reservas Canceladas</h3></div>
    <form class='form'>
        <!--<label for="control">Listado de reservas:</label><br>-->
    <div>
    <?php
    require '../funcionConectar.php';
    $conexion = conectar();

    $user = $_SESSION["u"];

    if ($user == '999999999') {
        // Consulta SQL con las columnas requeridas y el filtro por el teléfono
        //$consulta = "SELECT * FROM RESERVAS;";
        $consulta = "SELECT CANCELADO.fechainicio, CANCELADO.fechafin, 
                    USUARIOS.nombre, USUARIOS.apellidos, 
                    CANCELADO.telefono, CANCELADO.pagado, CANCELADO.fechacancelacion
                    FROM CANCELADO INNER JOIN 
                    USUARIOS ON CANCELADO.telefono = USUARIOS.telefono
                    WHERE fechainicio >= CURRENT_DATE - INTERVAL 15 DAY
                    ORDER BY fechainicio ASC;";

                //WHERE fechainicio >= CURRENT_DATE

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
                    <th>Datos</th>
                    <th>Fecha Cancelación</th>
                    <th>Pagado</th>
                </tr>";
    
            // Recorremos los resultados y los mostramos en la tabla
            //style='font-size: 12px'
            foreach ($resultado as $fila) {
                echo "<tr>
                    <td>" . date('d.m.y', strtotime($fila["fechainicio"])) .
                      "-" . date("H:i", strtotime($fila["fechainicio"])) . "</td>
                    <td>" . $fila["nombre"]." ".$fila["apellidos"]."<br>Tlf: ".$fila["telefono"]."</td>
                    <td>" . date('d.m.y', strtotime($fila["fechacancelacion"])) . "-" .
                            date("H:i", strtotime($fila["fechacancelacion"])) . "</td>";
                    if ($fila["pagado"] == 'SI'){
                        echo "<td style='color: green;'><b>".$fila["pagado"]."</b>";
                    } else {
                        echo "<td style='color: red;'><b>".$fila["pagado"]."</b></td>";
                    }
                echo "</tr>";
            }
            echo "</table>";
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
        <br><a href="../index.php" class="button-link">Volver atrás</a>
    </div>
    </form>
<footer>
    <?php require '../cookies.php'; ?>
</footer>
</body>
</html>